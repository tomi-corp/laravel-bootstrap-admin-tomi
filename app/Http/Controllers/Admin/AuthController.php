<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AddAdminRequest;
use App\Http\Requests\Admin\Auth\DoForgorPasswordRequest;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use App\Mail\ForgotEmailBackend;
use App\Models\Admin;
use App\Models\lender_update;
use App\Models\password_reset_admin;
use App\Models\User;
use App\Models\user_activity_logs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
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
            $random = Str::random(20) .  $user->email . Str::random(20);

            $data = [
                'subject' => 'Permintaan Reset Password',
                'title' => 'Lupa Password',
                'to' => $user->email,
                'token' => $random,
            ];

            try {
                Mail::to($user->email)->send(new ForgotEmailBackend($data));
                $token  = password_reset_admin::where('email', $user->email)->first();
                if (isset($token)) {
                    $token->token = $random;
                    $token->email = $user->email;
                    $token->save();
                } else {
                    $token  = new password_reset_admin();
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
        $cscs  = password_reset_admin::where('token', $token)->first();
        if (isset($cscs)) {
            // cek expired
            $anchorTime = Carbon::createFromFormat("Y-m-d H:i:s", $cscs->updated_at);
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

        $token  = password_reset_admin::where('token', $tokenSlug)->first();
        if (isset($token)) {
            // cek expired
            $anchorTime = Carbon::createFromFormat("Y-m-d H:i:s", $token->updated_at);
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
        $remember = $request->boolean('remember');

        if (filter_var($request->identity, FILTER_VALIDATE_EMAIL)) {
            if (auth('admin')
                ->attempt([
                    'email' => $request->identity,
                    'password' => $request->password
                ], $remember)
            ) {
                $user = auth('admin')->user();
                if (! blank($user->google2fa_secret)) {
                    return redirect()->route('admin.profile.confirmationCode');
                }
                return redirect()->intended(route('admin.dashboard.index'));
            } else {
                toastr()->error('Username/email atau password anda salah!');
                return redirect()->route('admin.login');
            }
        } else {
            if (auth('admin')
                ->attempt([
                    'username' => $request->identity,
                    'password' => $request->password
                ], $remember)
            ) {
                $user = auth('admin')->user();
                if (! blank($user->google2fa_secret)) {
                    return redirect()->route('admin.profile.confirmationCode');
                }
                return redirect()->intended(route('admin.dashboard.index'));
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
        return view('admin.auth.manage.index');
    }


    public function manageAdminTable()
    {
        $admin = Admin::get();
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
                if ($row->type == "master") {
                    $type = '<span class="badge badge-success">MASTER</span>';
                } else {
                    $type = '<span class="badge badge-warning">ADMIN</span>';
                }
                return $type;
            })

            ->addColumn('action', function ($row) {
                return '<div class="btn-group">
                    <button class="btn btn-sm btn-primary" onclick="editAdmin(' . $row->id . ')"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteAdmin(' . $row->id . ')"><i class="fa-solid fa-trash-can"></i></button>
                </div>';
            })

            ->rawColumns(['action', 'google2fa_secret', 'type'])
            ->only([
                'DT_RowIndex',
                'created_at',
                'updated_at',
                'username',
                'email',
                'type',
                'google2fa_secret',
                'action'
            ])
            ->toJson();
    }

    public function manageAdminAdd(AddAdminRequest $req)
    {
        $admin = new Admin();
        $admin->username = $req->username;
        $admin->email = $req->email;
        $admin->type = $req->type_admin;
        $admin->password = Hash::make($req->password);
        $admin->save();

        return response()->json([
            'status' => 200,
            'message' => 'Admin Berhasil Ditambahkan'
        ], 200);
    }

    public function manageAdminDetail(Request $req)
    {
        $admin = Admin::findOrFail($req->id);

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $admin->id,
                'username' => $admin->username,
                'email' => $admin->email,
                'type' => $admin->type,
            ],
        ], 200);
    }

    public function manageAdminUpdate(UpdateAdminRequest $req)
    {
        $admin = Admin::findOrFail($req->id);
        $admin->username = $req->username;
        $admin->email = $req->email;
        $admin->type = $req->type_admin;

        if ($req->filled('password')) {
            $admin->password = Hash::make($req->password);
        }

        $admin->save();

        return response()->json([
            'status' => 200,
            'message' => 'Admin Berhasil Diperbarui'
        ], 200);
    }

    public function manageAdminDelete(Request $req)
    {
        $admin = Admin::where('id', $req->id)->first();
        $admin->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Admin Berhasil Dihapus'
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
                return $row->admin?->username ?? '-';
            })

            ->addColumn('activity_type', function ($row) {
                if ($row->activity_type == "delete") {
                    $activ = '<span class="badge badge-danger">DELETE</span>';
                } elseif ($row->activity_type == "create") {
                    $activ = '<span class="badge badge-primary">CREATE</span>';
                } elseif ($row->activity_type == "update") {
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
                'user_agent'
            ])
            ->toJson();
    }


    public function test() {}
}
