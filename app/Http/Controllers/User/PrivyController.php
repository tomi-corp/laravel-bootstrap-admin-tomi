<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\privy;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PrivyController extends Controller
{
    public function registerPrivy()
    {
        // 
        $user = User::where('id', auth('api')->user()->id)->first();
        if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->selfie) && isset($user->ktp)) {
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
                    "selfie" =>  encodeBase64('image', $user->selfie),
                    "identity" =>  encodeBase64('image', $user->ktp),
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
                } elseif ($user->verified_privy) {
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
        $user = User::where('id', auth('api')->user()->id)->first();
        if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->selfie) && isset($user->ktp) && isset($user->kartu_keluarga) && ($user->update_data_privy == true)) {
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
                "selfie" =>  encodeBase64('image', $user->selfie),
                "identity" =>  encodeBase64('image', $user->ktp),
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
}
