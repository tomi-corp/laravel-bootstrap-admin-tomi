<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\bank;
use App\Models\CompanyReferral;
use App\Models\Instansi;
use App\Models\ListPerusahaan;
use App\Models\Referral;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

/**
 * Kelola referral: data KTP + rekening bank, akun login (admin instansi Referral),
 * dan perusahaan yang direferensikan (posisi Referal 1 / Referal 2, maksimal dua referral per perusahaan).
 * Hanya untuk admin instansi Internal karena berisi data pribadi.
 */
class ReferralController extends Controller implements HasMiddleware
{
    /** Jenis file => [kolom, folder penyimpanan]. */
    private const FILES = [
        'ktp' => ['id_card_file', 'referrals/ktp'],
        'bank-book' => ['bank_book_file', 'referrals/bank-book'],
    ];

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                abort_unless(auth('admin')->user()?->instansi?->code === 'internal', 403, 'Menu referral hanya untuk admin internal.');

                return $next($request);
            }),
        ];
    }

    public function index()
    {
        return view('internal.referral.index', [
            'banks' => bank::query()->orderBy('code_bank')->get(['id', 'code_bank']),
            'companies' => ListPerusahaan::query()->where('status', 'active')->orderBy('nama')->get(['id', 'nama']),
            'positions' => CompanyReferral::POSITIONS,
        ]);
    }

    public function table()
    {
        $query = Referral::query()
            ->select('referrals.*')
            ->with(['admin:id,username', 'bank:id,code_bank'])
            ->withCount('companyReferrals');

        $fileBadge = function (Referral $row, string $type) {
            [$column] = self::FILES[$type];
            if (! $row->{$column}) {
                return '<span class="badge badge-warning">BELUM ADA</span>';
            }

            return '<a href="'.route('internal.referral.file', [$row->id, $type]).'" target="_blank" class="badge badge-success">LIHAT</a>';
        };

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('name', fn ($row) => '<b>'.e(Str::upper($row->name)).'</b>')
            ->editColumn('national_id_number', fn ($row) => e($row->national_id_number))
            ->editColumn('phone_number', fn ($row) => e($row->phone_number ?? '-'))
            ->editColumn('email', fn ($row) => e(Str::lower((string) ($row->email ?? '-'))))
            ->addColumn('bank_account', fn ($row) => e(Str::upper(($row->bank?->code_bank ?? '-').' · '.($row->bank_account_number ?? '-')))
                .'<br><small>'.e(Str::upper($row->bank_account_name ?? '-')).'</small>')
            ->addColumn('ktp', fn ($row) => $fileBadge($row, 'ktp'))
            ->addColumn('bank_book', fn ($row) => $fileBadge($row, 'bank-book'))
            ->addColumn('companies', fn ($row) => '<button class="btn btn-sm btn-outline-primary" type="button" onclick="openCompanies('.$row->id.')">'
                .number_format($row->company_referrals_count, 0, ',', '.').' PERUSAHAAN</button>')
            ->addColumn('account', fn ($row) => e($row->admin?->username ?? '-'))
            ->addColumn('status', fn ($row) => $row->is_active
                ? '<span class="badge badge-success">AKTIF</span>'
                : '<span class="badge badge-secondary">TIDAK AKTIF</span>')
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-1" type="button" title="Ubah" onclick="editReferral('.$row->id.')"><i class="mdi mdi-pencil"></i></button>'
                .'<button class="btn btn-sm btn-danger" type="button" title="Hapus" onclick="deleteReferral('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>')
            ->filterColumn('account', fn ($q, $keyword) => $q->whereHas('admin', fn ($a) => $a->where('username', 'like', "%{$keyword}%")))
            ->rawColumns(['name', 'bank_account', 'ktp', 'bank_book', 'companies', 'status', 'action'])
            ->toJson();
    }

    public function show(Request $req)
    {
        $referral = Referral::with('admin:id,username,email')->findOrFail($req->id);

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $referral->id,
                'national_id_number' => $referral->national_id_number,
                'name' => $referral->name,
                'birth_place' => $referral->birth_place,
                'birth_date' => $referral->birth_date?->format('Y-m-d'),
                'gender' => $referral->gender,
                'address' => $referral->address,
                'phone_number' => $referral->phone_number,
                'email' => $referral->email,
                'bank_id' => $referral->bank_id,
                'bank_account_number' => $referral->bank_account_number,
                'bank_account_name' => $referral->bank_account_name,
                'notes' => $referral->notes,
                'is_active' => $referral->is_active,
                'has_ktp' => (bool) $referral->id_card_file,
                'has_bank_book' => (bool) $referral->bank_book_file,
                'username' => $referral->admin?->username,
                'account_email' => $referral->admin?->email,
            ],
        ], 200);
    }

    public function store(Request $req)
    {
        $req->validate($this->rules(), $this->messages());

        $stored = [];
        try {
            DB::transaction(function () use ($req, &$stored) {
                $admin = new Admin;
                $this->fillAdmin($admin, $req);
                $admin->password = Hash::make($req->password);
                $admin->save();

                $referral = new Referral;
                $referral->admin_id = $admin->id;
                $this->fillReferral($referral, $req);
                $stored = $this->storeFiles($referral, $req);
                $referral->save();
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }

        return response()->json(['status' => 200, 'message' => 'Referral berhasil ditambahkan.'], 200);
    }

    public function update(Request $req)
    {
        $referral = Referral::with('admin')->findOrFail($req->id);
        $req->validate($this->rules($referral), $this->messages());

        $stored = [];
        $replaced = [];
        try {
            DB::transaction(function () use ($req, $referral, &$stored, &$replaced) {
                $admin = $referral->admin ?? new Admin;
                $this->fillAdmin($admin, $req);
                if ($req->filled('password')) {
                    $admin->password = Hash::make($req->password);
                }
                $admin->save();

                $replaced = array_filter([
                    $req->hasFile('id_card_file') ? $referral->id_card_file : null,
                    $req->hasFile('bank_book_file') ? $referral->bank_book_file : null,
                ]);

                $referral->admin_id = $admin->id;
                $this->fillReferral($referral, $req);
                $stored = $this->storeFiles($referral, $req);
                $referral->save();
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }

        // File lama baru dihapus setelah data tersimpan.
        $this->deleteFiles($replaced);

        return response()->json(['status' => 200, 'message' => 'Referral berhasil diperbarui.'], 200);
    }

    public function destroy(Request $req)
    {
        $referral = Referral::with('admin')->findOrFail($req->id);
        abort_if($referral->admin_id && $referral->admin_id === auth('admin')->id(), 422, 'Tidak bisa menghapus akun yang sedang dipakai.');

        DB::transaction(function () use ($referral) {
            $referral->admin?->delete();
            $referral->delete();
        });

        return response()->json(['status' => 200, 'message' => 'Referral berhasil dihapus.'], 200);
    }

    public function file(Referral $referral, string $type)
    {
        [$column] = self::FILES[$type];
        $path = $referral->{$column};
        $disk = Storage::disk('local');
        abort_unless($path && $disk->exists($path), 404, 'File tidak ditemukan.');

        return response($disk->get($path), 200, [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }

    public function companies(Request $req)
    {
        $referral = Referral::findOrFail($req->id);

        $items = $referral->companyReferrals()
            ->with('company:id,nama')
            ->orderBy('company_id')
            ->get()
            ->map(fn (CompanyReferral $row) => [
                'id' => $row->id,
                'company' => Str::upper($row->company?->nama ?? '-'),
                'position' => Str::upper(CompanyReferral::POSITIONS[$row->position] ?? '-'),
            ]);

        return response()->json([
            'status' => 200,
            'data' => ['referral' => Str::upper($referral->name), 'items' => $items],
        ], 200);
    }

    /**
     * Tautkan referral ke perusahaan sebagai Referal 1 / Referal 2.
     * Satu perusahaan maksimal dua referral (satu per posisi); sama seperti pilihan di form Perusahaan.
     */
    public function attachCompany(Request $req)
    {
        $req->validate([
            'referral_id' => 'required|exists:referrals,id',
            'company_id' => 'required|exists:list_perusahaans,id',
            'position' => ['required', Rule::in(array_keys(CompanyReferral::POSITIONS))],
        ], [
            'company_id.required' => 'Perusahaan wajib dipilih.',
            'position.required' => 'Posisi referal wajib dipilih.',
        ]);

        $companyReferrals = CompanyReferral::where('company_id', $req->company_id);
        if ((clone $companyReferrals)->where('referral_id', $req->referral_id)->exists()) {
            throw ValidationException::withMessages(['company_id' => 'Referral ini sudah ditautkan ke perusahaan tersebut.']);
        }
        if ((clone $companyReferrals)->where('position', $req->position)->exists()) {
            $label = CompanyReferral::POSITIONS[$req->position];
            throw ValidationException::withMessages(['position' => "{$label} perusahaan ini sudah dipakai referral lain."]);
        }

        DB::transaction(function () use ($req) {
            // Baris terhapus (soft delete) dibersihkan agar tidak bentrok dengan unique index.
            CompanyReferral::onlyTrashed()
                ->where('company_id', $req->company_id)
                ->where(fn ($q) => $q->where('referral_id', $req->referral_id)->orWhere('position', $req->position))
                ->forceDelete();

            CompanyReferral::create($req->only(['company_id', 'referral_id', 'position']));
        });

        return response()->json(['status' => 200, 'message' => 'Perusahaan berhasil ditautkan.'], 200);
    }

    public function detachCompany(Request $req)
    {
        CompanyReferral::findOrFail($req->id)->forceDelete();

        return response()->json(['status' => 200, 'message' => 'Tautan perusahaan berhasil dihapus.'], 200);
    }

    /** Password & file wajib saat tambah; saat ubah boleh dikosongkan (data lama tetap dipakai). */
    private function rules(?Referral $referral = null): array
    {
        $password = Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised();
        $file = 'file|mimes:jpg,jpeg,png,pdf|max:5120';
        $adminId = $referral?->admin_id;

        return [
            'national_id_number' => ['required', 'digits:16', Rule::unique('referrals', 'national_id_number')->ignore($referral?->id)->whereNull('deleted_at')],
            'name' => 'required|string|max:255',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'bank_id' => 'required|exists:banks,id',
            'bank_account_number' => 'required|string|max:100',
            'bank_account_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'id_card_file' => ($referral?->id_card_file ? 'nullable|' : 'required|').$file,
            'bank_book_file' => ($referral?->bank_book_file ? 'nullable|' : 'required|').$file,
            'username' => ['required', 'string', 'max:255', Rule::unique('admins', 'username')->ignore($adminId)->whereNull('deleted_at')],
            'account_email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($adminId)->whereNull('deleted_at')],
            'password' => $adminId ? ['nullable', 'confirmed', $password] : ['required', 'confirmed', $password],
        ];
    }

    private function messages(): array
    {
        return [
            'national_id_number.required' => 'NIK wajib diisi.',
            'national_id_number.digits' => 'NIK harus 16 digit angka.',
            'national_id_number.unique' => 'NIK sudah terdaftar sebagai referral.',
            'name.required' => 'Nama wajib diisi.',
            'bank_id.required' => 'Bank wajib dipilih.',
            'bank_account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'id_card_file.required' => 'File KTP wajib diunggah.',
            'id_card_file.mimes' => 'File KTP harus berformat JPG, PNG, atau PDF.',
            'id_card_file.max' => 'Ukuran file KTP maksimal 5 MB.',
            'bank_book_file.required' => 'File buku rekening wajib diunggah.',
            'bank_book_file.mimes' => 'File buku rekening harus berformat JPG, PNG, atau PDF.',
            'bank_book_file.max' => 'Ukuran file buku rekening maksimal 5 MB.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai admin lain.',
            'account_email.required' => 'Email akun wajib diisi.',
            'account_email.email' => 'Format email akun tidak valid.',
            'account_email.unique' => 'Email akun sudah dipakai admin lain.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }

    private function fillAdmin(Admin $admin, Request $req): void
    {
        $instansi = Instansi::where('code', 'referral')->firstOrFail();
        $role = Role::where('instansi_id', $instansi->id)->where('code', 'referral')->firstOrFail();

        $admin->name = $req->name;
        $admin->username = $req->username;
        $admin->email = Str::lower(trim($req->account_email));
        $admin->instansi_id = $instansi->id;
        $admin->role_id = $role->id;
        $admin->type = 'admin';
        $admin->lender_id = null;
        $admin->company_id = null;
    }

    private function fillReferral(Referral $referral, Request $req): void
    {
        $referral->national_id_number = $req->national_id_number;
        $referral->name = $req->name;
        $referral->birth_place = $req->birth_place;
        $referral->birth_date = $req->birth_date ?: null;
        $referral->gender = $req->gender ?: null;
        $referral->address = $req->address;
        $referral->phone_number = $req->phone_number;
        $referral->email = $req->email ? Str::lower(trim($req->email)) : null;
        $referral->bank_id = $req->bank_id;
        $referral->bank_account_number = $req->bank_account_number;
        $referral->bank_account_name = $req->bank_account_name;
        $referral->notes = $req->notes;
        $referral->is_active = $req->boolean('is_active', true);
    }

    /**
     * Simpan file yang diunggah ke disk local dan isi kolomnya.
     *
     * @return array<int, string> path file baru (untuk dihapus jika penyimpanan gagal)
     */
    private function storeFiles(Referral $referral, Request $req): array
    {
        $stored = [];
        foreach (self::FILES as [$column, $directory]) {
            $file = $req->file($column);
            if ($file instanceof UploadedFile) {
                $name = Str::uuid().'.'.$file->getClientOriginalExtension();
                $referral->{$column} = $file->storeAs($directory, $name, 'local');
                $stored[] = $referral->{$column};
            }
        }

        return $stored;
    }

    private function deleteFiles(array $paths): void
    {
        foreach (array_filter($paths) as $path) {
            Storage::disk('local')->delete($path);
        }
    }
}
