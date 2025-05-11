<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Requests\User\Profile\ForgotOTPRequest;
use App\Http\Requests\User\Profile\ForgotPasswordActionRequest;
use App\Http\Requests\User\Profile\ForgotPasswordRequest;
use App\Http\Requests\User\Profile\UpdatePasswordRequest;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Http\Resources\User\Profile\ProfileDetailResource;
use App\Mail\ForgotEmail;
use App\Mail\WelcomeEmail;
use App\Models\password_reset_user;
use App\Models\User;
use App\Models\verif_email;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    public function cekUsername(Request $req)
    {
        $user = User::where('username', $req->username)->first();
        if (isset($user)) {
            return response()->json([
                'status' => false,
                'message' => '',
            ], 400);
        } else {
            return response()->json([
                'status' => true,
                'message' => '',
            ], 200);
        }
    }

    public function cekReff(Request $req) {}


    public function lang($lang)
    {
        if (!in_array($lang, ['en', 'id'])) {
            return response()->json([
                'status' => false,
                'message' => 'enum => en:id',
            ], 200);
        } else {
            $user = User::where('id', auth('api')->user()->id)->first();
            $user->lang = $lang;
            $user->save();
            App::setLocale($lang);
            return response()->json([
                'message' => 'success',
            ], 200);
        }
    }



    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        if ($token = auth('api')->attempt($credentials)) {
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }


    public function me()
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        return response()->json([
            'status' => 200,
            'detail' => new ProfileDetailResource($user)
        ], 200);
    }


    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function register(RegisterRequest $req)
    {

        while (true) {
            $str = Str::random(8);
            $userrr = User::where('ref_code', $str)->first();
            if (!isset($userrr)) {
                break;
            }
        }

        $userx = User::where('ref_code', $req->referral)
            ->where('ref_code', '!=', null)->first();

        $verifEmail = new verif_email();
        $verifEmail->email = $req->email;
        $verifEmail->code_rand = Str::random(32);
        $verifEmail->expired = Carbon::now()->addMinute(15);

        $user = new User();
        $user->name = $req->name;
        $user->username = $req->username;
        $user->email = $req->email;
        $user->ref_code = $str;
        $user->referral_id = $userx ? $userx->id : null;
        $user->password = Hash::make($req->password);
        $user->save();
        $verifEmail->save();

        // send email
        $data = [
            'subject' => 'Yeay! Kamu Resmi Bergabung dengan TOMI!',
            'title' => 'Welcome',
            'to' => $req->email,
            'token' => $verifEmail->code_rand,
        ];

        Mail::to($req->email)->send(new WelcomeEmail($data));

        return response()->json([
            'status' => 200,
            'message' => 'Success Register'
        ], 200);
    }

    public function updatePassword(UpdatePasswordRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->password = Hash::make($req->password);
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Password Updated'
        ], 200);
    }

    public function profileUpdate(UpdateProfileRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->name = $req->name;
        $user->username = $req->username;
        $user->email = $req->email;
        $user->dob = $req->dob;
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Profile Updated'
        ], 200);
    }

    public function forgotPassword(ForgotPasswordRequest $req)
    {
        $email = User::where('email', '=', $req->email)->first();

        if (isset($email)) {
            $random =  rand(0, 99999);

            $token =  password_reset_user::firstOrNew();
            $token->email = $email->email;
            $token->otp = $random;
            $token->save();

            $data = [
                'subject' => 'Permintaan Reset Password',
                'title' => 'Lupa Password',
                'to' => $email->email,
                'token' => $random,
            ];

            Mail::to($email->email)->send(new ForgotEmail($data));
        }
        return response()->json([
            'status' => 200,
            'message' => 'success'
        ], 200);
    }

    public function checkOtp(ForgotOTPRequest $req)
    {
        $token =  password_reset_user::where('email', $req->email)
            ->where('otp', $req->token)
            ->first();

        if (isset($token)) {
            return response()->json([
                'status' => 200,
                'message' => 'true'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'false'
            ], 400);
        }
    }

    public function actionUpdate(ForgotPasswordActionRequest $req)
    {
        $token =  password_reset_user::where('email', $req->email)
            ->where('otp', $req->token)
            ->first();

        $user = User::where('email', $token->email)->first();
        $user->password = Hash::make($req->password);
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'updated'
        ], 200);
    }

    public function validasiEmail($valid)
    {
        // $verif = verif_email::where('code_rand', $valid)->first();
        // if (isset($verif)) {
        // } else {
        // }
    }
}
