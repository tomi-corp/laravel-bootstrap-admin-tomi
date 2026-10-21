<?php

namespace App\Http\Controllers\User;

use App\Exceptions\PrivyDigitalIdException;
use App\Http\Controllers\Controller;
use App\Models\cost;
use App\Models\privy;
use App\Models\privy_pse;
use App\Models\PrivyDigitalIdSession;
use App\Models\User;
use App\Services\PrivyDigitalIdClient;
use App\Services\PrivyDigitalIdService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PrivyController extends Controller
{
    private const TOKENLESS_FAILURE_REASONS = [
        'cancelled',
        'user_back',
        'consent_denied',
        'too_many_attempts',
        'session_expired',
        'liveness_blocked',
        'fallback_manual',
        'error',
    ];

    private const TOKENLESS_FAILURE_MESSAGES = [
        'cancelled' => 'Verifikasi identitas dibatalkan oleh pengguna.',
        'user_back' => 'Pengguna kembali sebelum verifikasi identitas selesai.',
        'consent_denied' => 'Persetujuan untuk verifikasi identitas ditolak.',
        'too_many_attempts' => 'Batas percobaan verifikasi identitas telah tercapai.',
        'session_expired' => 'Sesi verifikasi identitas telah kedaluwarsa.',
        'liveness_blocked' => 'Verifikasi liveness diblokir karena pemeriksaan tidak memenuhi syarat.',
        'fallback_manual' => 'Verifikasi identitas dialihkan ke proses manual.',
        'error' => 'Terjadi kesalahan saat verifikasi identitas.',
    ];

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
                $uri = $privy->url.'/web/api/v2/register';
                $referrence = 'TOMI0000000'.$user->id.rand(100, 000);
                $client = new Client(['verify' => false]);
                $requestBody = [
                    'reference_number' => $referrence,
                    'channel_id' => 'Toko_Mikro001',
                    'email' => $user->email,
                    'phone' => $user->nohp,
                    'nik' => $user->nik,
                    'name' => $user->nama_lengkap,
                    'dob' => $user->dob,
                    'selfie' => encodeBase64('image', $user->kyc->selfie),
                    'identity' => encodeBase64('image', $user->kyc->ktp),
                ];

                $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

                $headers = [
                    'timestamp' => $signature['timestamp'],
                    'Authorization' => 'Bearer '.$login['token'],
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
                    if ($response['data']['status'] == 'waiting_verification') {
                        $registerToken = $response['data']['register_token'];
                        $user->register_token_privy = $registerToken;
                        $user->reference_number_privy = $referrence;
                        $user->save();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Registrasi berhasil, Harap menunggu hasil verifikasi data anda',
                        ], 200);
                    } elseif ($response['data']['status'] == 'verified' || $response['data']['status'] == 'registered') {
                        $registerToken = $response['data']['register_token'];
                        $user->verified_privy = 'y';
                        $user->register_token_privy = $registerToken;
                        $user->reference_number_privy = $referrence;
                        $user->save();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Registrasi berhasil, selamat data anda terverifikasi',
                        ], 200);
                    } elseif ($response['data']['status'] == 'rejected') {

                        $privy = privy::first();
                        $login = loginPrivy();
                        $uri = $privy->url.'/web/api/v2/register/status';
                        $client = new Client(['verify' => false]);
                        $requestBody = [
                            'reference_number' => $referrence,
                            'channel_id' => 'Toko_Mikro001',
                            'register_token' => $response['data']['register_token'],
                            'info' => '',
                        ];

                        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

                        $headers = [
                            'timestamp' => $signature['timestamp'],
                            'Authorization' => 'Bearer '.$login['token'],
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
                        'message' => $reason->reason.' Harap perbarui kembali data anda dan upload dokumen pendukung Kartu Keluarga',

                    ], 400);
                } elseif ($user->resend_privy == 1 && $user->update_data_privy == 1) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'hit endpoint resend register',
                    ], 400);
                } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {
                    $reason = json_decode($user->reason_privy);

                    return response()->json([
                        'status' => 400,
                        'reason' => $reason->reason,
                        'message' => $reason->reason.' Harap hubungi customer service untuk merubah data anda',
                    ], 400);
                } elseif ($user->verified_privy == 'y') {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Anda sudah terverifikasi',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Harap menunggu, data anda sedang proses verifikasi',
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
            $uri = $privy->url.'/web/api/v2/register/resend';

            $client = new Client(['verify' => false]);
            $requestBody = [
                'reference_number' => 'TOMI0000000'.$user->id,
                'channel_id' => 'Toko_Mikro001',
                'email' => $user->email,
                'phone' => $user->nohp,
                'nik' => $user->nik,
                'name' => $user->nama_lengkap,
                'dob' => $user->dob,
                'selfie' => encodeBase64('image', $user->kyc->selfie),
                'identity' => encodeBase64('image', $user->kyc->ktp),
                'supporting_document' => [
                    [
                        'document_name' => 'Kartu Keluarga',
                        'document' => encodeBase64('image', $user->kartu_keluarga),
                    ],
                ],
            ];

            $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

            $headers = [
                'timestamp' => $signature['timestamp'],
                'Authorization' => 'Bearer '.$login['token'],
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
                $user->reference_number_privy = 'TOMI0000000'.$user->id;
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
                'message' => 'Harap perbarui kembali data anda dan upload dokumen pendukung Kartu Keluarga',
            ], 400);
        }
    }

    public function cekStatusPrivy()
    {
        // {{base_url}}/web/api/v2/register/status
        $user = User::where('id', auth('api')->user()->id)->first();
        $privy = privy::first();
        $login = loginPrivy();
        $uri = $privy->url.'/web/api/v2/register/status';
        $referrence = 'TOMI00000005340';
        //  "reference_number" => $user->reference_number_privy,
        //             "channel_id" => "Toko_Mikro001",
        //             "register_token" => $user->register_token_privy,
        //             "info" => ""
        $client = new Client(['verify' => false]);
        $requestBody = [
            'reference_number' => 'TOMI00000005340',
            'channel_id' => 'Toko_Mikro001',
            'register_token' => '04c8a4639b310f5837febd0f7c581209',
            'info' => '',
        ];

        $signature = signature($requestBody, $privy->username, $privy->password, 'POST');

        $headers = [
            'timestamp' => $signature['timestamp'],
            'Authorization' => 'Bearer '.$login['token'],
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
        $authUser = auth('api')->user();
        $userId = $authUser?->id ?? $request->input('id_borrower');
        $user = $userId ? User::where('id', $userId)->first() : null;

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'id_borrower wajib diisi',
                'code' => 'invalid_request',
                'data' => [],
            ], 400);
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

        $referenceNumber = 'TOMIDIGITALID'.$user->id.now('Asia/Jakarta')->format('YmdHis');

        try {
            $session = app(PrivyDigitalIdClient::class)->createSession();

            PrivyDigitalIdSession::create([
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'privy_session_id' => $session['session_id'],
                'session_url' => $session['session_url'],
                'status' => 'created',
                'expires_at' => $session['expires_at'] ? Carbon::parse($session['expires_at'])->toDateTimeString() : null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Sesi verifikasi Privy berhasil dibuat.',
                'data' => [
                    'session_id' => $session['session_id'],
                    'session_url' => $session['session_url'],
                    'expires_at' => $session['expires_at'],
                ],
            ], 200);
        } catch (PrivyDigitalIdException $e) {
            Log::warning('Privy Digital ID session request failed', [
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'code' => $e->errorCode,
                'privy_status' => $e->privyStatus,
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => $e->errorCode,
            ], $e->httpStatus);
        }
    }

    public function digitalIdExchange(Request $request)
    {
        $exchangeCode = trim((string) ($request->input('exchange_code') ?? $request->input('code')));

        if ($exchangeCode === '') {
            return response()->json([
                'status' => false,
                'message' => 'Kode verifikasi tidak valid.',
            ], 400);
        }

        $authUser = auth('api')->user();
        $userId = $authUser?->id ?? $request->input('id_borrower');
        $user = $userId ? User::where('id', $userId)->first() : null;

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'id_borrower wajib diisi',
                'code' => 'invalid_request',
                'data' => [],
            ], 400);
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
                    'session_id' => $verifiedSession->privy_session_id,
                    'status_privy' => 'y',
                    'privy_id' => $user->id_privy,
                ],
            ], 200);
        }

        $usedByOtherUser = PrivyDigitalIdSession::withTrashed()->where('exchange_code', $exchangeCode)
            ->where('user_id', '!=', $user->id)
            ->exists();

        if ($usedByOtherUser) {
            return response()->json([
                'status' => false,
                'message' => 'Kode verifikasi tidak sesuai dengan akun Anda.',
            ], 403);
        }

        $digitalIdSession = PrivyDigitalIdSession::where('user_id', $user->id)
            ->whereIn('status', ['created', 'pending', 'failed', 'processing'])
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

        try {
            $exchanged = app(PrivyDigitalIdClient::class)->exchangeCode($exchangeCode);

            if ($digitalIdSession->privy_session_id && $digitalIdSession->privy_session_id !== $exchanged['session_id']) {
                Log::warning('Privy Digital ID exchange session mismatch', [
                    'user_id' => $user->id,
                    'local_session_id' => $digitalIdSession->privy_session_id,
                    'privy_session_id' => $exchanged['session_id'],
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Kode verifikasi tidak sesuai dengan sesi Anda.',
                    'code' => 'session_forbidden',
                ], 403);
            }

            app(PrivyDigitalIdService::class)->markReceived($exchanged['session_id'], 'exchange');
            $digitalIdSession->exchange_code = $exchangeCode;
            $digitalIdSession->exchanged_at = now();
            $digitalIdSession->save();

            $result = $this->processDigitalIdResult($digitalIdSession, $exchanged['token'], 'exchange', $exchanged['event']);

            if ($result['status'] === 'PROCESSING') {
                return response()->json([
                    'status' => true,
                    'message' => 'Hasil verifikasi sedang diproses.',
                    'data' => [
                        'session_id' => $exchanged['session_id'],
                        'status' => $result['status'],
                    ],
                ], 202);
            }

            return response()->json([
                'status' => $result['status'] === 'VERIFIED',
                'message' => $result['status'] === 'VERIFIED'
                    ? 'Verifikasi identitas berhasil.'
                    : 'Verifikasi identitas: '.$result['status'],
                'data' => [
                    'session_id' => $exchanged['session_id'],
                    'status' => $result['status'],
                    'privy_id' => $result['privy_id'],
                ],
            ], $result['status'] === 'VERIFIED' ? 200 : 422);
        } catch (PrivyDigitalIdException $e) {
            if (! in_array($e->httpStatus, [409], true)) {
                $digitalIdSession->exchange_code = $exchangeCode;
                $digitalIdSession->status = 'failed';
                $digitalIdSession->exchanged_at = now();
                $digitalIdSession->error_code = $e->errorCode;
                $digitalIdSession->error_message = substr($e->getMessage(), 0, 255);
                $digitalIdSession->save();
            }

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => $e->errorCode,
            ], $e->httpStatus);
        }
    }

    public function digitalIdStatus(Request $request)
    {
        $sessionId = trim((string) $request->input('session_id'));

        if ($sessionId === '' || strlen($sessionId) > 100) {
            return response()->json([
                'status' => false,
                'message' => 'session_id wajib diisi',
                'code' => 'invalid_request',
                'data' => [],
            ], 400);
        }

        $user = User::where('id', auth('api')->user()->id)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Sesi login berakhir, silakan masuk kembali.',
            ], 401);
        }

        $session = PrivyDigitalIdSession::where('privy_session_id', $sessionId)
            ->orWhere('id', $sessionId)
            ->latest()
            ->first();

        if (! $session) {
            return response()->json([
                'status' => false,
                'message' => 'Sesi verifikasi tidak dikenal',
                'code' => 'session_unknown',
                'data' => [],
            ], 404);
        }

        if ((int) $session->user_id !== (int) $user->id) {
            Log::warning('Privy Digital ID status borrower mismatch', [
                'session_id' => $sessionId,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Sesi verifikasi bukan milik borrower ini',
                'code' => 'session_forbidden',
                'data' => [],
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Status sesi verifikasi.',
            'data' => [
                'session_id' => $session->privy_session_id,
                'status' => strtoupper($session->status),
                'privy_id' => $session->status === 'verified' ? $user->id_privy : null,
                'error_code' => $session->status === 'failed' ? $session->error_code : null,
                'error_message' => $session->status === 'failed' ? $session->error_message : null,
                'expires_at' => $session->expires_at,
            ],
        ], 200);
    }

    // public function digitalIdCallback(Request $request)
    // {
    //     $sessionId = trim((string) $request->input('session_id'));
    //     $token = $request->input('token');
    //     $event = is_string($request->input('event')) ? strtolower(trim($request->input('event'))) : '';

    //     if ((! is_string($token) || $token === '') && $sessionId !== '' && $this->isTokenlessFailureEvent($event)) {
    //         return $this->handleTokenlessFailureCallback($request, $sessionId, $event);
    //     }

    //     if (! is_string($token) || $token === '') {
    //         Log::warning('Privy Digital ID callback invalid', [
    //             'has_session_id' => $sessionId !== '',
    //             'has_token' => is_string($token) && $token !== '',
    //         ]);

    //         return response()->json([
    //             'received' => false,
    //             'message' => 'token wajib diisi',
    //         ], 400);
    //     }

    //     try {
    //         $payload = app(PrivyDigitalIdClient::class)->verifyToken($token);
    //     } catch (PrivyDigitalIdException $e) {
    //         Log::warning('Privy Digital ID callback token invalid', [
    //             'code' => $e->errorCode,
    //             'http_status' => $e->httpStatus,
    //         ]);

    //         return response()->json([
    //             'received' => false,
    //             'message' => 'token callback tidak valid',
    //         ], 401);
    //     }

    //     $requestSessionId = $sessionId;
    //     $signedSessionId = $payload['session_id'] ?? $payload['data']['session_id'] ?? null;
    //     $sessionId = is_string($signedSessionId) ? trim($signedSessionId) : '';

    //     if ($sessionId === '') {
    //         Log::warning('Privy Digital ID callback invalid', [
    //             'has_session_id' => false,
    //             'has_token' => true,
    //         ]);

    //         return response()->json([
    //             'received' => false,
    //             'message' => 'session_id tidak ditemukan pada callback',
    //         ], 400);
    //     }

    //     if ($request->exists('session_id') && $requestSessionId !== $sessionId) {
    //         return response()->json([
    //             'received' => false,
    //             'message' => 'token callback tidak valid',
    //         ], 401);
    //     }

    //     $session = PrivyDigitalIdSession::where('privy_session_id', $sessionId)->latest()->first();

    //     if (! $session) {
    //         Log::warning('Privy Digital ID callback unknown session', [
    //             'session_id' => $sessionId,
    //             'event' => $request->input('event'),
    //         ]);

    //         return response()->json([
    //             'received' => true,
    //             'processed' => false,
    //         ], 200);
    //     }

    //     $session = app(PrivyDigitalIdService::class)->acceptCallback($session);

    //     if (! $session) {
    //         return response()->json([
    //             'received' => true,
    //             'processed' => false,
    //         ], 200);
    //     }

    //     $sharedData = $request->input('shared_data', []);
    //     if (is_string($sharedData)) {
    //         $decodedSharedData = json_decode($sharedData, true);
    //         $sharedData = is_array($decodedSharedData) ? $decodedSharedData : [];
    //     }

    //     $session->forceFill([
    //         'exchange_code' => $request->input('exchange_code') ?: $session->exchange_code,
    //         'callback_state' => $request->input('state'),
    //         'callback_token' => Crypt::encryptString($token),
    //         'callback_shared_data' => is_array($sharedData) ? $sharedData : [],
    //     ])->save();

    //     try {
    //         $this->processDigitalIdResult($session, $token, 'callback', $request->input('state') ?? $request->input('event'), $payload);
    //     } catch (PrivyDigitalIdException $e) {
    //         Log::warning('Privy Digital ID callback processing failed', [
    //             'session_id' => $sessionId,
    //             'code' => $e->errorCode,
    //             'http_status' => $e->httpStatus,
    //         ]);
    //     }

    //     return response()->json([
    //         'received' => true,
    //     ], 200);
    // }
    /**
     * POST /v1/api/privy/callback — dipanggil aplikasi TomiHub (dengan token login) dan server Privy.
     *
     * Berhasil      : { session_id, event: "identity.verified", token }
     * Tidak berhasil: { session_id, event: "identity.<alasan>" } tanpa token → cukup dicatat, balas 200.
     * Token diverifikasi sesuai dokumen v1.5 §12.2 (EdDSA, aud, iss, exp) lalu my_identity didekripsi (§12.3).
     * Proses idempoten per session_id: callback berulang memberi hasil yang sama.
     */
    public function digitalIdCallback(Request $request)
    {
        $sessionId = trim((string) $request->input('session_id'));
        $token = is_string($request->input('token')) ? trim($request->input('token')) : '';
        $event = is_string($request->input('event')) ? strtolower(trim($request->input('event'))) : '';

        // Callback dengan token tanpa event diperlakukan sebagai identity.verified
        if ($event === '' && $token !== '') {
            $event = 'identity.verified';
        }

        // 1. token wajib hanya untuk identity.verified (required_if:event,identity.verified)
        if ($event === 'identity.verified' && $token === '') {
            Log::warning('Privy Digital ID callback verified without token', [
                'session_id' => $sessionId,
                'event' => $event,
            ]);

            return $this->digitalIdCallbackResponse(false, 'token wajib diisi', 400);
        }

        // 2. Event tidak berhasil (tanpa token): catat status sesi, balas 200
        if ($token === '') {
            if ($sessionId === '') {
                return $this->digitalIdCallbackResponse(false, 'session_id wajib diisi', 400);
            }

            return $this->handleTokenlessFailureCallback($request, $sessionId, $event);
        }

        // 3. Verifikasi token
        try {
            $payload = app(PrivyDigitalIdClient::class)->verifyToken($token);
        } catch (PrivyDigitalIdException $e) {
            Log::warning('Privy Digital ID callback token invalid', [
                'session_id' => $sessionId,
                'code' => $e->errorCode,
                'http_status' => $e->httpStatus,
            ]);

            return $this->digitalIdCallbackResponse(false, 'token callback tidak valid', 401, ['error_code' => $e->errorCode]);
        }

        $requestSessionId = $sessionId;
        $signedSessionId = $payload['session_id'] ?? $payload['data']['session_id'] ?? null;
        $sessionId = is_string($signedSessionId) ? trim($signedSessionId) : '';

        if ($sessionId === '') {
            Log::warning('Privy Digital ID callback invalid: session_id missing in token');

            return $this->digitalIdCallbackResponse(false, 'session_id tidak ditemukan pada callback', 400);
        }

        // session_id di body harus sama dengan session_id yang ditandatangani Privy
        if ($request->exists('session_id') && $requestSessionId !== $sessionId) {
            return $this->digitalIdCallbackResponse(false, 'token callback tidak valid', 401);
        }

        $session = PrivyDigitalIdSession::where('privy_session_id', $sessionId)->latest()->first();

        if (! $session) {
            Log::warning('Privy Digital ID callback unknown session', [
                'session_id' => $sessionId,
                'event' => $event,
            ]);

            return response()->json([
                'status' => true,
                'received' => true,
                'processed' => false,
                'message' => 'Sesi tidak ditemukan, tetapi callback diterima',
            ], 200);
        }

        // 4. Bila dipanggil aplikasi (ada token login), sesi wajib milik nasabah yang login
        $authUser = $this->callbackAuthUser();

        if ($authUser && (int) $authUser->id !== (int) $session->user_id) {
            Log::warning('Privy Digital ID callback session owned by another user', [
                'session_id' => $sessionId,
                'auth_user_id' => $authUser->id,
            ]);

            return $this->digitalIdCallbackResponse(false, 'Sesi verifikasi bukan milik akun Anda.', 403);
        }

        $session = app(PrivyDigitalIdService::class)->acceptCallback($session);

        if (! $session) {
            return response()->json([
                'status' => true,
                'received' => true,
                'processed' => false,
                'message' => 'Callback sudah diproses sebelumnya',
            ], 200);
        }

        // 5. Idempoten: sesi yang sudah verified (lewat exchange / callback sebelumnya) tidak diproses ulang
        if ($session->status === 'verified') {
            return $this->digitalIdVerifiedResponse(User::find($session->user_id), $session->privy_id, 'Callback sudah diproses sebelumnya');
        }

        $sharedData = $request->input('shared_data', []);
        if (is_string($sharedData)) {
            $decodedSharedData = json_decode($sharedData, true);
            $sharedData = is_array($decodedSharedData) ? $decodedSharedData : [];
        }

        $session->forceFill([
            'exchange_code' => $request->input('exchange_code') ?: $session->exchange_code,
            'callback_state' => $request->input('state') ?: $event,
            'callback_token' => Crypt::encryptString($token),
            'callback_shared_data' => is_array($sharedData) ? $sharedData : [],
        ])->save();

        try {
            // 6. Verifikasi + dekripsi identitas, simpan users.verified_privy & users.id_privy
            $result = $this->processDigitalIdResult($session, $token, 'callback', $request->input('state') ?: $event, $payload);
        } catch (PrivyDigitalIdException $e) {
            Log::warning('Privy Digital ID callback processing failed', [
                'session_id' => $sessionId,
                'code' => $e->errorCode,
                'http_status' => $e->httpStatus,
            ]);

            return $this->digitalIdCallbackResponse(false, 'Gagal memproses hasil verifikasi digital ID', 500, ['error_code' => $e->errorCode]);
        }

        // Server Privy cukup menerima ack 200; aplikasi menerima status yang sebenarnya
        $failureStatus = $authUser ? 422 : 200;

        return match ($result['status'] ?? null) {
            'VERIFIED' => $this->digitalIdVerifiedResponse(User::find($session->user_id), $result['privy_id'] ?? null),
            'IDENTITY_MISMATCH' => $this->digitalIdCallbackResponse(
                false,
                'NIK pada identitas Privy tidak sesuai dengan data akun TomiHub Anda. Silakan hubungi Customer Service.',
                $failureStatus,
                ['privy_verified' => false, 'reason' => 'identity_mismatch'],
            ),
            'IDENTITY_CONFLICT' => $this->digitalIdCallbackResponse(
                false,
                'NIK pada akun Privy sudah terdaftar di akun TomiHub lain. Silakan hubungi Customer Service.',
                $failureStatus,
                ['privy_verified' => false, 'reason' => 'identity_conflict'],
            ),
            'FAILED' => $this->digitalIdCallbackResponse(
                false,
                'Verifikasi identitas tidak berhasil.',
                $failureStatus,
                ['privy_verified' => false, 'reason' => 'failed'],
            ),
            default => $this->digitalIdCallbackResponse(
                false,
                'Verifikasi identitas sedang diproses.',
                202,
                ['privy_verified' => false, 'reason' => 'processing'],
            ),
        };
    }

    private function digitalIdCallbackResponse(bool $ok, string $message, int $httpStatus = 200, array $extra = [])
    {
        return response()->json(array_merge([
            'status' => $ok,
            'received' => $httpStatus < 400,
            'message' => $message,
        ], $extra), $httpStatus);
    }

    private function digitalIdVerifiedResponse(?User $user, ?string $privyId, string $message = 'Verifikasi identitas berhasil')
    {
        return response()->json([
            'status' => true,
            'received' => true,
            'message' => $message,
            'privy_verified' => true,
            'privy_id' => $user?->id_privy ?: $privyId,
        ], 200);
    }

    /** Nasabah dari token login aplikasi; null untuk callback server-to-server dari Privy. */
    private function callbackAuthUser(): ?User
    {
        try {
            $user = auth('api')->user();
        } catch (\Throwable) {
            return null;
        }

        return $user instanceof User ? $user : null;
    }

    private function isTokenlessFailureEvent(string $event): bool
    {
        if (! str_starts_with($event, 'identity.')) {
            return false;
        }

        return in_array(substr($event, strlen('identity.')), self::TOKENLESS_FAILURE_REASONS, true);
    }

    private function handleTokenlessFailureCallback(Request $request, string $sessionId, string $event)
    {
        $reason = str_starts_with($event, 'identity.') ? substr($event, strlen('identity.')) : '';
        $reason = $this->isTokenlessFailureEvent($event) ? $reason : 'error';
        $message = self::TOKENLESS_FAILURE_MESSAGES[$reason];
        $session = PrivyDigitalIdSession::where('privy_session_id', $sessionId)->latest()->first();

        if (! $session) {
            Log::warning('Privy Digital ID tokenless failure callback unknown session', [
                'session_id' => $sessionId,
                'event' => $event,
            ]);

            return response()->json([
                'status' => true,
                'received' => true,
                'processed' => false,
                'message' => $message,
            ], 200);
        }

        $authUser = $this->callbackAuthUser();

        if ($authUser && (int) $authUser->id !== (int) $session->user_id) {
            return $this->digitalIdCallbackResponse(false, 'Sesi verifikasi bukan milik akun Anda.', 403);
        }

        $session = app(PrivyDigitalIdService::class)->acceptCallback($session);

        if (! $session) {
            return response()->json([
                'status' => true,
                'received' => true,
                'processed' => false,
                'message' => $message,
            ], 200);
        }

        $sharedData = $request->input('shared_data', []);
        if (is_string($sharedData)) {
            $decodedSharedData = json_decode($sharedData, true);
            $sharedData = is_array($decodedSharedData) ? $decodedSharedData : [];
        }

        $session->forceFill([
            'callback_state' => $request->input('state') ?: ($event ?: null),
            'callback_shared_data' => is_array($sharedData) ? $sharedData : [],
        ])->save();

        // Sesi yang sudah verified tidak diturunkan statusnya (claimSession menolak status verified)
        $digitalIdService = app(PrivyDigitalIdService::class);
        if ($digitalIdService->claimSession($sessionId, 'callback')) {
            $digitalIdService->finishSession($sessionId, [
                'status' => 'FAILED',
                'event' => $event !== '' ? $event : 'identity.error',
                'error_code' => $reason,
                'error_message' => $message,
            ]);
        }

        Log::info('Privy Digital ID non-verified event received', [
            'session_id' => $sessionId,
            'event' => $event,
        ]);

        return response()->json([
            'status' => true,
            'received' => true,
            'message' => $message,
            'event' => $event,
        ], 200);
    }

    private function processDigitalIdResult(PrivyDigitalIdSession $session, string $token, string $source, mixed $event = null, ?array $verifiedPayload = null): array
    {
        $digitalIdService = app(PrivyDigitalIdService::class);
        $claimed = $digitalIdService->claimSession($session->privy_session_id, $source);

        if (! $claimed) {
            $session->refresh();

            return [
                'status' => strtoupper($session->status),
                'privy_id' => $session->privy_id,
            ];
        }

        try {
            $payload = $verifiedPayload ?? app(PrivyDigitalIdClient::class)->verifyToken($token);
            $identity = [];
            $encryptedIdentity = $payload['data']['my_identity']
                ?? $payload['my_identity']
                ?? null;

            if (is_string($encryptedIdentity) && $encryptedIdentity !== '') {
                $identity = app(PrivyDigitalIdClient::class)->decryptIdentity($encryptedIdentity);
            }

            $status = strtoupper((string) (
                $payload['status']
                ?? $payload['verification_status']
                ?? $payload['data']['status']
                ?? $event
                ?? ''
            ));
            // Event callback/exchange berbentuk "identity.verified" → "VERIFIED"
            $status = preg_replace('/^IDENTITY\./', '', $status);
            $privyId = $payload['privy_id']
                ?? $payload['id_privy']
                ?? $payload['id_user']
                ?? $payload['data']['privy_id']
                ?? $payload['data']['id_privy']
                ?? $identity['privy_id']
                ?? $identity['id_privy']
                ?? $identity['id_user']
                ?? null;
            $enterpriseToken = $payload['enterprise_token']
                ?? $payload['enterpriseToken']
                ?? $payload['data']['enterprise_token'] ?? null;
            if ($privyId && in_array($status, ['', 'VERIFIED', 'SUCCESS', 'COMPLETED', 'APPROVED'], true)) {
                $user = User::find($session->user_id);

                if ($user) {
                    $identityCheck = $this->checkDigitalIdIdentity($user, $identity);

                    if ($identityCheck !== null) {
                        $digitalIdService->finishSession($session->privy_session_id, [
                            'status' => strtolower($identityCheck['status']),
                            'event' => is_scalar($event) ? (string) $event : null,
                            'privy_id' => (string) $privyId,
                        ]);

                        if (! empty($identity)) {
                            $digitalIdService->updateDecryptedIdentity($session->privy_session_id, $identity);
                        }

                        return $identityCheck + ['privy_id' => (string) $privyId];
                    }

                    // NIK / tanggal lahir / nama lengkap yang masih kosong diisi dari data Privy
                    $this->fillProfileFromDigitalId($user, $identity);
                    $this->syncDigitalIdKyc($session, $user, $identity, $payload, $session->callback_shared_data ?? []);
                    $this->markUserPrivyVerified($user, (string) $privyId, is_string($enterpriseToken) ? $enterpriseToken : null, $user->verified_privy == 'y');
                }

                if (! empty($identity)) {
                    $digitalIdService->updateDecryptedIdentity($session->privy_session_id, $identity);
                }

                $digitalIdService->finishSession($session->privy_session_id, [
                    'status' => 'VERIFIED',
                    'event' => is_scalar($event) ? (string) $event : null,
                    'privy_id' => (string) $privyId,
                ]);

                return [
                    'status' => 'VERIFIED',
                    'privy_id' => (string) $privyId,
                ];
            }

            if (in_array($status, ['FAILED', 'REJECTED', 'ERROR'], true)) {
                $digitalIdService->finishSession($session->privy_session_id, [
                    'status' => 'FAILED',
                    'event' => is_scalar($event) ? (string) $event : null,
                    'privy_id' => $privyId ? (string) $privyId : null,
                ]);

                return [
                    'status' => 'FAILED',
                    'privy_id' => $privyId ? (string) $privyId : null,
                ];
            }

            return [
                'status' => 'PROCESSING',
                'privy_id' => null,
            ];
        } catch (PrivyDigitalIdException $e) {
            $digitalIdService->failSession($session->privy_session_id, $e->errorCode, $e->getMessage());

            Log::warning('Privy Digital ID result processing failed', [
                'session_id' => $session->privy_session_id,
                'source' => $source,
                'code' => $e->errorCode,
                'http_status' => $e->httpStatus,
            ]);

            throw $e;
        }
    }

    /**
     * Cocokkan identitas hasil dekripsi Privy dengan data akun.
     * NIK akun yang masih kosong tidak dianggap berbeda (akan diisi dari Privy).
     *
     * @return array|null null = cocok; selain itu hasil IDENTITY_MISMATCH / IDENTITY_CONFLICT
     */
    private function checkDigitalIdIdentity(User $user, array $identity): ?array
    {
        $privyNik = preg_replace('/\D/', '', (string) ($identity['identity_number'] ?? ''));
        $userNik = preg_replace('/\D/', '', (string) $user->nik);

        $nikMatch = $privyNik !== '' && ($userNik === '' || $userNik === $privyNik);

        if (! $nikMatch) {
            return ['status' => 'IDENTITY_MISMATCH', 'nik_match' => $nikMatch];
        }

        if ($userNik === '' && User::where('nik', $privyNik)->whereKeyNot($user->getKey())->exists()) {
            return ['status' => 'IDENTITY_CONFLICT', 'nik_match' => false];
        }

        return null;
    }

    private function fillProfileFromDigitalId(User $user, array $identity): void
    {
        $privyNik = preg_replace('/\D/', '', (string) ($identity['identity_number'] ?? ''));
        $privyDob = $this->normalizeDigitalIdDate($identity['dob'] ?? null);

        if (empty($user->nik) && $privyNik !== '') {
            $user->nik = $privyNik;
        }

        if (empty($user->dob) && $privyDob !== null) {
            $user->dob = $privyDob;
        }

        if (! empty($identity['name']) && empty($user->nama_lengkap) && Schema::hasColumn($user->getTable(), 'nama_lengkap')) {
            $user->nama_lengkap = (string) $identity['name'];
        }
        // Disimpan bersama markUserPrivyVerified()
    }

    private function normalizeDigitalIdDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function markUserPrivyVerified(User $user, string $privyId, ?string $enterpriseToken, bool $wasVerified): void
    {
        $user->id_privy = $privyId;
        $user->enterprise_token_privy = $enterpriseToken ?? $user->enterprise_token_privy;
        $user->reason_privy = null;
        $user->resend_privy = null;
        $user->verified_privy = 'y';
        $user->save();

        if ($wasVerified) {
            return;
        }

        $cost = cost::first();

        if (! $cost) {
            return;
        }

        $newPSEPrivy = new privy_pse;
        $newPSEPrivy->user_id = $user->id;
        $newPSEPrivy->cost = $cost->biaya;
        $newPSEPrivy->keterangan = 'Penerbitan sertifikat elektronik';
        $newPSEPrivy->save();
    }

    private function syncDigitalIdKyc(PrivyDigitalIdSession $session, User $user, array $identity, array $payload, array $sharedData): void
    {
        $sources = [$identity, $payload['data'] ?? [], $payload, $sharedData];
        $selfie = $this->firstDigitalIdImage($sources, [
            'selfie',
            'selfie_image',
            'selfie_photo',
            'face_image',
            'face_photo',
            'liveness_image',
            'liveness_photo',
            'photo_selfie',
        ]);
        $ktp = $this->firstDigitalIdImage($sources, [
            'ktp',
            'ktp_image',
            'ktp_photo',
            'identity',
            'identity_image',
            'identity_photo',
            'id_card',
            'id_card_image',
            'id_card_photo',
            'identity_card',
            'identity_card_image',
            'identity_card_photo',
            'nik_image',
        ]);

        if ($selfie === null && $ktp === null) {
            return;
        }

        $updateData = [];

        if ($selfie !== null) {
            $updateData['local_selfie_path'] = $this->storeDigitalIdImage($user, $selfie, 'selfie');
        }

        if ($ktp !== null) {
            $updateData['local_ktp_path'] = $this->storeDigitalIdImage($user, $ktp, 'ktp');
        }

        if (! empty($updateData)) {
            $session->update($updateData);
        }
    }

    private function firstDigitalIdImage(array $sources, array $keys): ?string
    {
        foreach ($sources as $source) {
            if (! is_array($source)) {
                continue;
            }

            foreach ($keys as $key) {
                $value = data_get($source, $key) ?? $this->findDigitalIdValueByKey($source, $key);

                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        return null;
    }

    private function findDigitalIdValueByKey(array $source, string $key): mixed
    {
        foreach ($source as $sourceKey => $value) {
            if ((string) $sourceKey === $key) {
                return $value;
            }

            if (is_array($value)) {
                $nested = $this->findDigitalIdValueByKey($value, $key);

                if ($nested !== null) {
                    return $nested;
                }
            }
        }

        return null;
    }

    private function storeDigitalIdImage(User $user, string $value, string $type): string
    {
        $binary = $this->decodeDigitalIdImage($value);
        $extension = $this->imageExtension($binary);
        $timestamp = now('Asia/Jakarta')->format('YmdHis');
        $fileName = 'privy_'.$type.'_'.$user->id.'_'.$timestamp.'.'.$extension;
        $path = 'assets/users/'.$fileName;

        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    private function decodeDigitalIdImage(string $value): string
    {
        if (preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,(.+)$/', $value, $matches)) {
            $value = $matches[1];
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $response = (new Client(['timeout' => 30, 'http_errors' => false]))->request('GET', $value);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return (string) $response->getBody();
            }
        }

        $decoded = base64_decode($value, true);

        return $decoded !== false ? $decoded : $value;
    }

    private function imageExtension(string $binary): string
    {
        return match (substr($binary, 0, 4)) {
            "\x89PNG" => 'png',
            default => 'jpg',
        };
    }
}
