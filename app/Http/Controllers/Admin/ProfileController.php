<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FAQRCode\Google2FA;


class ProfileController extends Controller
{
    public function index(Request $req)
    {
        $user = Admin::where('id', auth('admin')->user()->id)->first();
        if (isset($user->twofactor)) {
            $enabled = true;
        } else {
            $enabled = false;
        }
        return view('admin.profile.index', compact('user', 'enabled'));
    }

    public function enableTwoFactor()
    {
        $user = Admin::where('id', auth('admin')->user()->id)->first();

        if (isset($user->google2fa_secret)) {
            return response()->json([
                'status' => 400,
                'message' => 'anda sudah mengaktifkan 2fa'
            ], 400);
        } else {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                'TOMI',
                $user->email,
                $secret
            );

            $writer = new Writer(new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            ));
            $qrCodeSvg = $writer->writeString($qrCodeUrl);

            return response()->json([
                'status' => 200,
                'data' => $qrCodeSvg
            ], 200);
        }
    }

    public function confirmationCode()
    {
        return view('admin.auth.twofactor.index');
    }

    public function confirmationCodeInput(Request $req)
    {
        $user = auth('admin')->user();
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $req->code);
        if ($valid) {
            session(['2fa_passed' => true]);
            return redirect()->intended(route('admin.dashboard.index'));
        } else {
            return back()->withErrors([
                'code' => 'Kode dua faktor yang Anda masukkan tidak valid.',
            ]);
        }
    }

    public function updateProfile(UpdateProfileRequest $req)
    {
        $user = Admin::where('id', auth('admin')->user()->id)->first();
        $user->username = $req->username;
        $user->email = $req->email;
        if ($req->password != "") {
            $user->password = Hash::make($req->password);
        }
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Profile berhasil diperbarui'
        ], 200);
    }
}
