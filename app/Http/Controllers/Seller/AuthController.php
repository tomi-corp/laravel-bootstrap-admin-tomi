<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('seller.auth.login');
    }

    public function loginAction(Request $request)
    {
        $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->identity]
            : ['phone' => $request->identity];

        $credentials = array_merge($login, [
            'password' => $request->password,
            'is_active' => true,
        ]);

        if (Auth::guard('seller')->attempt($credentials, $request->remember)) {

            $request->session()->regenerate();

            return redirect()->route('seller.dashboard');
        }

        return back()
            ->withInput($request->only('identity'))
            ->withErrors([
                'identity' => 'Email / phone atau password salah.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('seller')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login');
    }
}
