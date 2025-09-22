<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Requests\User\Auth\RevisiEmailRequest;
use App\Http\Requests\User\Profile\ForgotOTPRequest;
use App\Http\Requests\User\Profile\ForgotPasswordActionRequest;
use App\Http\Requests\User\Profile\ForgotPasswordRequest;
use App\Http\Requests\User\Profile\UpdateEmailRequest;
use App\Http\Requests\User\Profile\UpdatePasswordRequest;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Http\Requests\User\Profile\UpdateProfileSelfieKTPKKRequest;
use App\Http\Resources\User\Profile\ProfileDetailResource;
use App\Mail\ForgotEmail;
use App\Mail\PergantianPasswordEmail;
use App\Mail\VerifikasiEmail;
use App\Mail\VerifOtpNewMail;
use App\Mail\WelcomeEmail;
use App\Models\notification;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



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
        $user = new User();
        $user->name = $req->name;
        $user->nohp = $req->nohp;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        try {
            $user->save();
            $verifWa = verif_whatsapp::where('id_user', $user->id)->first();
            if ($verifWa !== null) {
                $verifWa->id_user = $user->id;
                $verifWa->code_rand = rand(1000, 9999);
                $verifWa->expired = Carbon::now()->addMinute(15);
            } else {
                $verifWa = new verif_whatsapp();
                $verifWa->id_user = $user->id;
                $verifWa->code_rand = rand(1000, 9999);
                $verifWa->expired = Carbon::now()->addMinute(15);
            }

            $verifEmail = verif_email::where('id_user', $user->id)->first();
            if ($verifEmail !== null) {
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            } else {
                $verifEmail = new verif_email();
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            }
            $verifEmail->save();
            $verifWa->save();

            $data = [
                'subject' => 'Selangkah Lagi! Aktifkan Akun TOMI Anda',
                'title' => 'Aktifkan Akun Anda Sekarang',
                'to' => $user->email,
                'token' => $verifEmail->code_rand,
                'nama' => $user->nama,
            ];

            Mail::to($user->email)->send(new VerifikasiEmail($data));
            sendOTPWhatsapp($req->nohp, $verifWa->code_rand);

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
            Log::channel('registerlog')->info($th);
            return response()->json([
                'code' => 400,
                'message' => "register gagal",
            ], 400);
        }
    }

    public function revisiEmail(RevisiEmailRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->email = $req->email;
        $user->save();

        if ($user->email_verified_at == null) {
            $verifEmail = verif_email::where('id_user', $user->id)->first();
            if ($verifEmail !== null) {
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            } else {
                $verifEmail = new verif_email();
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            }
            $verifEmail->save();
            $data = [
                'subject' => 'Selangkah Lagi! Aktifkan Akun TOMI Anda',
                'title' => 'Aktifkan Akun Anda Sekarang',
                'to' => $user->email,
                'token' => $verifEmail->code_rand,
                'nama' => $user->nama,
            ];

            Mail::to($user->email)->send(new VerifikasiEmail($data));

            return response()->json([
                'status' => 200,
                'message' => 'OTP Berhasil dikirim ke email ' . $user->email,
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Email telah terverifikasi',
            ], 200);
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
        $notif = new notification();
        $notif->user_id = $user->id;
        $notif->title = "Password Berhasil Diperbarui";
        $notif->description = "Password Anda telah berhasil diubah. Untuk keamanan akun, kami sarankan untuk: 1. Menggunakan kombinasi huruf, angka, dan simbol. 2. Mengganti password secara berkala. 3. Tidak membagikan password ke siapapun.";
        $notif->type = 'profile';
        $notif->read = 'belum';
        $notif->save();

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

            $notif = new notification();
            $notif->user_id = $user->id;
            $notif->title = "Email anda berhasil diperbarui";
            $notif->description = "Selamat!, email anda sebelum nya. " . $user->email . " berhasil diperbarui menjadi " . $updateEmail->email  . " tetap jaga keamanan akun anda";
            $notif->type = 'profile';
            $notif->read = 'belum';
            $notif->save();
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

        if ($user->verified_privy == 'y') {
            return response()->json([
                'status' => 200,
                'message' => 'Data anda sudah terverified, tidak bisa mengubah kembali data yg sudah terverifikasi'
            ], 200);
        } else if ($user->update_data_privy == 1) {
            return response()->json([
                'status' => 200,
                'message' => 'Data anda sudah terbaru, silahkan melanjutkan aktivitas pinjaman payroll untuk memferifikasi data anda'
            ], 200);
        } else {
            $user->name = $req->name;
            $user->nama_lengkap = $req->nama_lengkap;
            $user->dob = $req->dob;
            $user->nik = $req->nik;
            $user->update_data_privy = '1';
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Profile Updated'
            ], 200);
        }
    }

    public function profileUpdateLiveness(UpdateProfileSelfieKTPKKRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $base64selfie = $req->selfie;
        $output_file_selfie =  $user->nik . '_selfie_' . $user->id . "_" . rand(1000, 9999) . '.jpg';
        if (strpos($base64selfie, 'data:image/') === 0) {
            $parts = explode(';base64,', $base64selfie);
            if (isset($parts[1])) {
                $base64selfie = $parts[1];
            }
        }
        Storage::disk('local')->put('assets/users/' . $output_file_selfie, base64_decode($base64selfie));

        $base64ktp = $req->identity;
        $output_file_ktp =  $user->nik . '_ktp_' . $user->id . "_" . rand(1000, 9999) . '.jpg';
        if (strpos($base64ktp, 'data:image/') === 0) {
            $parts = explode(';base64,', $base64ktp);
            if (isset($parts[1])) {
                $base64ktp = $parts[1];
            }
        }
        Storage::disk('local')->put('assets/users/' . $output_file_ktp, base64_decode($base64ktp));

        if (isset($req->kartu_keluarga)) {
            $base64kk = $req->kartu_keluarga;
            $output_file_kk =  $user->nik . '_kk_' . $user->id . "_" . rand(1000, 9999) . '.jpg';
            if (strpos($base64kk, 'data:image/') === 0) {
                $parts = explode(';base64,', $base64kk);
                if (isset($parts[1])) {
                    $base64kk = $parts[1];
                }
            }
            Storage::disk('local')->put('assets/users/' . $output_file_kk, base64_decode($base64kk));
            $user->kartu_keluarga = $output_file_kk;
        }

        $user->selfie = $output_file_selfie;
        $user->ktp = $output_file_ktp;

        return response()->json([
            'status' => 200,
            'message' => 'Liveness berhasil'
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
                'nama' => $email->nama,
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
        $verif_whatsapp = verif_whatsapp::where('code_rand', $req->otp)
            ->where('id_user', auth('api')->user()->id)
            ->first();

        if (isset($verif_whatsapp)) {
            if ($verif_whatsapp->expired > Carbon::now()) {
                $user = User::where('id', $verif_whatsapp->id_user)
                    ->first();
                $user->whatsapp_verified_at = Carbon::now();
                $user->save();
                $verif_whatsapp->delete();
                if ($user->email_verified_at == null) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil verifikasi whatsapp, silahkan lanjutkan untuk verifikasi email yang dikirim ke ' . $user->email,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil verifikasi whatsapp',
                    ], 200);
                }
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

    public function verifOtpEmail(Request $req)
    {
        $verifEmail = verif_email::where('code_rand', $req->otp)
            ->where('id_user', auth('api')->user()->id)
            ->first();

        if (isset($verifEmail)) {
            if ($verifEmail->expired > Carbon::now()) {
                $user = User::where('id', $verifEmail->id_user)
                    ->first();
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
                    'message' => 'Berhasil verifikasi email',
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
        $user = User::where('id', auth('api')->user()->id)->first();

        if ($user->email_verified_at == null) {
            $verifEmail = verif_whatsapp::where('id_user', $user->id)->first();
            if ($verifEmail !== null) {
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);;
                $verifEmail->expired = Carbon::now()->addMinute(15);
            } else {
                $verifEmail = new verif_whatsapp();
                $verifEmail->id_user = $user->id;
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
                'message' => 'OTP berhasil dikirim ke ' . $user->nohp,
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Whatsapp telah terverifikasi',
            ], 200);
        }
    }

    public function kirimUlangOtpEmail(Request $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();

        if ($user->email_verified_at == null) {
            $verifEmail = verif_email::where('id_user', $user->id)->first();
            if ($verifEmail !== null) {
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            } else {
                $verifEmail = new verif_email();
                $verifEmail->id_user = $user->id;
                $verifEmail->code_rand = rand(1000, 9999);
                $verifEmail->expired = Carbon::now()->addMinute(60);
            }
            $verifEmail->save();
            $data = [
                'subject' => 'Selangkah Lagi! Aktifkan Akun TOMI Anda',
                'title' => 'Aktifkan Akun Anda Sekarang',
                'to' => $user->email,
                'token' => $verifEmail->code_rand,
                'nama' => $user->nama,
            ];

            Mail::to($user->email)->send(new VerifikasiEmail($data));

            return response()->json([
                'status' => 200,
                'message' => 'OTP Berhasil dikirim ke email ' . $user->email,
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Email telah terverifikasi',
            ], 200);
        }
    }

    public function preview(Request $req)
    {
        $filename = $req->query('filename');
        // Validasi nama file agar tidak bisa path traversal
        if (Str::contains($filename, ['..', '/', '\\'])) {
            abort(403, 'Invalid filename.');
        }

        $path = storage_path('app/assets/users/' . $filename);
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $mime = mime_content_type($path);
        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
        ]);
    }
}
