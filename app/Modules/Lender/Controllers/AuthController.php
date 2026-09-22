<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ForgotEmailBackend;
use App\Models\ksp_admin;
use App\Models\password_reset_admin;
use App\Modules\Lender\Requests\LoginRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('lender.auth.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $credentials = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->identity, 'password' => $request->password]
            : ['username' => $request->identity, 'password' => $request->password];

        if (! Auth::guard('lender')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('identity'))
                ->with('error', 'Username/email atau password salah.');
        }

        $user = Auth::guard('lender')->user();

        if (blank($user->id_lender)) {
            Auth::guard('lender')->logout();

            return back()
                ->withInput($request->only('identity'))
                ->with('error', 'Akun ini tidak memiliki akses lender.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('lender.dashboard'));
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('lender')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('lender.login');
    }

    public function forgot(): View
    {
        return view('lender.auth.forgot');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $admin = ksp_admin::query()
            ->where('email', $request->email)
            ->whereNotNull('id_lender')
            ->first();

        if ($admin) {
            $token = Str::random(20).$admin->email.Str::random(20);

            password_reset_admin::query()->updateOrCreate(
                ['email' => $admin->email],
                ['token' => $token]
            );

            Mail::to($admin->email)->send(new ForgotEmailBackend([
                'subject' => 'Permintaan Reset Password Lender',
                'title' => 'Reset Password Lender',
                'to' => $admin->email,
                'token' => $token,
                'view' => 'lender.mail.forgot',
            ]));
        }

        return back()->with('success', 'Jika email terdaftar sebagai lender, link reset password akan dikirim.');
    }

    public function reset(string $token): View
    {
        $reset = password_reset_admin::query()->where('token', $token)->firstOrFail();

        abort_if(Carbon::parse($reset->updated_at)->diffInMinutes(now()) > 15, 404);

        return view('lender.auth.reset', [
            'token' => $token,
        ]);
    }

    public function updatePassword(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $reset = password_reset_admin::query()->where('token', $token)->firstOrFail();

        if (Carbon::parse($reset->updated_at)->diffInMinutes(now()) > 15) {
            return redirect()
                ->route('lender.forgot')
                ->with('error', 'Token reset password sudah kedaluwarsa.');
        }

        $admin = ksp_admin::query()
            ->where('email', $reset->email)
            ->whereNotNull('id_lender')
            ->firstOrFail();

        $admin->password = Hash::make($request->password);
        $admin->save();
        $reset->delete();

        return redirect()
            ->route('lender.login')
            ->with('success', 'Password berhasil diperbarui. Silakan login.');
    }
}
