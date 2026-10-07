<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\cost;
use App\Models\privy;
use App\Models\PrivyDigitalIdSession;
use App\Models\privy_pse;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrivyController extends Controller
{
    public function registerPrivy()
    {
        // 
        $user = User::where('id', auth('api')->user()->id)
            ->with('kyc')
            ->first();

        if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->kyc->selfie) && isset($user->kyc->ktp)) {
            if ($user->register_token_privy == null) {
                // register privy
                $privy = privy::first();
                $login = loginPrivy();
                $uri = $privy->url . '/web/api/v2/register';
                $referrence = "TOMI0000000" . $user->id . rand(100, 000);
                $client = new Client(['verify' => false]);
                $requestBody = [
                    "reference_number" => $referrence,
                    "channel_id" => "Toko_Mikro001",
                    "email" => $user->email,
                    "phone" =>  $user->nohp,
                    "nik" =>  $user->nik,
                    "name" => $user->nama_lengkap,
                    "dob" =>  $user->dob,
                    "selfie" =>  encodeBase64('image', $user->kyc->selfie),
                    "identity" =>  encodeBase64('image', $user->kyc->ktp),
                ];

                $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

                $headers = [
                    'timestamp' => $signature['timestamp'],
                    'Authorization' => 'Bearer ' . $login['token'],
                    'Signature' => $signature['signature'],
                    'Request-ID' => $user->id,
                    'Content-Type' => 'application/json',
                    'Connection' => 'keep-alive',
                    'Accept' => 'application/json',
                ];

                $response = $client->request('POST', $uri, [
                    'headers' => $headers,
                    'json' => $requestBody,
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode == 201) {
                    $responseBody = $response->getBody()->getContents();
                    $response = json_decode($responseBody, true);
                    if ($response['data']['status'] == "waiting_verification") {
                        $registerToken = $response['data']['register_token'];
                        $user->register_token_privy = $registerToken;
                        $user->reference_number_privy = $referrence;
                        $user->save();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Registrasi berhasil, Harap menunggu hasil verifikasi data anda',
                        ], 200);
                    } else if ($response['data']['status'] == "verified" || $response['data']['status'] == "registered") {
                        $registerToken = $response['data']['register_token'];
                        $user->verified_privy = 'y';
                        $user->register_token_privy = $registerToken;
                        $user->reference_number_privy = $referrence;
                        $user->save();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Registrasi berhasil, selamat data anda terverifikasi',
                        ], 200);
                    } else if ($response['data']['status'] == "rejected") {


                        $privy = privy::first();
                        $login = loginPrivy();
                        $uri = $privy->url . '/web/api/v2/register/status';
                        $client = new Client(['verify' => false]);
                        $requestBody = [
                            "reference_number" => $referrence,
                            "channel_id" => "Toko_Mikro001",
                            "register_token" => $response['data']['register_token'],
                            "info" => ""
                        ];

                        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

                        $headers = [
                            'timestamp' => $signature['timestamp'],
                            'Authorization' => 'Bearer ' . $login['token'],
                            'Signature' => $signature['signature'],
                            'Request-ID' => $user->id,
                            'Content-Type' => 'application/json',
                            'Connection' => 'keep-alive',
                            'Accept' => 'application/json',
                        ];

                        $responseReject = $client->request('POST', $uri, [
                            'headers' => $headers,
                            'json' => $requestBody,
                        ]);

                        $responseBodyReject = $responseReject->getBody()->getContents();
                        $responseReject = json_decode($responseBodyReject, true);

                        $registerToken = $responseReject['data']['register_token'];
                        $user->verified_privy = 'n';
                        $user->register_token_privy = $registerToken;
                        $user->reference_number_privy = $referrence;
                        $user->reason_privy = $responseReject['data']['reject_reason'];
                        $user->resend_privy = $responseReject['data']['resend'] == true ? 1 : 0;
                        $user->save();

                        return response()->json([
                            'status' => 400,
                            'message' => $responseReject['data']['reject_reason']['reason'],
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Ada kendala saat melakukan verifikasi data anda',
                    ], 400);
                }
            } else {
                if ($user->resend_privy == 1 && $user->update_data_privy == 0) {
                    $reason = json_decode($user->reason_privy);
                    return response()->json([
                        'status' => 400,
                        'reason' => $reason->reason,
                        'message' => $reason->reason . ' Harap perbarui kembali data anda dan upload dokumen pendukung Kartu Keluarga'

                    ], 400);
                } elseif ($user->resend_privy == 1 && $user->update_data_privy == 1) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'hit endpoint resend register'
                    ], 400);
                } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {
                    $reason = json_decode($user->reason_privy);
                    return response()->json([
                        'status' => 400,
                        'reason' => $reason->reason,
                        'message' => $reason->reason . ' Harap hubungi customer service untuk merubah data anda'
                    ], 400);
                } elseif ($user->verified_privy == "y") {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Anda sudah terverifikasi'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Harap menunggu, data anda sedang proses verifikasi'
                    ], 400);
                }
            }
        }
    }

    public function resendRegister()
    {
        // register privy
        $user = User::where('id', auth('api')->user()->id)
            ->with('kyc')
            ->first();
        if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->kyc->selfie) && isset($user->kyc->ktp) && isset($user->kartu_keluarga) && ($user->update_data_privy == true)) {
            $privy = privy::first();
            $login = loginPrivy();
            $uri = $privy->url . '/web/api/v2/register/resend';

            $client = new Client(['verify' => false]);
            $requestBody = [
                "reference_number" => "TOMI0000000" . $user->id,
                "channel_id" => "Toko_Mikro001",
                "email" => $user->email,
                "phone" =>  $user->nohp,
                "nik" =>  $user->nik,
                "name" => $user->nama_lengkap,
                "dob" =>  $user->dob,
                "selfie" =>  encodeBase64('image', $user->kyc->selfie),
                "identity" =>  encodeBase64('image', $user->kyc->ktp),
                "supporting_document" => [
                    [
                        "document_name" => "Kartu Keluarga",
                        "document"      => encodeBase64('image', $user->kartu_keluarga)
                    ]
                ]
            ];

            $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

            $headers = [
                'timestamp' => $signature['timestamp'],
                'Authorization' => 'Bearer ' . $login['token'],
                'Signature' => $signature['signature'],
                'Request-ID' => $user->id,
                'Content-Type' => 'application/json',
                'Connection' => 'keep-alive',
                'Accept' => 'application/json',
            ];

            $response = $client->request('POST', $uri, [
                'headers' => $headers,
                'json' => $requestBody,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 201) {
                $responseBody = $response->getBody()->getContents();
                $response = json_decode($responseBody, true);
                $registerToken = $response['data']['register_token'];

                $user->register_token_privy = $registerToken;
                $user->reference_number_privy = "TOMI0000000" . $user->id;
                $user->save();
                return response()->json([
                    'status' => 400,
                    'message' => 'Harap menunggu verifikasi data anda',
                ], 400);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Ada kendala saat melakukan verifikasi data anda',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Harap perbarui kembali data anda dan upload dokumen pendukung Kartu Keluarga'
            ], 400);
        }
    }

    public function cekStatusPrivy()
    {
        // {{base_url}}/web/api/v2/register/status
        $user = User::where('id', auth('api')->user()->id)->first();
        $privy = privy::first();
        $login = loginPrivy();
        $uri = $privy->url . '/web/api/v2/register/status';
        $referrence = "TOMI00000005340";
        //  "reference_number" => $user->reference_number_privy,
        //             "channel_id" => "Toko_Mikro001",
        //             "register_token" => $user->register_token_privy,
        //             "info" => ""
        $client = new Client(['verify' => false]);
        $requestBody = [
            "reference_number" => "TOMI00000005340",
            "channel_id" => "Toko_Mikro001",
            "register_token" => "04c8a4639b310f5837febd0f7c581209",
            "info" => ""
        ];

        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

        $headers = [
            'timestamp' => $signature['timestamp'],
            'Authorization' => 'Bearer ' . $login['token'],
            'Signature' => $signature['signature'],
            'Request-ID' => $user->id,
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];

        $response = $client->request('POST', $uri, [
            'headers' => $headers,
            'json' => $requestBody,
        ]);

        $responseBody = $response->getBody()->getContents();

        dd($responseBody);
    }

    public function digitalIdSession(Request $request)
    {
        $user = User::where('id', auth('api')->user()->id)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Sesi login berakhir, silakan masuk kembali.',
            ], 401);
        }

        if (in_array($user->verified_privy, ['y', '1', 1, true], true) && ! empty($user->id_privy)) {
            return response()->json([
                'status' => false,
                'message' => 'Akun Anda sudah terverifikasi Privy.',
            ], 409);
        }

        if (empty($user->nik) || empty($user->nama_lengkap) || empty($user->dob) || empty($user->email) || empty($user->nohp)) {
            return response()->json([
                'status' => false,
                'message' => 'Lengkapi data profil terlebih dahulu.',
            ], 422);
        }

        $privy = privy::first();

        if (! $privy) {
            return response()->json([
                'status' => false,
                'message' => 'Layanan Privy sedang tidak dapat diakses.',
            ], 502);
        }

        $login = loginPrivy();
        $uri = rtrim(config('services.privy_digital_id.api_base_url'), '/') . '/web/api/v2/digital-id/session';
        $client = new Client(['verify' => false]);
        $referenceNumber = 'TOMIDIGITALID' . $user->id . now('Asia/Jakarta')->format('YmdHis');
        $requestBody = [
            'reference_number' => $referenceNumber,
            'channel_id' => 'Toko_Mikro001',
            'email' => $user->email,
            'phone' => $user->nohp,
            'nik' => $user->nik,
            'name' => $user->nama_lengkap,
            'dob' => $user->dob,
            'info' => '',
        ];

        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

        $headers = [
            'timestamp' => $signature['timestamp'],
            'Authorization' => 'Bearer ' . $login['token'],
            'Signature' => $signature['signature'],
            'Request-ID' => $user->id,
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];

        try {
            $response = $client->request('POST', $uri, [
                'headers' => $headers,
                'json' => $requestBody,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $sessionUrl = $responseBody['data']['session_url'] ?? $responseBody['session_url'] ?? null;
            $expiresAt = $responseBody['data']['expires_at']
                ?? $responseBody['data']['expired_at']
                ?? $responseBody['expires_at']
                ?? $responseBody['expired_at']
                ?? null;
            $privySessionId = $responseBody['data']['session_id']
                ?? $responseBody['data']['id']
                ?? $responseBody['session_id']
                ?? $responseBody['id']
                ?? null;

            if (! $sessionUrl) {
                Log::warning('Privy Digital ID session response missing session_url', [
                    'user_id' => $user->id,
                    'reference_number' => $referenceNumber,
                    'privy_status' => $response->getStatusCode(),
                    'privy_message' => $responseBody['message'] ?? null,
                ]);

                $debug = [];
                if (config('app.debug')) {
                    $debug = [
                        'privy_status' => $response->getStatusCode(),
                        'privy_message' => $responseBody['message'] ?? null,
                        'privy_data_keys' => isset($responseBody['data']) && is_array($responseBody['data'])
                            ? array_keys($responseBody['data'])
                            : [],
                    ];
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Layanan Privy sedang tidak dapat diakses.',
                    'data' => $responseBody['data'] ?? null,
                ] + ($debug ? ['debug' => $debug] : []), 502);
            }

            PrivyDigitalIdSession::create([
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'privy_session_id' => $privySessionId,
                'session_url' => $sessionUrl,
                'status' => 'pending',
                'expires_at' => $expiresAt ? Carbon::parse($expiresAt)->toDateTimeString() : null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Sesi verifikasi Privy berhasil dibuat.',
                'data' => [
                    'session_url' => $sessionUrl,
                    'expires_at' => $expiresAt,
                ],
            ], 200);
        } catch (GuzzleException $e) {
            $hasResponse = $e instanceof RequestException && $e->hasResponse();
            $statusCode = $hasResponse ? $e->getResponse()->getStatusCode() : 502;
            $responseBody = $hasResponse
                ? json_decode($e->getResponse()->getBody()->getContents(), true)
                : null;
            $message = match ($statusCode) {
                401 => 'Sesi login berakhir, silakan masuk kembali.',
                409 => 'Akun Anda sudah terverifikasi Privy.',
                422 => 'Lengkapi data profil terlebih dahulu.',
                429 => 'Terlalu banyak percobaan, coba lagi dalam beberapa menit.',
                default => 'Layanan Privy sedang tidak dapat diakses.',
            };

            Log::warning('Privy Digital ID session request failed', [
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'privy_status' => $statusCode,
                'privy_message' => $responseBody['message'] ?? null,
                'error' => $e->getMessage(),
            ]);

            if ($statusCode === 404 || $statusCode >= 500) {
                $statusCode = 502;
                $message = 'Layanan Privy sedang tidak dapat diakses.';
            }

            $debug = [];
            if (config('app.debug')) {
                $debug = [
                    'privy_status' => $hasResponse ? $e->getResponse()->getStatusCode() : null,
                    'privy_message' => $responseBody['message'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json([
                'status' => false,
                'message' => $message,
            ] + ($debug ? ['debug' => $debug] : []), $statusCode);
        }
    }

    public function digitalIdExchange(Request $request)
    {
        $exchangeCode = trim((string) $request->input('exchange_code'));

        if ($exchangeCode === '') {
            return response()->json([
                'status' => false,
                'message' => 'Kode verifikasi tidak valid.',
            ], 400);
        }

        $user = User::where('id', auth('api')->user()->id)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Sesi login berakhir, silakan masuk kembali.',
            ], 401);
        }

        $verifiedSession = PrivyDigitalIdSession::where('exchange_code', $exchangeCode)
            ->where('user_id', $user->id)
            ->where('status', 'verified')
            ->latest()
            ->first();

        if ($verifiedSession && in_array($user->verified_privy, ['y', '1', 1, true], true)) {
            return response()->json([
                'status' => true,
                'message' => 'Verifikasi Privy berhasil.',
                'data' => [
                    'status_privy' => 'y',
                    'privy_id' => $user->id_privy,
                ],
            ], 200);
        }

        $usedByOtherUser = PrivyDigitalIdSession::where('exchange_code', $exchangeCode)
            ->where('user_id', '!=', $user->id)
            ->exists();

        if ($usedByOtherUser) {
            return response()->json([
                'status' => false,
                'message' => 'Kode verifikasi tidak sesuai dengan akun Anda.',
            ], 403);
        }

        $digitalIdSession = PrivyDigitalIdSession::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        if (! $digitalIdSession) {
            return response()->json([
                'status' => false,
                'message' => 'Sesi telah berakhir, silakan coba lagi.',
            ], 410);
        }

        $privy = privy::first();

        if (! $privy) {
            return response()->json([
                'status' => false,
                'message' => 'Layanan Privy sedang tidak dapat diakses.',
            ], 502);
        }

        $login = loginPrivy();
        $uri = rtrim(config('services.privy_digital_id.api_base_url'), '/') . '/web/api/v2/digital-id/exchange';
        $client = new Client(['verify' => false]);
        $requestBody = [
            'reference_number' => $digitalIdSession->reference_number,
            'channel_id' => 'Toko_Mikro001',
            'exchange_code' => $exchangeCode,
            'info' => '',
        ];

        if (! empty($digitalIdSession->privy_session_id)) {
            $requestBody['session_id'] = $digitalIdSession->privy_session_id;
        }

        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

        $headers = [
            'timestamp' => $signature['timestamp'],
            'Authorization' => 'Bearer ' . $login['token'],
            'Signature' => $signature['signature'],
            'Request-ID' => $user->id,
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];

        try {
            $response = $client->request('POST', $uri, [
                'headers' => $headers,
                'json' => $requestBody,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data = $responseBody['data'] ?? [];
            $privyId = $data['privy_id'] ?? $data['id_privy'] ?? $data['id_user'] ?? null;
            $enterpriseToken = $data['enterprise_token'] ?? $data['enterpriseToken'] ?? null;
            $status = $data['status'] ?? $responseBody['status'] ?? null;

            if (! $privyId) {
                $digitalIdSession->exchange_code = $exchangeCode;
                $digitalIdSession->status = 'failed';
                $digitalIdSession->exchanged_at = now();
                $digitalIdSession->save();

                return response()->json([
                    'status' => false,
                    'message' => $data['message'] ?? $responseBody['message'] ?? 'Verifikasi Privy gagal.',
                ], 422);
            }

            if ($privyId) {
                $wasVerified = $user->verified_privy == 'y';

                $user->id_privy = $privyId;
                $user->enterprise_token_privy = $enterpriseToken ?? $user->enterprise_token_privy;
                $user->reason_privy = null;
                $user->resend_privy = null;
                $user->verified_privy = 'y';
                $user->save();
                $digitalIdSession->exchange_code = $exchangeCode;
                $digitalIdSession->status = 'verified';
                $digitalIdSession->exchanged_at = now();
                $digitalIdSession->save();

                if (! $wasVerified) {
                    $cost = cost::first();

                    if ($cost) {
                        $newPSEPrivy = new privy_pse();
                        $newPSEPrivy->user_id = $user->id;
                        $newPSEPrivy->cost = $cost->biaya;
                        $newPSEPrivy->keterangan = 'Penerbitan sertifikat elektronik';
                        $newPSEPrivy->save();
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Verifikasi Privy berhasil.',
                'data' => [
                    'status_privy' => 'y',
                    'privy_id' => $privyId,
                ],
            ], 200);
        } catch (GuzzleException $e) {
            $hasResponse = $e instanceof RequestException && $e->hasResponse();
            $statusCode = $hasResponse ? $e->getResponse()->getStatusCode() : 502;
            $responseBody = $hasResponse
                ? json_decode($e->getResponse()->getBody()->getContents(), true)
                : null;
            $message = match ($statusCode) {
                400 => 'Kode verifikasi tidak valid.',
                401 => 'Sesi login berakhir, silakan masuk kembali.',
                403 => 'Kode verifikasi tidak sesuai dengan akun Anda.',
                409 => 'Kode verifikasi sudah digunakan.',
                410 => 'Sesi telah berakhir, silakan coba lagi.',
                422 => $responseBody['message'] ?? $responseBody['data']['message'] ?? 'Verifikasi Privy gagal.',
                default => 'Layanan Privy sedang tidak dapat diakses.',
            };

            if ($statusCode === 404 || $statusCode >= 500) {
                $statusCode = 502;
                $message = 'Layanan Privy sedang tidak dapat diakses.';
            }

            if (! in_array($statusCode, [409], true)) {
                $digitalIdSession->exchange_code = $exchangeCode;
                $digitalIdSession->status = 'failed';
                $digitalIdSession->exchanged_at = now();
                $digitalIdSession->save();
            }

            return response()->json([
                'status' => false,
                'message' => $message,
            ], $statusCode);
        }
    }
}
