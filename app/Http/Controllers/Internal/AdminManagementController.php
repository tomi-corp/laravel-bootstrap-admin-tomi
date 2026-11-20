<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Instansi;
use App\Models\lender;
use App\Models\ListPerusahaan;
use App\Models\Referral;
use App\Models\Role;
use App\Models\user_activity_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

/**
 * Kelola akun admin panel: instansi (internal/lender/perusahaan/referral), role per instansi,
 * dan entitas yang diwakili (lender/perusahaan/referral) sesuai skema admin yang baru.
 */
class AdminManagementController extends Controller
{
    /** Instansi yang wajib memilih entitas => kolom entitas di tabel admins. */
    private const ENTITY_COLUMNS = [
        'lender' => 'lender_id',
        'perusahaan' => 'company_id',
        'referral' => 'referral_id',
    ];

    public function index()
    {
        return view('internal.admin-management.index', [
            'instansis' => Instansi::query()->orderBy('id')->get(['id', 'code', 'name']),
            'roles' => Role::query()->orderBy('instansi_id')->orderBy('name')->get(['id', 'instansi_id', 'code', 'name']),
            // Pilihan entitas per kode instansi, untuk dropdown yang muncul setelah instansi dipilih.
            'entities' => [
                'lender' => ['label' => 'Lender', 'items' => lender::query()->where('status', 'active')->orderBy('nama')->get(['id', 'nama as name'])],
                'perusahaan' => ['label' => 'Perusahaan', 'items' => ListPerusahaan::query()->where('status', 'active')->orderBy('nama')->get(['id', 'nama as name'])],
                'referral' => ['label' => 'Referral', 'items' => Referral::query()->where('is_active', 1)->orderBy('name')->get(['id', 'name'])],
            ],
        ]);
    }

