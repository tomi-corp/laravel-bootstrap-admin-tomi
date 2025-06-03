<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Requests\User\Profile\ForgotOTPRequest;
use App\Http\Requests\User\Profile\ForgotPasswordActionRequest;
use App\Http\Requests\User\Profile\ForgotPasswordRequest;
use App\Http\Requests\User\Profile\UpdateEmailRequest;
use App\Http\Requests\User\Profile\UpdatePasswordRequest;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Http\Resources\User\Profile\ProfileDetailResource;
use App\Mail\ForgotEmail;
use App\Mail\PergantianPasswordEmail;
use App\Mail\VerifOtpNewMail;
use App\Mail\WelcomeEmail;
use App\Models\password_reset_user;
use App\Models\perubahaan_email;
use App\Models\User;
use App\Models\verif_email;
use App\Models\verif_whatsapp;
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
        $credentials = $request->only('email', 'password');
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
            'code' => 200,
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
            'code' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 200);
    }

    public function register(RegisterRequest $req)
    {

        // sendOTPWhatsapp()
        $verifEmail = verif_whatsapp::where('email', $req->email)->first();

        if ($verifEmail !== null) {
            $verifEmail->email = $req->email;
            $verifEmail->code_rand = rand(1000, 9999);
            $verifEmail->expired = Carbon::now()->addMinute(15);
        } else {
            $verifEmail = new verif_whatsapp();
            $verifEmail->email = $req->email;
            $verifEmail->code_rand = rand(1000, 9999);
            $verifEmail->expired = Carbon::now()->addMinute(15);
        }

        $user = new User();
        $user->name = $req->name;
        $user->nohp = $req->nohp;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);

        try {
            sendOTPWhatsapp($req->nohp, $verifEmail->code_rand);
            $user->save();
            $verifEmail->save();
            if ($token = auth('api')->attempt(['email' => $req->email, 'password' => $req->password])) {
                return response()->json([
                    'code' => 200,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'message' => 'Success Register',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'send_ulang_code' => '60'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => "register gagal",
            ], 400);
        }
    }

    public function updatePassword(UpdatePasswordRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->password = Hash::make($req->password);
        $user->save();

        $data = [
            'subject' => 'Pergantian password anda telah berhasil',
            'title' => 'Password anda telah diperbarui',
            'nama' => $user->name,
            'to' => $user->email,
        ];
        Mail::to($req->email)->send(new PergantianPasswordEmail($data));


        return response()->json([
            'status' => 200,
            'message' => 'Password Updated'
        ], 200);
    }

    public function emailUpdate(UpdateEmailRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $random =  rand(1000, 9999);;
        $updateEmail = perubahaan_email::where('user_id', $user->id)->first();
        if (isset($updateEmail)) {
            $formatted_dt1 = Carbon::parse($updateEmail->updated_at);
            $date_diff = $formatted_dt1->diffInDays(Carbon::now());
            if ($date_diff < 30) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Harap menunggu 30 Hari setelah email terkahir diperbarui'
                ], 400);
            } else {
                $data = [
                    'subject' => 'Konfirmasi Pergantian Email Anda',
                    'title' => 'Abaikan Jika anda tidak melakukan nya',
                    'nama' => $user->name,
                    'to' => $req->email,
                    'token' => $random
                ];
                Mail::to($req->email)->send(new VerifOtpNewMail($data));
                $updateEmail->otp = $random;
                $updateEmail->email = $req->email;
                $updateEmail->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'OTP Berhasil terikirim kealamat email anda'
                ], 200);
            }
        } else {
            $data = [
                'subject' => 'Konfirmasi Pergantian Email Anda',
                'title' => 'Abaikan Jika anda tidak melakukan nya',
                'nama' => $user->name,
                'to' => $req->email,
                'token' => $random
            ];
            Mail::to($req->email)->send(new VerifOtpNewMail($data));
            $updateEmail = new perubahaan_email();
            $updateEmail->user_id = $user->id;
            $updateEmail->otp = $random;
            $updateEmail->email = $req->email;
            $updateEmail->save();
            return response()->json([
                'status' => 200,
                'message' => 'OTP Berhasil terikirim kealamat email anda'
            ], 200);
        }
    }


    public function emailOtp(Request $req)
    {
        $updateEmail = perubahaan_email::where('user_id', auth('api')->user()->id)
            ->where('otp', $req->otp)
            ->first();

        if (isset($updateEmail)) {
            $user = User::where('id', auth('api')->user()->id)->first();
            $user->email = $updateEmail->email;
            $user->save();
            return response()->json([
                'status' => 200,
                'message' => 'Email berhasil diperbarui'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'OTP yang anda masukan salah'
            ], 400);
        }
    }

    public function profileUpdate(UpdateProfileRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->name = $req->name;
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
            $random =  rand(1000, 9999);;

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

    public function verifOtp(Request $req)
    {
        $verifEmail = verif_whatsapp::where('code_rand', $req->otp)
            ->where('email', auth('api')->user()->email)
            ->first();
        if (isset($verifEmail)) {
            if ($verifEmail->expired > Carbon::now()) {
                $user = User::where('email', $verifEmail->email)->first();
                $user->email_verified_at = Carbon::now();
                $user->save();
                $verifEmail->delete();


                $data = [
                    'subject' => 'Selamat Datang',
                    'title' => 'Terimakasih telah bergabung bersama kami',
                    'nama' => $user->name,
                    'to' => $user->email
                ];

                Mail::to($user->email)->send(new WelcomeEmail($data));


                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil verifikasi whatsapp',
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Kode OTP Expired',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan',
            ], 400);
        }
    }

    public function kirimUlangOtp(Request $req)
    {
        $user = User::where('email', auth('api')->user()->email)->first();

        if ($user->email_verified_at == null) {
            $verifEmail = verif_email::where('email', $user->email)->first();
            if ($verifEmail !== null) {
                $verifEmail->email = $user->email;
                $verifEmail->code_rand = rand(1000, 9999);;
                $verifEmail->expired = Carbon::now()->addMinute(15);
            } else {
                $verifEmail = new verif_email();
                $verifEmail->email = $user->email;
                $verifEmail->code_rand = rand(1000, 9999);;
                $verifEmail->expired = Carbon::now()->addMinute(15);
            }
            sendOTPWhatsapp($user->nohp, $verifEmail->code_rand);
            $verifEmail->save();
            // send email
            // $data = [
            //     'subject' => 'Yeay! Kamu Resmi Bergabung dengan TOMI!',
            //     'title' => 'Welcome',
            //     'to' => $user->email,
            //     'token' => $verifEmail->code_rand,
            // ];
            // Mail::to($user->email)->send(new WelcomeEmail($data));
            return response()->json([
                'status' => 200,
                'message' => 'OTP berhasil dikirim',
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Email telah terverifikasi',
            ], 200);
        }
    }
}
