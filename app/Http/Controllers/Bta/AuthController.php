<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bta\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('bta.auth.login.index');
    }
    public function actionLogin(LoginRequest $req)
    {
        if (auth('bta')
            ->attempt([
                'username' => $req->username,
                'password' => $req->password
            ], $req->remember === 'on' ? true : false)
        ) {
            return redirect()->intended(route('bta.dashboard.index'));
        } else {
            toastr()->error('Username atau password anda salah!');
            return redirect()->route('bta.login');
        }
    }
}
