<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\user_presence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserPresenceController extends Controller
{
    public function today()
    {
        return response()->json([
            'status' => 200,
            'message' => 'Data presensi hari ini berhasil diambil',
            'data' => $this->todayPresence(),
        ], 200);
    }

    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $dateColumn = $this->dateColumn();
        $query = $this->currentUserPresenceQuery();

        if ($request->filled('start_date') && $dateColumn) {
            $query->whereDate($dateColumn, '>=', $request->start_date);
        }

        if ($request->filled('end_date') && $dateColumn) {
            $query->whereDate($dateColumn, '<=', $request->end_date);
        }

        $presences = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Riwayat presensi berhasil diambil',
            'data' => $presences,
        ], 200);
    }

    public function checkIn(Request $request)
    {
        $missingColumns = $this->missingRequiredColumns([
            'date' => $this->dateColumn(),
            'check_in_time' => $this->checkInColumn(),
        ]);

        if ($missingColumns) {
            return $this->missingColumnResponse($missingColumns);
        }

        $validator = Validator::make($request->all(), $this->locationRules());

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $todayPresence = $this->todayPresence();

        if ($todayPresence && $todayPresence->{$this->checkInColumn()}) {
            return response()->json([
                'status' => 400,
                'message' => 'Presensi masuk hari ini sudah dilakukan',
                'data' => $todayPresence,
            ], 400);
        }

        $now = Carbon::now();
        $data = $this->locationPayload($request);
        $data[$this->dateColumn()] = $now->format('Y-m-d');
        $data[$this->checkInColumn()] = $now;

        if ($this->userColumn()) {
            $data[$this->userColumn()] = auth('api')->user()->id;
        }

        if ($this->statusColumn()) {
            $data[$this->statusColumn()] = 'check_in';
        }

        $presence = user_presence::create($data);

        return response()->json([
            'status' => 200,
            'message' => 'Presensi masuk berhasil',
            'data' => $presence,
        ], 200);
    }

    public function checkOut(Request $request)
    {
        $missingColumns = $this->missingRequiredColumns([
            'date' => $this->dateColumn(),
            'check_in_time' => $this->checkInColumn(),
            'check_out_time' => $this->checkOutColumn(),
        ]);

        if ($missingColumns) {
            return $this->missingColumnResponse($missingColumns);
        }

        $validator = Validator::make($request->all(), $this->locationRules());

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $presence = $this->todayPresence();

        if (!$presence || !$presence->{$this->checkInColumn()}) {
            return response()->json([
                'status' => 400,
                'message' => 'Presensi masuk hari ini belum dilakukan',
            ], 400);
        }

        if ($presence->{$this->checkOutColumn()}) {
            return response()->json([
                'status' => 400,
                'message' => 'Presensi keluar hari ini sudah dilakukan',
                'data' => $presence,
            ], 400);
        }

        $data = $this->locationPayload($request, 'out');
        $data[$this->checkOutColumn()] = Carbon::now();

        if ($this->statusColumn()) {
            $data[$this->statusColumn()] = 'check_out';
        }

        $presence->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Presensi keluar berhasil',
            'data' => $presence->fresh(),
        ], 200);
    }

    public function list(Request $request)
    {
        return $this->history($request);
    }

    public function store(Request $request)
    {
        return $this->checkIn($request);
    }

    public function update(Request $request)
    {
        return $this->checkOut($request);
    }

    private function todayPresence()
    {
        $dateColumn = $this->dateColumn();

        if (!$dateColumn) {
            return null;
        }

        return $this->currentUserPresenceQuery()
            ->whereDate($dateColumn, Carbon::now()->format('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();
    }

    private function currentUserPresenceQuery()
    {
        return user_presence::query()
            ->when($this->userColumn(), function ($item, $userColumn) {
                $item->where($userColumn, auth('api')->user()->id);
            });
    }

    private function locationPayload(Request $request, string $type = 'in'): array
    {
        $payload = [];
        $map = [
            'latitude' => $this->firstExistingColumn(["{$type}_latitude", 'latitude']),
            'longitude' => $this->firstExistingColumn(["{$type}_longitude", 'longitude']),
            'address' => $this->firstExistingColumn(["{$type}_address", 'address', 'location']),
            'notes' => $this->firstExistingColumn(["{$type}_notes", 'notes', 'description']),
        ];

        foreach ($map as $requestKey => $column) {
            if ($column && $request->filled($requestKey)) {
                $payload[$column] = $request->{$requestKey};
            }
        }

        return $payload;
    }

    private function locationRules(): array
    {
        return [
            'latitude' => ['sometimes', 'nullable', 'numeric'],
            'longitude' => ['sometimes', 'nullable', 'numeric'],
            'address' => ['sometimes', 'nullable', 'string'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    private function missingRequiredColumns(array $columns): array
    {
        return collect($columns)
            ->filter(fn ($column) => !$column)
            ->keys()
            ->all();
    }

    private function missingColumnResponse(array $columns)
    {
        return response()->json([
            'status' => 500,
            'message' => 'Struktur table user_presences belum sesuai untuk presensi masuk dan keluar',
            'missing_columns' => $columns,
            'required_any_of' => [
                'date' => ['presence_date', 'tanggal', 'date'],
                'check_in_time' => ['check_in_at', 'masuk_at', 'time_in', 'jam_masuk'],
                'check_out_time' => ['check_out_at', 'keluar_at', 'time_out', 'jam_keluar'],
            ],
        ], 500);
    }

    private function validationResponse($validator)
    {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422);
    }

    private function userColumn(): ?string
    {
        return $this->firstExistingColumn(['user_id', 'id_user', 'karyawan_id', 'employee_id']);
    }

    private function dateColumn(): ?string
    {
        return $this->firstExistingColumn(['presence_date', 'tanggal', 'date']);
    }

    private function checkInColumn(): ?string
    {
        return $this->firstExistingColumn(['check_in_at', 'masuk_at', 'time_in', 'jam_masuk']);
    }

    private function checkOutColumn(): ?string
    {
        return $this->firstExistingColumn(['check_out_at', 'keluar_at', 'time_out', 'jam_keluar']);
    }

    private function statusColumn(): ?string
    {
        return $this->firstExistingColumn(['status']);
    }

    private function firstExistingColumn(array $columnNames): ?string
    {
        foreach ($columnNames as $columnName) {
            if (in_array($columnName, $this->columns())) {
                return $columnName;
            }
        }

        return null;
    }

    private function columns(): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = Schema::getColumnListing('user_presences');
        }

        return $columns;
    }
}
