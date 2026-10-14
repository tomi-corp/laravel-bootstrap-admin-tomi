<?php

namespace App\Http\Controllers\User;

use App\Exceptions\PrivyDigitalIdException;
use App\Http\Controllers\Controller;
use App\Models\cost;
use App\Models\kyc_user;
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
use Illuminate\Support\Facades\Storage;

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
                'expires_at' => $session->expires_at,
            ],
        ], 200);
    }

    public function digitalIdCallback(Request $request)
    {
        $sessionId = trim((string) $request->input('session_id'));
        $token = $request->input('token');

        if (! is_string($token) || $token === '') {
            Log::warning('Privy Digital ID callback invalid', [
                'has_session_id' => $sessionId !== '',
                'has_token' => is_string($token) && $token !== '',
            ]);

            return response()->json([
                'received' => false,
                'message' => 'token wajib diisi',
            ], 400);
        }

        if ($sessionId === '') {
            try {
                $payload = app(PrivyDigitalIdClient::class)->verifyToken($token);
                $sessionId = trim((string) ($payload['session_id'] ?? $payload['data']['session_id'] ?? ''));
            } catch (PrivyDigitalIdException $e) {
                Log::warning('Privy Digital ID callback token invalid', [
                    'code' => $e->errorCode,
                    'http_status' => $e->httpStatus,
                ]);

                return response()->json([
                    'received' => false,
                    'message' => 'token callback tidak valid',
                ], 401);
            }
        }

        if ($sessionId === '') {
            Log::warning('Privy Digital ID callback invalid', [
                'has_session_id' => false,
                'has_token' => true,
            ]);

            return response()->json([
                'received' => false,
                'message' => 'session_id tidak ditemukan pada callback',
            ], 400);
        }

        $session = PrivyDigitalIdSession::where('privy_session_id', $sessionId)->latest()->first();

        if (! $session) {
            Log::warning('Privy Digital ID callback unknown session', [
                'session_id' => $sessionId,
                'event' => $request->input('event'),
            ]);

            return response()->json([
                'received' => true,
                'processed' => false,
            ], 200);
        }

        app(PrivyDigitalIdService::class)->markReceived($sessionId, 'callback');

        $sharedData = $request->input('shared_data', []);
        if (is_string($sharedData)) {
            $decodedSharedData = json_decode($sharedData, true);
            $sharedData = is_array($decodedSharedData) ? $decodedSharedData : [];
        }

        $session->forceFill([
            'exchange_code' => $request->input('exchange_code') ?: $session->exchange_code,
            'callback_state' => $request->input('state'),
            'callback_token' => Crypt::encryptString($token),
            'callback_shared_data' => is_array($sharedData) ? $sharedData : [],
        ])->save();

        try {
            $this->processDigitalIdResult($session, $token, 'callback', $request->input('state') ?? $request->input('event'));
        } catch (PrivyDigitalIdException $e) {
            Log::warning('Privy Digital ID callback processing failed', [
                'session_id' => $sessionId,
                'code' => $e->errorCode,
                'http_status' => $e->httpStatus,
            ]);
        }

        return response()->json([
            'received' => true,
        ], 200);
    }

    private function processDigitalIdResult(PrivyDigitalIdSession $session, string $token, string $source, mixed $event = null): array
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
            $payload = app(PrivyDigitalIdClient::class)->verifyToken($token);
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
                ?? $payload['data']['enterprise_token']
                ?? $payload['data']['enterpriseToken']
                ?? $identity['enterprise_token']
                ?? $identity['enterpriseToken']
                ?? null;

            if ($privyId && in_array($status, ['', 'VERIFIED', 'SUCCESS', 'COMPLETED', 'APPROVED'], true)) {
                $user = User::find($session->user_id);

                if ($user) {
                    $this->syncDigitalIdKyc($user, $identity, $payload, $session->callback_shared_data ?? []);
                    $this->markUserPrivyVerified($user, (string) $privyId, is_string($enterpriseToken) ? $enterpriseToken : null, $user->verified_privy == 'y');
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

    private function syncDigitalIdKyc(User $user, array $identity, array $payload, array $sharedData): void
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

        $kyc = kyc_user::where('user_id', $user->id)->latest()->first() ?: new kyc_user;
        $kyc->user_id = $user->id;

        if ($selfie !== null) {
            $kyc->selfie = $this->storeDigitalIdImage($user, $selfie, 'selfie');
        }

        if ($ktp !== null) {
            $kyc->ktp = $this->storeDigitalIdImage($user, $ktp, 'ktp');
        }

        $kyc->save();
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
        $fileName = $user->nik.'_'.$type.'_'.$user->id.'_digital_id_'.now('Asia/Jakarta')->format('YmdHis').'.'.$extension;

        Storage::disk('local')->put('assets/users/'.$fileName, $binary);

        return $fileName;
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
