<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\list_karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    private const OFFICE_LATITUDE = -6.25587415828449;

    private const OFFICE_LONGITUDE = 106.81204864232936;

    private const VALID_RADIUS_METERS = 50;

    private const MAX_OVERTIME_HOURS_PER_MONTH = 30;

    private const MAX_ANNUAL_LEAVE_DAYS_PER_YEAR = 12;

    private ?list_karyawan $currentEmployee = null;

    private bool $currentEmployeeLoaded = false;

    public function config()
    {
        return $this->ok([
            'officeLocation' => [
                'latitude' => self::OFFICE_LATITUDE,
                'longitude' => self::OFFICE_LONGITUDE,
            ],
            'validRadiusMeters' => self::VALID_RADIUS_METERS,
            'companyName' => 'PT Toko Mikro',
        ]);
    }

    public function today()
    {
        $record = $this->wfoRecordFor(Carbon::today()->format('Y-m-d'));

        return $this->ok($record ? $this->attendancePayload($record) : null);
    }

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $dateKey = $request->filled('date')
            ? Carbon::parse($request->date)->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $record = $this->wfoRecordFor($dateKey);

        return response()->json([
            'data' => $record ? $this->attendanceDetailPayload($record) : null,
        ], 200);
    }

    public function calendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => ['required', 'date_format:Y-m'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $completedDates = $this->currentUserRecords()
            ->where('work_mode', 'WFO')
            ->whereNotNull('check_in_at')
            ->whereNotNull('check_out_at')
            ->whereBetween('date_key', [
                Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d'),
                Carbon::createFromFormat('Y-m', $request->month)->endOfMonth()->format('Y-m-d'),
            ])
            ->orderBy('date_key')
            ->pluck('date_key')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->values();

        return $this->ok(['completedDates' => $completedDates]);
    }

    public function validateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        return $this->ok($this->locationValidationPayload((float) $request->latitude, (float) $request->longitude));
    }

    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), $this->presenceRules());
        $validator->after(fn ($validator) => $this->validatePresenceFileAndFace($validator, $request));

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $today = Carbon::today();

        if ($today->isWeekend()) {
            return $this->badRequest('Hari weekend tidak bisa digunakan untuk presensi WFO.');
        }

        $dateKey = $today->format('Y-m-d');

        if ($this->wfoRecordFor($dateKey)?->check_in_at) {
            return $this->badRequest('Presensi masuk hari ini sudah dilakukan.');
        }

        if ($this->hasWfaOnDate($dateKey)) {
            return $this->badRequest('Tanggal ini sudah memiliki pengajuan WFA.');
        }

        if ($this->hasActiveLeaveOnDate($dateKey)) {
            return $this->badRequest('Tanggal ini sedang izin atau cuti aktif.');
        }

        $location = $this->locationValidationPayload((float) $request->latitude, (float) $request->longitude);

        if (! $location['valid']) {
            return $this->badRequest('Lokasi berada di luar radius kantor.', ['location' => $location]);
        }

        $record = AttendanceRecord::create(array_merge(
            $this->identityPayload(),
            $this->locationPayload($request, $location),
            [
                'date_key' => $dateKey,
                'work_mode' => 'WFO',
                'check_in_at' => Carbon::now(),
                'check_in_photo' => $this->storeFile($request, 'photo', 'attendance/check-in'),
            ]
        ));

        return $this->ok($this->attendancePayload($record->fresh(), true), 'Presensi masuk berhasil.');
    }

    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), $this->presenceRules());
        $validator->after(fn ($validator) => $this->validatePresenceFileAndFace($validator, $request));

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $record = $this->wfoRecordFor(Carbon::today()->format('Y-m-d'));

        if (! $record || ! $record->check_in_at) {
            return $this->badRequest('Presensi masuk hari ini belum dilakukan.');
        }

        if ($record->check_out_at) {
            return $this->badRequest('Presensi keluar hari ini sudah dilakukan.');
        }

        $location = $this->locationValidationPayload((float) $request->latitude, (float) $request->longitude);

        $record->update(array_merge(
            $this->locationPayload($request, $location),
            [
                'check_out_at' => Carbon::now(),
                'check_out_photo' => $this->storeFile($request, 'photo', 'attendance/check-out'),
            ]
        ));

        return $this->ok($this->attendancePayload($record->fresh(), true), 'Presensi keluar berhasil.');
    }

    public function requestOvertime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'overtimeDate' => ['required', 'date'],
            'startTime' => ['required', 'date_format:H:i'],
            'hours' => ['required', 'numeric'],
            'reason' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $date = Carbon::parse($request->overtimeDate)->startOfDay();
        $hours = (float) $request->hours;

        if ($date->lt(Carbon::today())) {
            return $this->badRequest('Tanggal lembur tidak boleh sebelum hari ini.');
        }

        if ($this->hasActiveOvertimeOnDate($date->format('Y-m-d'))) {
            return $this->badRequest('Sudah ada pengajuan lembur aktif di tanggal tersebut.');
        }

        if ($date->isWeekend()) {
            if ($request->startTime < '08:00' || $hours < 2 || $hours > 8) {
                return $this->badRequest('Lembur weekend dimulai minimal jam 08:00 dengan durasi 2 sampai 8 jam.');
            }
        } elseif ($request->startTime < '17:00' || ! in_array($hours, [2.0, 3.0], true)) {
            return $this->badRequest('Lembur weekday dimulai minimal jam 17:00 dengan durasi 2 atau 3 jam.');
        }

        $remaining = $this->remainingOvertimeHours($date->format('Y-m'));

        if ($hours > $remaining) {
            return $this->badRequest('Jatah lembur bulanan tidak mencukupi.', ['remainingHours' => $remaining]);
        }

        $record = AttendanceRecord::create(array_merge($this->identityPayload(), [
            'overtime_requested_at' => Carbon::now(),
            'overtime_date' => $date->format('Y-m-d'),
            'overtime_start_time' => $request->startTime,
            'overtime_hours' => $hours,
            'overtime_reason' => $request->reason,
            'approval_status' => 'pending',
        ]));

        return $this->ok($this->approvalPayload($record), 'Pengajuan lembur berhasil dibuat.');
    }

    public function remainingOvertime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => ['required', 'date_format:Y-m'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        return $this->ok([
            'remainingHours' => $this->remainingOvertimeHours($request->month),
            'maxHoursPerMonth' => self::MAX_OVERTIME_HOURS_PER_MONTH,
        ]);
    }

    public function finishOvertime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'overtimeRequestId' => ['required', 'integer'],
            'finishedAt' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $record = $this->currentUserRecords()
            ->whereKey($request->overtimeRequestId)
            ->whereNotNull('overtime_requested_at')
            ->first();

        if (! $record || $record->end_overtime_at) {
            return $this->badRequest('Pengajuan lembur aktif tidak ditemukan.');
        }

        $startedAt = Carbon::parse($record->overtime_date->format('Y-m-d').' '.$record->overtime_start_time);
        $finishedAt = Carbon::parse($request->finishedAt);
        $minimumFinishedAt = $startedAt->copy()->addMinutes((int) ((float) $record->overtime_hours * 60));

        if ($finishedAt->lt($startedAt)) {
            return $this->badRequest('Waktu selesai lembur tidak boleh sebelum jam mulai.');
        }

        if ($finishedAt->lt($minimumFinishedAt)) {
            return $this->badRequest('Waktu selesai lembur belum memenuhi durasi pengajuan.');
        }

        $record->update([
            'end_overtime_at' => $finishedAt,
            'overtime_actual_hours' => round($startedAt->diffInMinutes($finishedAt) / 60, 2),
        ]);

        return $this->ok($this->approvalPayload($record->fresh()), 'Lembur berhasil diselesaikan.');
    }

    public function requestWfa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wfaDate' => ['required', 'date'],
            'reason' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $date = Carbon::parse($request->wfaDate)->startOfDay();
        $dateKey = $date->format('Y-m-d');

        if ($date->lt(Carbon::today())) {
            return $this->badRequest('Tanggal WFA tidak boleh sebelum hari ini.');
        }

        if ($date->isWeekend()) {
            return $this->badRequest('Tanggal WFA tidak boleh weekend.');
        }

        if ($this->currentUserRecords()->whereBetween('wfa_date', [
            $date->copy()->startOfWeek()->format('Y-m-d'),
            $date->copy()->endOfWeek()->format('Y-m-d'),
        ])->whereIn('approval_status', ['pending', 'approved'])->exists()) {
            return $this->badRequest('WFA maksimal 1 hari dalam 1 minggu.');
        }

        if ($this->wfoRecordFor($dateKey)?->check_in_at) {
            return $this->badRequest('Tanggal ini sudah memiliki presensi WFO.');
        }

        if ($this->hasActiveLeaveOnDate($dateKey) || $this->hasWfaOnDate($dateKey)) {
            return $this->badRequest('Tanggal WFA bentrok dengan pengajuan lain.');
        }

        $record = AttendanceRecord::create(array_merge($this->identityPayload(), [
            'work_mode' => 'WFA',
            'wfa_requested_at' => Carbon::now(),
            'wfa_date' => $dateKey,
            'wfa_reason' => $request->reason,
            'approval_status' => 'pending',
        ]));

        return $this->ok($this->approvalPayload($record), 'Pengajuan WFA berhasil dibuat.');
    }

    public function requestLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaveType' => ['required', 'in:annual,special,permission,sick'],
            'specialLeaveType' => ['nullable', 'string'],
            'leaveDate' => ['required', 'date'],
            'leaveEndDate' => ['required', 'date', 'after_or_equal:leaveDate'],
            'reason' => ['required', 'string'],
            'attachment' => ['sometimes', 'nullable'],
        ]);
        $validator->after(fn ($validator) => $this->validateLeave($validator, $request));

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $record = AttendanceRecord::create(array_merge($this->identityPayload(), [
            'leave_at' => Carbon::now(),
            'leave_approval_status' => 'pending',
            'leave_type' => $request->leaveType,
            'special_leave_type' => $request->specialLeaveType,
            'leave_date' => Carbon::parse($request->leaveDate)->format('Y-m-d'),
            'leave_end_date' => Carbon::parse($request->leaveEndDate)->format('Y-m-d'),
            'leave_reason' => $request->reason,
            'leave_attachment' => $this->storeFile($request, 'attachment', 'attendance/leave'),
            'approval_status' => 'pending',
        ]));

        return $this->ok($this->approvalPayload($record), 'Pengajuan izin/cuti berhasil dibuat.');
    }

    public function remainingAnnualLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => ['required', 'integer', 'digits:4'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $used = $this->usedAnnualLeaveDays((int) $request->year);

        return $this->ok([
            'remainingDays' => max(0, self::MAX_ANNUAL_LEAVE_DAYS_PER_YEAR - $used),
            'maxDaysPerYear' => self::MAX_ANNUAL_LEAVE_DAYS_PER_YEAR,
        ]);
    }

    public function approvals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['sometimes', 'nullable', 'in:overtime,wfa,leave,permission,sick'],
            'leaveType' => ['sometimes', 'nullable', 'in:annual,special,permission,sick'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $records = $this->currentUserRecords()
            ->where(function ($query) {
                $query->whereNotNull('overtime_requested_at')
                    ->orWhereNotNull('wfa_requested_at')
                    ->orWhereNotNull('leave_at');
            })
            ->latest('updated_at')
            ->get()
            ->filter(fn ($record) => $this->matchesApprovalFilter($record, $request))
            ->map(fn ($record) => $this->approvalPayload($record))
            ->values();

        return $this->ok($records);
    }

    public function updateApprovalStatus(Request $request, AttendanceRecord $attendanceRecord)
    {
        if ((int) $attendanceRecord->user_id !== (int) auth('api')->id()) {
            return response()->json([
                'status' => 404,
                'message' => 'Data approval tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:pending,approved,rejected'],
            'note' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $attendanceRecord->update([
            'approval_status' => $request->status,
            'leave_approval_status' => $attendanceRecord->leave_at ? $request->status : $attendanceRecord->leave_approval_status,
            'approval_note' => $request->note,
            'approved_by' => auth('api')->id(),
            'approved_at' => Carbon::now(),
        ]);

        return $this->ok($this->approvalPayload($attendanceRecord->fresh()), 'Status approval berhasil diperbarui.');
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required'],
            'type' => ['sometimes', 'nullable', 'in:presence,attachment'],
        ]);
        $validator->after(function ($validator) use ($request) {
            if (! $request->hasFile('file') && ! $request->filled('file')) {
                $validator->errors()->add('file', 'File wajib diisi.');
            }
        });

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $path = $this->storeFile($request, 'file', 'attendance/files');

        return $this->ok([
            'uri' => $path,
            'url' => $path,
            'name' => $request->file('file')?->getClientOriginalName(),
            'mimeType' => $request->file('file')?->getMimeType(),
            'size' => $request->file('file')?->getSize(),
        ], 'File berhasil diupload.');
    }

    public function photo(AttendanceRecord $attendanceRecord, string $type)
    {
        if ((int) $attendanceRecord->user_id !== (int) auth('api')->id()) {
            return response()->json([
                'status' => 404,
                'message' => 'Foto presensi tidak ditemukan.',
            ], 404);
        }

        $path = $this->normalizeNullablePublicFilePath(match ($type) {
            'check-in' => $attendanceRecord->check_in_photo,
            'check-out' => $attendanceRecord->check_out_photo,
            default => null,
        });

        if (! $path) {
            return response()->json([
                'status' => 404,
                'message' => 'Foto presensi tidak ditemukan.',
            ], 404);
        }

        $disk = $this->storedAttendanceFileDisk($path);

        if (! $disk) {
            return response()->json([
                'status' => 404,
                'message' => 'Foto presensi tidak ditemukan.',
            ], 404);
        }

        return response()->file(Storage::disk($disk)->path($path), [
            'Content-Type' => Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function validateLeave($validator, Request $request): void
    {
        $start = Carbon::parse($request->leaveDate)->startOfDay();
        $end = Carbon::parse($request->leaveEndDate)->startOfDay();
        $leaveType = $request->leaveType;
        $days = $this->workDaysBetween($start, $end, $leaveType === 'sick');

        if ($leaveType !== 'sick' && $this->periodHasWeekend($start, $end)) {
            $validator->errors()->add('leaveDate', 'Weekend tidak boleh dipilih untuk izin/cuti selain sakit.');
        }

        if ($this->hasDateConflict($start, $end)) {
            $validator->errors()->add('leaveDate', 'Rentang tanggal bentrok dengan WFO, WFA, izin, cuti, atau lembur.');
        }

        if ($leaveType === 'permission' && $start->format('Y-m') !== Carbon::now()->format('Y-m')) {
            $validator->errors()->add('leaveDate', 'Izin hanya bisa diajukan untuk bulan berjalan.');
        }

        if ($leaveType === 'annual') {
            if ($days > 3) {
                $validator->errors()->add('leaveEndDate', 'Cuti tahunan maksimal 3 hari kerja per pengajuan.');
            }

            if ($this->usedAnnualLeaveDays((int) $start->year) + $days > self::MAX_ANNUAL_LEAVE_DAYS_PER_YEAR) {
                $validator->errors()->add('leaveDate', 'Sisa cuti tahunan tidak mencukupi.');
            }
        }

        if ($leaveType === 'special') {
            if (! $request->filled('specialLeaveType')) {
                $validator->errors()->add('specialLeaveType', 'Jenis cuti khusus wajib diisi.');
            }

            $max = $this->specialLeaveMaxDays($request->specialLeaveType);

            if ($max !== null && $days > $max) {
                $validator->errors()->add('leaveEndDate', 'Durasi cuti khusus melebihi batas jenis cuti yang dipilih.');
            }
        }
    }

    private function validatePresenceFileAndFace($validator, Request $request): void
    {
        if (! $request->hasFile('photo') && ! $request->filled('photo')) {
            $validator->errors()->add('photo', 'Foto presensi wajib diisi.');
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            if (! $file->isValid() || ! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true) || $file->getSize() > 5 * 1024 * 1024) {
                $validator->errors()->add('photo', 'Foto presensi harus berupa JPG, PNG, atau WEBP maksimal 5MB.');
            }
        }

        if (! filter_var(data_get($request->input('faceVerification'), 'matched'), FILTER_VALIDATE_BOOL)) {
            $validator->errors()->add('faceVerification.matched', 'Verifikasi wajah wajib valid dan cocok dengan user.');
        }
    }

    private function presenceRules(): array
    {
        return [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['required', 'numeric', 'min:0'],
            'distance' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string'],
            'photo' => ['required'],
            'faceVerification' => ['required', 'array'],
            'faceVerification.matched' => ['required'],
            'faceVerification.score' => ['nullable', 'numeric'],
        ];
    }

    private function identityPayload(): array
    {
        $user = auth('api')->user();
        $employee = $this->currentEmployee();
        $company = $employee?->perusahaan;

        return [
            'user_id' => $user->id,
            'employee_id' => $employee?->id,
            'company_id' => $employee?->id_list_perusahaan,
            'employee_name' => $employee?->nama_karyawan ?? $user->nama_lengkap ?? $user->name,
            'employee_nip' => $employee?->nip ?? $employee?->nik ?? $user->nik ?? null,
            'scope_key' => $employee?->nik ?? $user->nik ?? (string) $user->id,
            'company' => $company?->nama_perusahaan ?? $company?->name ?? null,
            'department' => $employee?->departement ?? $employee?->divisi ?? null,
            'position' => $employee?->jabatan ?? null,
        ];
    }

    private function currentEmployee(): ?list_karyawan
    {
        if ($this->currentEmployeeLoaded) {
            return $this->currentEmployee;
        }

        $this->currentEmployeeLoaded = true;
        $user = auth('api')->user();

        if (! $user) {
            return null;
        }

        $this->currentEmployee = list_karyawan::with('perusahaan')
            ->where(function ($query) use ($user) {
                if (isset($user->nik)) {
                    $query->where('nik', $user->nik);
                }

                if (isset($user->nohp)) {
                    $query->orWhere('whatsapp', $user->nohp);
                }
            })
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->latest('id')
            ->first();

        return $this->currentEmployee;
    }

    private function currentUserRecords()
    {
        return AttendanceRecord::query()->where('user_id', auth('api')->id());
    }

    private function wfoRecordFor(string $dateKey): ?AttendanceRecord
    {
        return $this->currentUserRecords()
            ->where('work_mode', 'WFO')
            ->whereDate('date_key', $dateKey)
            ->latest('id')
            ->first();
    }

    private function hasWfaOnDate(string $dateKey): bool
    {
        return $this->currentUserRecords()
            ->whereDate('wfa_date', $dateKey)
            ->whereIn('approval_status', ['pending', 'approved'])
            ->exists();
    }

    private function hasActiveLeaveOnDate(string $dateKey): bool
    {
        return $this->currentUserRecords()
            ->whereNotNull('leave_at')
            ->whereIn('leave_approval_status', ['pending', 'approved'])
            ->whereDate('leave_date', '<=', $dateKey)
            ->whereDate('leave_end_date', '>=', $dateKey)
            ->exists();
    }

    private function hasActiveOvertimeOnDate(string $dateKey): bool
    {
        return $this->currentUserRecords()
            ->whereDate('overtime_date', $dateKey)
            ->whereIn('approval_status', ['pending', 'approved'])
            ->exists();
    }

    private function hasDateConflict(Carbon $start, Carbon $end): bool
    {
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $dateKey = $date->format('Y-m-d');

            if ($this->wfoRecordFor($dateKey)?->check_in_at || $this->hasWfaOnDate($dateKey) || $this->hasActiveLeaveOnDate($dateKey) || $this->hasActiveOvertimeOnDate($dateKey)) {
                return true;
            }
        }

        return false;
    }

    private function remainingOvertimeHours(string $month): float
    {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $used = (float) $this->currentUserRecords()
            ->whereBetween('overtime_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->whereIn('approval_status', ['pending', 'approved'])
            ->sum('overtime_hours');

        return max(0, self::MAX_OVERTIME_HOURS_PER_MONTH - $used);
    }

    private function usedAnnualLeaveDays(int $year): int
    {
        return $this->currentUserRecords()
            ->where('leave_type', 'annual')
            ->whereIn('leave_approval_status', ['pending', 'approved'])
            ->whereYear('leave_date', $year)
            ->get()
            ->sum(fn ($record) => $this->workDaysBetween($record->leave_date, $record->leave_end_date));
    }

    private function workDaysBetween(Carbon $start, Carbon $end, bool $includeWeekend = false): int
    {
        $days = 0;

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($includeWeekend || ! $date->isWeekend()) {
                $days++;
            }
        }

        return $days;
    }

    private function periodHasWeekend(Carbon $start, Carbon $end): bool
    {
        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekend()) {
                return true;
            }
        }

        return false;
    }

    private function specialLeaveMaxDays(?string $type): ?int
    {
        return [
            'menstrual' => 2,
            'marriage' => 3,
            'child_marriage' => 2,
            'child_circumcision' => 2,
            'child_baptism' => 2,
            'wife_birth_or_miscarriage' => 2,
            'family_passed_away' => 2,
            'household_family_passed_away' => 1,
        ][$type] ?? null;
    }

    private function locationPayload(Request $request, array $location): array
    {
        return [
            'last_latitude' => $request->latitude,
            'last_longitude' => $request->longitude,
            'last_accuracy' => $request->accuracy,
            'last_distance' => $request->filled('distance') ? $request->distance : $location['distance'],
            'last_address' => $request->address,
        ];
    }

    private function locationValidationPayload(float $latitude, float $longitude): array
    {
        $distance = $this->distanceInMeters($latitude, $longitude, self::OFFICE_LATITUDE, self::OFFICE_LONGITUDE);

        return [
            'valid' => $distance <= self::VALID_RADIUS_METERS,
            'distance' => round($distance, 2),
            'validRadiusMeters' => self::VALID_RADIUS_METERS,
            'officeLocation' => [
                'latitude' => self::OFFICE_LATITUDE,
                'longitude' => self::OFFICE_LONGITUDE,
            ],
        ];
    }

    private function distanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function storeFile(Request $request, string $key, string $directory): ?string
    {
        if ($request->hasFile($key)) {
            return $request->file($key)->store($directory, $this->attendanceFileDisk($directory));
        }

        if ($request->filled($key) && is_string($request->input($key))) {
            $base64Path = $this->storeBase64Image($request->input($key), $directory);

            if ($base64Path) {
                return $base64Path;
            }
        }

        return $request->filled($key) && is_string($request->input($key))
            ? $this->normalizePublicFilePath($request->input($key))
            : null;
    }

    private function storeBase64Image(string $value, string $directory): ?string
    {
        if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/', $value, $matches)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            return null;
        }

        $path = trim($directory, '/').'/'.bin2hex(random_bytes(16)).'.'.$extension;

        return Storage::disk($this->attendanceFileDisk($directory))->put($path, $binary) ? $path : null;
    }

    private function attendancePayload(AttendanceRecord $record, bool $withDetail = false): array
    {
        $payload = [
            'id' => $record->id,
            'dateKey' => $record->date_key?->format('Y-m-d'),
            'workMode' => $record->work_mode,
            'checkInAt' => $record->check_in_at?->toISOString(),
            'checkOutAt' => $record->check_out_at?->toISOString(),
            'lastLatitude' => $record->last_latitude ? (float) $record->last_latitude : null,
            'lastLongitude' => $record->last_longitude ? (float) $record->last_longitude : null,
            'lastAccuracy' => $record->last_accuracy ? (float) $record->last_accuracy : null,
            'lastDistance' => $record->last_distance ? (float) $record->last_distance : null,
            'lastAddress' => $record->last_address,
        ];

        if ($withDetail) {
            $checkInPhoto = $this->normalizeNullablePublicFilePath($record->check_in_photo);
            $checkOutPhoto = $this->normalizeNullablePublicFilePath($record->check_out_photo);

            $payload += [
                'employeeName' => $record->employee_name,
                'employeeNip' => $record->employee_nip,
                'checkInPhoto' => $this->attendancePhotoUrl($record, 'check-in', $checkInPhoto),
                'checkOutPhoto' => $this->attendancePhotoUrl($record, 'check-out', $checkOutPhoto),
                'valid' => $record->check_in_at !== null,
            ];
        }

        return $payload;
    }

    private function attendanceDetailPayload(AttendanceRecord $record): array
    {
        $checkInPhoto = $this->normalizeNullablePublicFilePath($record->check_in_photo);
        $checkOutPhoto = $this->normalizeNullablePublicFilePath($record->check_out_photo);
        $checkInPhotoUrl = $this->attendancePhotoUrl($record, 'check-in', $checkInPhoto);
        $checkOutPhotoUrl = $this->attendancePhotoUrl($record, 'check-out', $checkOutPhoto);

        return [
            'dateKey' => $record->date_key?->format('Y-m-d'),
            'checkInAt' => $record->check_in_at?->toISOString(),
            'checkOutAt' => $record->check_out_at?->toISOString(),
            'checkInPhoto' => $checkInPhotoUrl,
            'checkOutPhoto' => $checkOutPhotoUrl,
            'check_in_photo' => $checkInPhoto,
            'check_in_photo_url' => $checkInPhotoUrl,
            'photo_in' => $checkInPhoto,
            'check_out_photo' => $checkOutPhoto,
            'check_out_photo_url' => $checkOutPhotoUrl,
            'photo_out' => $checkOutPhoto,
            'checkIn' => [
                'photo' => $checkInPhoto,
                'photoUrl' => $checkInPhotoUrl,
                'photo_url' => $checkInPhotoUrl,
            ],
            'checkOut' => [
                'photo' => $checkOutPhoto,
                'photoUrl' => $checkOutPhotoUrl,
                'photo_url' => $checkOutPhotoUrl,
            ],
            'lastLatitude' => $record->last_latitude !== null ? (float) $record->last_latitude : null,
            'lastLongitude' => $record->last_longitude !== null ? (float) $record->last_longitude : null,
            'lastAccuracy' => $record->last_accuracy !== null ? (float) $record->last_accuracy : null,
            'lastDistance' => $record->last_distance !== null ? (float) $record->last_distance : null,
            'lastAddress' => $record->last_address,
        ];
    }

    private function attendancePhotoUrl(AttendanceRecord $record, string $type, ?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return route('attendance.photo', [
            'attendanceRecord' => $record->id,
            'type' => $type,
        ], false);
    }

    private function attendanceFileDisk(string $directory): string
    {
        return str_starts_with(trim($directory, '/'), 'attendance/') ? 'local' : 'public';
    }

    private function storedAttendanceFileDisk(string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }

    private function normalizeNullablePublicFilePath(?string $path): ?string
    {
        return $path ? $this->normalizePublicFilePath($path) : null;
    }

    private function normalizePublicFilePath(string $path): string
    {
        $path = trim($path);
        $storagePrefix = '/storage/';
        $storagePosition = strpos($path, $storagePrefix);

        if ($storagePosition !== false) {
            return ltrim(substr($path, $storagePosition + strlen($storagePrefix)), '/');
        }

        return ltrim($path, '/');
    }

    private function approvalPayload(AttendanceRecord $record): array
    {
        $type = $record->overtime_requested_at ? 'overtime' : ($record->wfa_requested_at ? 'wfa' : 'leave');

        return [
            'id' => $record->id,
            'type' => $type,
            'leaveType' => $record->leave_type,
            'specialLeaveType' => $record->special_leave_type,
            'title' => $this->approvalTitle($record, $type),
            'status' => $record->approval_status ?? $record->leave_approval_status ?? 'pending',
            'requestedAt' => ($record->overtime_requested_at ?? $record->wfa_requested_at ?? $record->leave_at)?->toISOString(),
            'lastUpdatedAt' => $record->updated_at?->toISOString(),
            'dateLabel' => $this->approvalDateLabel($record, $type),
            'description' => $record->overtime_reason ?? $record->wfa_reason ?? $record->leave_reason,
            'employeeName' => $record->employee_name,
            'employeeNip' => $record->employee_nip,
        ];
    }

    private function approvalTitle(AttendanceRecord $record, string $type): string
    {
        if ($type === 'overtime') {
            return 'Pengajuan Lembur';
        }

        if ($type === 'wfa') {
            return 'Pengajuan WFA';
        }

        return match ($record->leave_type) {
            'annual' => 'Cuti Tahunan',
            'special' => 'Cuti Khusus',
            'permission' => 'Izin',
            'sick' => 'Sakit',
            default => 'Pengajuan Cuti',
        };
    }

    private function approvalDateLabel(AttendanceRecord $record, string $type): ?string
    {
        if ($type === 'overtime') {
            return $record->overtime_date?->format('Y-m-d');
        }

        if ($type === 'wfa') {
            return $record->wfa_date?->format('Y-m-d');
        }

        if (! $record->leave_date) {
            return null;
        }

        if ($record->leave_end_date && ! $record->leave_date->isSameDay($record->leave_end_date)) {
            return $record->leave_date->format('Y-m-d').' - '.$record->leave_end_date->format('Y-m-d');
        }

        return $record->leave_date->format('Y-m-d');
    }

    private function matchesApprovalFilter(AttendanceRecord $record, Request $request): bool
    {
        if (! $request->filled('type') && ! $request->filled('leaveType')) {
            return true;
        }

        $type = $record->overtime_requested_at ? 'overtime' : ($record->wfa_requested_at ? 'wfa' : 'leave');

        if ($request->type === 'permission' || $request->type === 'sick') {
            return $type === 'leave' && $record->leave_type === $request->type;
        }

        if ($request->type === 'leave') {
            return $type === 'leave' && in_array($record->leave_type, ['annual', 'special'], true);
        }

        if ($request->filled('type') && $request->type !== $type) {
            return false;
        }

        return ! $request->filled('leaveType') || $record->leave_type === $request->leaveType;
    }

    private function ok($data, string $message = 'Data berhasil diambil.')
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    private function badRequest(string $message, array $extra = [])
    {
        return response()->json(array_merge([
            'status' => 400,
            'message' => $message,
        ], $extra), 400);
    }

    private function validationResponse($validator)
    {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422);
    }
}
