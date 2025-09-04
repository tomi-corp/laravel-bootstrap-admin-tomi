<?php

use App\Models\privy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('sendOTPWhatsapp')) {
    function sendOTPWhatsapp($nohp, $code)
    {
        $client = new Client(['verify' => false]);
        $uri = 'https://api.verihubs.com/v1/whatsapp/otp/send';
        $body = [
            "msisdn" => $nohp,
            "otp" => $code,
            "time_limit" => 60,
            "template_name" => "otp_tomi",
            "lang_code" => "id",
        ];

        $res = $client->request('POST', $uri, [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'APP-ID' => '4ac45ece-4051-4ff5-a597-bde23bcf17d3',
                'API-Key' => 'rlelTbhhJE1xub2TeGE7psMYd+Ztxix6',
                'Content-Type' => 'application/json'
            ]
        ])->getBody()->getContents();

        $response_json = json_decode($res);
        return response()->json($response_json, 200);
    }
}

if (! function_exists('loginPrivy')) {
    function loginPrivy()
    {
        $client = new Client(['verify' => false]);
        $now = Carbon::now();
        $privy = privy::first();

        if ($privy->expired >= $now) {
            $data = [
                'success' => 'true',
                'token' => $privy->token,
            ];
        } else {
            $body = [
                "client_id" => $privy->username,
                "client_secret" => $privy->password,
                "grant_type" => "client_credentials",
            ];

            $res = $client->request('POST', $privy->url . '/oauth2/api/v1/token', [
                'body' => json_encode($body),
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ])->getBody()
                ->getContents();

            $carbonDate = Carbon::parse($now);
            $response_json = json_decode($res);
            $privy->token = $response_json->data->access_token;
            $newCarbonDate = $carbonDate->addSeconds($response_json->data->expires_in);
            $privy->expired = $newCarbonDate->toDateTimeString();
            $privy->save();

            $data = [
                'success' => 'true',
                'token' => $privy->token,
            ];
        }
        return $data;
    }
}


if (! function_exists('encodeBase64')) {
    function encodeBase64($tipe, $namefile)
    {
        if ($tipe == 'image') {
            $filePath = '/assets/users/' . $namefile;
        } elseif ($tipe == 'file') {
            $filePath = '/assets/pk/' . $namefile;
        }

        $disk = 'local';

        if (!Storage::disk($disk)->exists($filePath)) {
            return null;
        }

        $fileContents = Storage::disk($disk)->get($filePath);

        if ($fileContents === false) {
            return null;
        }

        $mimeType = Storage::disk($disk)->mimeType($filePath);

        if ($mimeType === false || empty($mimeType)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'gif':
                    $mimeType = 'image/gif';
                    break;
                case 'webp':
                    $mimeType = 'image/webp';
                    break;
                case 'docx':
                    $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                case 'doc':
                    $mimeType = 'application/msword';
                    break;
                default:
                    return null;
            }
        }

        $base64EncodedData = base64_encode($fileContents);
        $dataUri = "data:{$mimeType};base64,{$base64EncodedData}";
        return $dataUri;
    }
}

if (! function_exists('signature')) {
    function signature($requestBody, $username, $password, $method)
    {
        $time = Carbon::now('Asia/Jakarta')->format('D M d Y H:i:s \G\M\TO');
        unset($requestBody['selfie'], $requestBody['identity'], $requestBody['ktp'], $requestBody['supporting_docs'], $requestBody['document']);
        $json = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $rawText = preg_replace('/\s+/', '', $json);

        $bodyMd5 = base64_encode(md5($rawText, true));

        $stringToSign = $time . ':' . $username . ':' . strtoupper($method) . ':' . $bodyMd5;

        $hmac = hash_hmac('sha256', $stringToSign, $password, true);
        $hmacBase64 = base64_encode($hmac);

        $authString = $username . ':' . $hmacBase64;
        $finalSignature = base64_encode($authString);

        return [
            'timestamp' => $time,
            'signature' => $finalSignature,
        ];
    }
}


if (! function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) {
            $hasil = $bilangan[$angka];
        } elseif ($angka < 20) {
            $hasil = terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $hasil = terbilang(intval($angka / 10)) . ' puluh ' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = 'seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = terbilang(intval($angka / 100)) . ' ratus ' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = 'seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = terbilang(intval($angka / 1000)) . ' ribu ' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = terbilang(intval($angka / 1000000)) . ' juta ' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $hasil = terbilang(intval($angka / 1000000000)) . ' miliar ' . terbilang($angka % 1000000000);
        } else {
            return 'angka terlalu besar';
        }

        // Bersihkan spasi ganda dan trim
        $hasil = trim(preg_replace('/\s+/', ' ', $hasil));

        // Ubah huruf pertama setiap kata menjadi kapital
        return ucwords($hasil);
    }
}




