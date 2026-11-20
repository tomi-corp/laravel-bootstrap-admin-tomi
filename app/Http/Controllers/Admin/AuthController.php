<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AddAdminRequest;
use App\Http\Requests\Admin\Auth\DoForgorPasswordRequest;
use App\Mail\ForgotEmailBackend;
use App\Models\Admin;
use App\Models\Instansi;
use App\Models\lender;
use App\Models\ListPerusahaan;
use App\Models\password_reset_admin;
use App\Models\Referral;
use App\Models\Role;
use App\Models\user_activity_logs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login.index');
    }

    public function forgot()
    {
        return view('admin.auth.forgot.index');
    }

    public function forgotAction(Request $req)
    {
        $user = Admin::where('email', $req->email)->first();
        if (isset($user)) {
            $random = Str::random(20).$user->email.Str::random(20);

            $data = [
                'subject' => 'Permintaan Reset Password',
                'title' => 'Lupa Password',
                'to' => $user->email,
                'token' => $random,
            ];

            try {
                Mail::to($user->email)->send(new ForgotEmailBackend($data));
                $token = password_reset_admin::where('email', $user->email)->first();
                if (isset($token)) {
                    $token->token = $random;
                    $token->email = $user->email;
                    $token->save();
                } else {
                    $token = new password_reset_admin;
                    $token->token = $random;
                    $token->email = $user->email;
                    $token->save();
                }
            } catch (\Throwable $th) {
                toastr()->info('ada kesalahan pada server!');

                return back();
            }
        }

        toastr()->info('Silahkan periksa inbox/spam folder email anda!');

        return back();
    }

    public function forgotUpdatePassword($token)
    {
        $cscs = password_reset_admin::where('token', $token)->first();
        if (isset($cscs)) {
            // cek expired
            $anchorTime = Carbon::createFromFormat('Y-m-d H:i:s', $cscs->updated_at);
            $minuteDiff = $anchorTime->diffInMinutes(Carbon::now());
            // expired 15 menit
            if ($minuteDiff > 15) {
                toastr()->info('token telah kadaluarsa');

                return redirect()->route('admin.forgot');
            } else {
                return view('admin.auth.forgot.action', compact('token'));
            }
        } else {
            abort(404);
        }
    }

    public function forgotActionDo(DoForgorPasswordRequest $req, $tokenSlug)
    {

        $token = password_reset_admin::where('token', $tokenSlug)->first();
        if (isset($token)) {
            // cek expired
            $anchorTime = Carbon::createFromFormat('Y-m-d H:i:s', $token->updated_at);
            $minuteDiff = $anchorTime->diffInMinutes(Carbon::now());
            // expired 15 menit
            if ($minuteDiff > 15) {
                toastr()->info('token telah kadaluarsa');

                return redirect()->route('admin.forgot');
            } else {
                $admin = Admin::where('email', $token->email)->first();
                $admin->password = Hash::make($req->password);
                $admin->save();
                toastr()->success('password success diperbarui');

                return redirect()->route('admin.login');
            }
        } else {
            abort(404);
        }
    }

    public function loginAction(Request $request)
    {
        if (filter_var($request->identity, FILTER_VALIDATE_EMAIL)) {
            if (auth('admin')
                ->attempt([
                    'email' => $request->identity,
                    'password' => $request->password,
                ], $request->remember === 'on' ? true : false)
            ) {
                $user = auth('admin')->user();
                if (! blank($user->google2fa_secret)) {
                    return redirect()->route('admin.profile.confirmationCode');
                }

                return redirect()->intended(route('admin.insight.index'));
            } else {
                toastr()->error('Username/email atau password anda salah!');

                return redirect()->route('admin.login');
            }
        } else {
            if (auth('admin')
                ->attempt([
                    'username' => $request->identity,
                    'password' => $request->password,
                ], $request->remember === 'on' ? true : false)
            ) {
                $user = auth('admin')->user();
                if (! blank($user->google2fa_secret)) {
                    return redirect()->route('admin.profile.confirmationCode');
                }

                return redirect()->intended(route('admin.insight.index'));
            } else {
                toastr()->error('Username/email atau password anda salah!');

                return redirect()->route('admin.login');
            }
        }
    }

    public function logoutAction()
    {
        session(['2fa_passed' => false]);
        auth('admin')->logout();

        return redirect()->route('admin.login');
    }

    // manage admin
    public function manageAdminIndex()
    {
        return view('admin.auth.manage.index', [
            'instansis' => Instansi::query()->orderBy('name')->get(),
            'roles' => Role::query()->with('instansi')->orderBy('name')->get(),
            'lenders' => lender::query()->where('status', 'active')->orderBy('nama')->get(['id', 'nama']),
            'companies' => ListPerusahaan::query()->where('status', 'active')->orderBy('nama')->get(['id', 'nama']),
            'referrals' => Referral::query()->where('is_active', 1)->whereNull('admin_id')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function manageAdminTable()
    {
        $admin = Admin::with(['instansi', 'role', 'lender', 'company', 'referral'])->get();

        return DataTables::of($admin)
            ->addIndexColumn()

            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('updated_at', function ($row) {
                return Carbon::parse($row->updated_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('google2fa_secret', function ($row) {
                if ($row->google2fa_secret != null) {
                    $auth2fa = '<span class="badge badge-success">ACTIVE</span>';
                } else {
                    $auth2fa = '<span class="badge badge-warning">INACTIVE</span>';
                }

                return $auth2fa;
            })

            ->addColumn('type', function ($row) {
                if ($row->type == 'master') {
                    $type = '<span class="badge badge-success">MASTER</span>';
                } else {
                    $type = '<span class="badge badge-warning">ADMIN</span>';
                }

                return $type;
            })

            ->addColumn('instansi', fn ($row) => $row->instansi?->name ?? '-')
            ->addColumn('role', fn ($row) => $row->role?->name ?? '-')
            ->addColumn('entity', function ($row) {
                return $row->lender?->nama
                    ?? $row->company?->nama
                    ?? $row->referral?->name
                    ?? '-';
            })

            ->addColumn('action', function ($row) {
                // <button class="btn btn-sm btn-primary mr-2" onclick="editAdmin(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>
                return '<button class="btn btn-sm btn-danger mr-2" onclick="deleteAdmin('.$row->id.')"><i class=" mdi mdi-trash-can"></i> </button>';
            })

            ->rawColumns(['action', 'google2fa_secret', 'type'])
            ->only([
                'DT_RowIndex',
                'created_at',
                'updated_at',
                'name',
                'username',
                'email',
                'type',
                'instansi',
                'role',
                'entity',
                'google2fa_secret',
                'action',
            ])
            ->toJson();
    }

    public function manageAdminAdd(AddAdminRequest $req)
    {
        $role = Role::with('instansi')->findOrFail($req->role_id);

        if ((int) $role->instansi_id !== (int) $req->instansi_id) {
            throw ValidationException::withMessages([
                'role_id' => 'Role tidak sesuai dengan instansi yang dipilih.',
            ]);
        }

        $instansi = $role->instansi->code;
        $requiredEntity = match ($instansi) {
            'lender' => 'lender_id',
            'perusahaan' => 'company_id',
            'referral' => 'referral_id',
            default => null,
        };

        if ($requiredEntity && ! $req->filled($requiredEntity)) {
            throw ValidationException::withMessages([
                $requiredEntity => 'Entity wajib dipilih untuk instansi ini.',
            ]);
        }

        // Referral adalah turunan admin: relasinya disimpan di referrals.admin_id, satu referral satu akun.
        if ($instansi === 'referral' && Referral::whereKey($req->referral_id)->whereNotNull('admin_id')->exists()) {
            throw ValidationException::withMessages([
                'referral_id' => 'Referral ini sudah memiliki akun admin.',
            ]);
        }

        $admin = new Admin;
        $admin->name = $req->name;
        $admin->username = $req->username;
        $admin->email = $req->email;
        $admin->instansi_id = $role->instansi_id;
        $admin->role_id = $role->id;
        $admin->lender_id = $instansi === 'lender' ? $req->lender_id : null;
        $admin->company_id = $instansi === 'perusahaan' ? $req->company_id : null;
        $admin->type = $instansi === 'internal' && $role->code === 'super_admin' ? 'master' : 'admin';
        $admin->password = Hash::make($req->password);

        DB::transaction(function () use ($admin, $instansi, $req) {
            $admin->save();

            if ($instansi === 'referral') {
                Referral::whereKey($req->referral_id)->update(['admin_id' => $admin->id]);
            }
        });

        return response()->json([
            'status' => 200,
            'message' => 'Admin Berhasil Ditambahkan',
        ], 200);
    }

    public function manageAdminDelete(Request $req)
    {
        $admin = Admin::where('id', $req->id)->first();
        $admin->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Admin Berhasil Dihapus',
        ], 200);
    }

    public function manageAuditTable()
    {
        $admin = user_activity_logs::with('admin')->get();

        return DataTables::of($admin)
            ->addIndexColumn()

            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('username', function ($row) {
                return $row->admin->username;
            })

            ->addColumn('activity_type', function ($row) {
                if ($row->activity_type == 'delete') {
                    $activ = '<span class="badge badge-danger">DELETE</span>';
                } elseif ($row->activity_type == 'create') {
                    $activ = '<span class="badge badge-primary">CREATE</span>';
                } elseif ($row->activity_type == 'update') {
                    $activ = '<span class="badge badge-success">UPDATE</span>';
                }

                return $activ;
            })

            ->rawColumns(['activity_type'])
            ->only([
                'DT_RowIndex',
                'created_at',
                'username',
                'activity_type',
                'description',
                'ip_address',
                'user_agent',
            ])
            ->toJson();
    }

    public function test() {}
}