    public function table()
    {
        $query = Admin::query()->select('admins.*')->with(['instansi:id,name', 'role:id,name', 'lender:id,nama', 'company:id,nama', 'referral:id,name']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('name', fn ($row) => e($row->name ?: $row->username))
            ->addColumn('instansi', fn ($row) => $row->instansi?->name ?? '-')
            ->addColumn('role', fn ($row) => $row->role?->name ?? '-')
            ->addColumn('entity', fn ($row) => $row->lender?->nama ?? $row->company?->nama ?? $row->referral?->name ?? '-')
            ->addColumn('two_factor', fn ($row) => $row->google2fa_secret
                ? '<span class="badge badge-success">AKTIF</span>'
                : '<span class="badge badge-warning">BELUM AKTIF</span>')
            ->editColumn('created_at', fn ($row) => $row->created_at?->format('d-m-Y H:i') ?? '-')
            ->addColumn('action', function ($row) {
                $buttons = '<button class="btn btn-sm btn-primary mr-1" type="button" title="Ubah" onclick="editAdmin('.$row->id.')"><i class="mdi mdi-pencil"></i></button>';
                if ($row->id !== auth('admin')->id()) {
                    $buttons .= '<button class="btn btn-sm btn-danger" type="button" title="Hapus" onclick="deleteAdmin('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>';
                }

                return $buttons;
            })
            ->filterColumn('instansi', fn ($q, $keyword) => $q->whereHas('instansi', fn ($i) => $i->where('name', 'like', "%{$keyword}%")))
            ->filterColumn('role', fn ($q, $keyword) => $q->whereHas('role', fn ($r) => $r->where('name', 'like', "%{$keyword}%")))
            ->rawColumns(['two_factor', 'action'])
            ->toJson();
    }

    public function show(Request $req)
    {
        $admin = Admin::findOrFail($req->id);

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'instansi_id' => $admin->instansi_id,
                'role_id' => $admin->role_id,
                'entity_id' => $admin->lender_id ?? $admin->company_id ?? $admin->referral_id,
            ],
        ], 200);
    }

    public function store(Request $req)
    {
        $req->validate($this->rules(), $this->messages());

        $admin = new Admin;
        $this->fillAdmin($admin, $req);
        $admin->password = Hash::make($req->password);
        $admin->save();

        return response()->json(['status' => 200, 'message' => 'Admin berhasil ditambahkan.'], 200);
    }

    public function update(Request $req)
    {
        $admin = Admin::findOrFail($req->id);
        $req->validate($this->rules($admin), $this->messages());

        $this->fillAdmin($admin, $req);
        if ($req->filled('password')) {
            $admin->password = Hash::make($req->password);
        }
        $admin->save();

        return response()->json(['status' => 200, 'message' => 'Admin berhasil diperbarui.'], 200);
    }

    public function destroy(Request $req)
    {
        $admin = Admin::findOrFail($req->id);
        abort_if($admin->id === auth('admin')->id(), 422, 'Tidak bisa menghapus akun yang sedang dipakai.');

        $admin->delete();

        return response()->json(['status' => 200, 'message' => 'Admin berhasil dihapus.'], 200);
    }

    public function auditTable()
    {
        $query = user_activity_logs::query()->select('user_activity_logs.*')->with('admin:id,username');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', fn ($row) => $row->created_at?->format('d-m-Y H:i:s') ?? '-')
            ->addColumn('username', fn ($row) => $row->admin?->username ?? '-')
            ->editColumn('activity_type', fn ($row) => match ($row->activity_type) {
                'delete' => '<span class="badge badge-danger">HAPUS</span>',
                'create' => '<span class="badge badge-primary">TAMBAH</span>',
                'update' => '<span class="badge badge-success">UBAH</span>',
                default => '<span class="badge badge-secondary">'.e(Str::upper((string) $row->activity_type)).'</span>',
            })
            ->rawColumns(['activity_type'])
            ->toJson();
    }

    /** Password wajib saat tambah; saat ubah boleh dikosongkan (password lama tetap dipakai). */
    private function rules(?Admin $admin = null): array
    {
        $password = Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised();

        return [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('admins', 'username')->ignore($admin?->id)->whereNull('deleted_at')],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin?->id)->whereNull('deleted_at')],
            'instansi_id' => 'required|exists:instansis,id',
            'role_id' => 'required|exists:roles,id',
            'entity_id' => 'nullable|integer',
            'password' => $admin ? ['nullable', 'confirmed', $password] : ['required', 'confirmed', $password],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai admin lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai admin lain.',
            'instansi_id.required' => 'Instansi wajib dipilih.',
            'role_id.required' => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }

    /**
     * Isi instansi, role, entitas, dan tipe admin. Role harus milik instansi yang dipilih,
     * entitas wajib untuk instansi lender/perusahaan/referral, dan role Super Admin hanya
     * boleh diberikan oleh super admin.
     */
    private function fillAdmin(Admin $admin, Request $req): void
    {
        $role = Role::with('instansi')->findOrFail($req->role_id);

        if ((int) $role->instansi_id !== (int) $req->instansi_id) {
            throw ValidationException::withMessages(['role_id' => 'Role tidak sesuai dengan instansi yang dipilih.']);
        }

        $instansi = $role->instansi->code;
        $isSuperAdmin = $instansi === 'internal' && $role->code === 'super_admin';
        if ($isSuperAdmin && auth('admin')->user()?->type !== 'master') {
            throw ValidationException::withMessages(['role_id' => 'Hanya super admin yang bisa memberikan role Super Admin.']);
        }

        $entityColumn = self::ENTITY_COLUMNS[$instansi] ?? null;
        if ($entityColumn) {
            $entityExists = match ($instansi) {
                'lender' => lender::whereKey($req->entity_id)->exists(),
                'perusahaan' => ListPerusahaan::whereKey($req->entity_id)->exists(),
                'referral' => Referral::whereKey($req->entity_id)->exists(),
            };
            if (! $req->filled('entity_id') || ! $entityExists) {
                throw ValidationException::withMessages(['entity_id' => ucfirst($instansi).' wajib dipilih.']);
            }
        }

        $admin->name = $req->name;
        $admin->username = $req->username;
        $admin->email = Str::lower(trim($req->email));
        $admin->instansi_id = $role->instansi_id;
        $admin->role_id = $role->id;
        $admin->type = $isSuperAdmin ? 'master' : 'admin';
        foreach (self::ENTITY_COLUMNS as $column) {
            $admin->{$column} = $column === $entityColumn ? (int) $req->entity_id : null;
        }
    }
}
