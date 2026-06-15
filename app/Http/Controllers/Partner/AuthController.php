<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('partner.auth.login');
    }

    public function loginProcess(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? [
                'email' => $request->identity,
                'password' => $request->password
            ]
            : [
                'username' => $request->identity,
                'password' => $request->password
            ];

        if (Auth::guard('partner')->attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return redirect()
                ->route('partners.dashboard')
                ->with('success', 'Login berhasil.');
        }

        return back()
            ->withInput()
            ->with('error', 'Email, username, atau password salah.');
    }

    /**
     * Form lupa password
     */
    public function forgotPassword()
    {
        return view('partner.auth.forgot-password');
    }

    /**
     * Kirim link reset password
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return back()->with(
            'info',
            'Fitur reset password belum diaktifkan.'
        );
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('partners.login')
            ->with('success', 'Logout berhasil.');
    }
}
