<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('seller.auth.login.index');
    }

    public function loginAction(Request $request)
    {
        $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        return back()
            ->withInput($request->only('identity', 'remember'))
            ->withErrors([
                'identity' => 'Seller authentication is not configured yet.',
            ]);
    }
}
