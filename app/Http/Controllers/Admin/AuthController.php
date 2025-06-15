<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\DoForgorPasswordRequest;
use App\Mail\ForgotEmailBackend;
use App\Models\Admin;
use App\Models\password_reset_admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
        if (filter_var($request->identity, FILTER_VALIDATE_EMAIL)) {
            if (auth('admin')
                ->attempt([
                    'email' => $request->identity,
                    'password' => $request->password
                ], $request->remember === 'on' ? true : false)
            ) {
                return redirect()->route('admin.dashboard.index');
            } else {
                toastr()->error('Username/email atau password anda salah!');
                return redirect()->route('admin.login');
            }
        } else {
            if (auth('admin')
                ->attempt([
                    'username' => $request->identity,
                    'password' => $request->password
                ], $request->remember === 'on' ? true : false)
            ) {
                return redirect()->route('admin.dashboard.index');
            } else {
                toastr()->error('Username/email atau password anda salah!');
                return redirect()->route('admin.login');
            }
        }
    }


    public function logoutAction()
    {
        auth('admin')->logout();
        return redirect()->route('admin.login');
    }
}