if (! function_exists('ttdPK')) {
    function ttdPK($user, $fileName, $pinjamanId)
    {
        // kirim document ke privy
        $privy = privy::first();
        $login = loginPrivy();
        $url =  $privy->url . '/web/api/v2/doc-signing';
        $client = new Client(['verify' => false]);
        $reference_number =  'TOMIDOC' . $user->nik .  $pinjamanId;
        $requestBody = [
            "reference_number" => $reference_number,
            "channel_id" => "Toko_Mikro001",
            "custom_signature_placement" => true,
            "doc_process" => 0,
            "info" => "",
            "visibility" => true,
            "document" => [
                "document_name" => $fileName,
                "barcode_position" => "1",
                "document_file" => encodeBase64('file', $fileName),
                "sign_process" => "1"
            ],
            "doc_owner" => [
                "privyId" => "LOYZRR5016",
                "enterpriseToken" => "f96bdf97a65a76930272eedd23f8d982f472aa53"
            ],
            "recipients" => [
                [
                    "user_type" => "0",
                    "autosign" => "1",
                    "id_user" => $user->id_privy,
                    "signer_type" => "Signer",
                    "enterpriseToken" => "",
                    "posX" => 450,
                    "posY" => 910,
                    "signPage" => 9,
                    "detail" => 1,
                ],
                [
                    "user_type" => "0",
                    "autosign" => "1",
                    "id_user" => "LOYZRR5016",
                    "signer_type" => "Signer",
                    "enterpriseToken" => "f96bdf97a65a76930272eedd23f8d982f472aa53",
                    "posX" => 150,
                    "posY" => 910,
                    "signPage" => 9,
                    "detail" => 1,
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

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $requestBody,
        ]);
        $responseBody = $response->getBody()->getContents();
        $decodedResponseBody = json_decode($responseBody, true);

        return [
            'data' => $decodedResponseBody['data'],
        ];
    }
}


if (! function_exists('getDocumentPK')) {
    function getDocumentPK($user, $reference_number, $documentToken)
    {
        $privy = privy::first();
        $loginDocument = loginPrivy();
        $urlDocument =  $privy->url . '/web/api/v2/doc-signing/status';
        $client = new Client(['verify' => false]);
        $requestBodyDocument = [
            "reference_number" => $reference_number,
            "channel_id" => "Toko_Mikro001",
            "document_token" => $documentToken,
            "info" => ""
        ];
        $signatureDocument = signature($requestBodyDocument, $privy->username, $privy->password, 'POST');

        $headerDocument = [
            'timestamp' => $signatureDocument['timestamp'],
            'Authorization' => 'Bearer ' . $loginDocument['token'],
            'Signature' => $signatureDocument['signature'],
            'Request-ID' => $user->id,
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];
        $responseDocument = $client->request('POST', $urlDocument, [
            'headers' => $headerDocument,
            'json' => $requestBodyDocument,
        ]);
        $responseBodyDocument = $responseDocument->getBody()->getContents();
        $decodedResponseBodyDocument = json_decode($responseBodyDocument, true);
        return [
            'data' => $decodedResponseBodyDocument['data'],
        ];
    }
}

if (! function_exists('checkStatus')) {
    function checkStatus()
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $privy = privy::first();
        $loginPrivy = loginPrivy();
        $client = new Client(['verify' => false]);
        $requestBody = [
            "reference_number" => $user->reference_number_privy,
            "channel_id" => "Toko_Mikro001",
            "register_token" => $user->register_token_privy,
            "info" => ""
        ];
        $urlCheckStatus =  $privy->url . '/web/api/v2/register/status';
        $signatureDocument = signature($requestBody, $privy->username, $privy->password, 'POST');

        $headerDocument = [
            'timestamp' => $signatureDocument['timestamp'],
            'Authorization' => 'Bearer ' . $loginPrivy['token'],
            'Signature' => $signatureDocument['signature'],
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];
        $responseDocument = $client->request('POST', $urlCheckStatus, [
            'headers' => $headerDocument,
            'json' => $requestBody,
        ]);
        $responseBodyDocument = $responseDocument->getBody()->getContents();
        $decodedResponseBodyDocument = json_decode($responseBodyDocument, true);
        return [
            'data' => $decodedResponseBodyDocument['data'],
        ];
    }
}


if (! function_exists('encryptSecure')) {
    function encryptSecure($param)
    {
        $key = env('KEYDECRYPTION');
        $salt =  env('SALTDECRYPTION');
        $method = 'AES-256-CBC';
        $iv = substr(hash('sha256', $salt), 0, 16);
        $key = hash('sha256', $key);
        $encrypted = openssl_encrypt($param, $method, $key, 0, $iv);
        return base64_encode($encrypted);
    }
}

if (! function_exists('decryptSecure')) {
    function decryptSecure($param)
    {
        $key = env('KEYDECRYPTION');
        $salt =  env('SALTDECRYPTION');
        $iv = substr(hash('sha256', $salt), 0, 16);
        $key = hash('sha256', $key);
        $method = 'AES-256-CBC';
        $encrypted = base64_decode($param);
        return openssl_decrypt($encrypted, $method, $key, 0, $iv);
    }
}

if (! function_exists('secureFile')) {
    function secureFile($param)
    {
        return URL::temporarySignedRoute(
            'preview.file',
            now()->addMinutes(10),
            ['filename' => $param]
        );
    }
}
