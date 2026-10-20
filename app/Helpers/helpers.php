<?php

use App\Models\privy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('sendOTPWhatsapp')) {
    function sendOTPWhatsapp($nohp, $code)
    {
        $client = new Client(['verify' => false]);
        $uri = rtrim(config('services.verihubs.base_url'), '/') . '/' . ltrim(config('services.verihubs.otp_path'), '/');
        $body = [
            "msisdn" => $nohp,
            "otp" => (string) $code,
            "time_limit" => (int) config('services.verihubs.time_limit'),
            "template_name" => config('services.verihubs.otp_template'),
            "lang_code" => config('services.verihubs.lang_code'),
        ];

        $res = $client->request('POST', $uri, [
            'body' => json_encode($body),
            'headers' => [
                'APP-ID' => config('services.verihubs.app_id'),
                'API-Key' => config('services.verihubs.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
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

if (! function_exists('loginPrivySign')) {
    /** Convert a structured Privy error response into a safe diagnostic message. */
    function formatPrivySignError(int $status, string $responseBody): string
    {
        $payload = json_decode($responseBody, true);
        $error = is_array($payload['error'] ?? null) ? $payload['error'] : [];
        $details = [];

        $errors = $error['errors'] ?? [];
        if (is_string($errors) && $errors !== '') {
            $details[] = $errors;
        } elseif (is_array($errors)) {
            foreach ($errors as $item) {
                if (is_string($item) && $item !== '') {
                    $details[] = $item;
                    continue;
                }

                if (! is_array($item)) {
                    continue;
                }

                $param = (string) ($item['param'] ?? 'request');
                $param = match ($param) {
                    'recipients[id_user]' => 'user.id_privy (API: recipients[id_user])',
                    default => $param,
                };
                $messages = $item['messages'] ?? [];
                $messages = is_array($messages) ? $messages : [$messages];
                $messages = array_filter(array_map('strval', $messages));

                if ($messages !== []) {
                    $details[] = $param . ': ' . implode(', ', $messages);
                }
            }
        }

        $message = $error['message'] ?? $payload['message'] ?? null;
        if (is_string($message) && $message !== '') {
            $details[] = $message;
        }

        return 'Privy Sign menolak request (' . $status . ')'
            . ($details !== [] ? ': ' . implode('; ', array_unique($details)) : '.');
    }

    /** Retrieve an access token using the dedicated Privy Sign credentials. */
    function loginPrivySign()
    {
        $client = new Client(['verify' => true]);
        $baseUrl = rtrim((string) config('services.privy_sign.base_url'), '/');
        $username = (string) config('services.privy_sign.username');
        $password = (string) config('services.privy_sign.password');

        $response = $client->request('POST', $baseUrl . '/oauth2/api/v1/token', [
            'json' => [
                'client_id' => $username,
                'client_secret' => $password,
                'grant_type' => 'client_credentials',
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ]);

        $status = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException(formatPrivySignError($status, $responseBody));
        }

        $payload = json_decode($responseBody, true);
        $token = $payload['data']['access_token'] ?? null;

        if (! is_string($token) || $token === '') {
            throw new \RuntimeException('Privy Sign tidak mengembalikan access token.');
        }

        return [
            'success' => 'true',
            'token' => $token,
        ];
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
    /**
     * Mengirim dokumen perjanjian pinjaman ke Privy untuk proses tanda tangan.
     *
     * Dokumen dikirim menggunakan nama file yang diberikan, lalu dibuatkan
     * nomor referensi unik berdasarkan NIK user dan ID pinjaman. Helper ini
     * mengatur dua penerima tanda tangan otomatis: peminjam dan pihak koperasi.
     *
     * @param  object  $user       Data user peminjam dengan atribut nik, id, dan id_privy.
     * @param  string  $fileName   Nama file dokumen yang akan dikirim ke Privy.
     * @param  int     $pinjamanId ID pinjaman yang digunakan untuk nomor referensi.
     * @return array{data: mixed} Respons data dari API Privy.
     */
    function ttdPK($user, $fileName, $pinjamanId)
    {
        if (empty($user->id_privy)) {
            $message = 'Privy ID peminjam (users.id_privy) kosong; dokumen tidak dikirim ke Privy.';
            Log::warning('Privy Sign TTD PK skipped because borrower Privy ID is missing', [
                'pinjaman_id' => $pinjamanId,
                'user_id' => $user->id ?? null,
            ]);

            return [
                'error' => true,
                'status' => 422,
                'message' => $message,
            ];
        }

        // Kirim dokumen menggunakan credential khusus Privy Sign.
        try {
            $login = loginPrivySign();
        } catch (\Throwable $exception) {
            Log::warning('Privy Sign authentication failed for TTD PK', [
                'pinjaman_id' => $pinjamanId,
                'user_id' => $user->id ?? null,
                'message' => $exception->getMessage(),
            ]);

            return [
                'error' => true,
                'status' => 502,
                'message' => $exception->getMessage(),
            ];
        }

        $privySign = config('services.privy_sign');
        $url = rtrim((string) $privySign['base_url'], '/') . '/web/api/v2/doc-signing';
        $client = new Client(['verify' => true]);
        $reference_number =  'TOMIDOC' . $user->nik .  $pinjamanId;
        $requestBody = [
            "reference_number" => $reference_number,
            "channel_id" => $privySign['channel_id'],
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
                "privyId" => $privySign['document_owner'],
                "enterpriseToken" => $privySign['enterprise_token']
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
                    "id_user" => $privySign['document_owner'],
                    "signer_type" => "Signer",
                    "enterpriseToken" => $privySign['enterprise_token'],
                    "posX" => 150,
                    "posY" => 910,
                    "signPage" => 9,
                    "detail" => 1,
                ]
            ]
        ];
        $signature = signature($requestBody, $privySign['api_key'], $privySign['secret_key'], 'POST');
        $headers = [
            'timestamp' => $signature['timestamp'],
            'Authorization' => 'Bearer ' . $login['token'],
            'Signature' => $signature['signature'],
            'Request-ID' => $user->id,
            'Content-Type' => 'application/json',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
        ];

        $statusMessages = [
            400 => 'Payload atau format parameter salah',
            401 => 'Token salah, kedaluwarsa, atau kredensial tidak valid',
            403 => 'Hak akses aplikasi atau akun tidak mencukupi',
            404 => 'Endpoint, resource, atau ID dokumen tidak ditemukan',
            409 => 'Dokumen atau sesi berada dalam status yang tidak sesuai',
            422 => 'Field wajib kosong, format data salah, atau identitas tidak terdaftar',
            429 => 'Batas permintaan API terlampaui',
            500 => 'Gangguan pada server penyedia layanan',
            502 => 'Layanan upstream bermasalah atau sedang tidak tersedia',
            503 => 'Layanan upstream bermasalah atau sedang tidak tersedia',
        ];

        try {
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'json' => $requestBody,
                'http_errors' => false,
            ]);
        } catch (RequestException $exception) {
            return [
                'error' => true,
                'status' => 502,
                'message' => $statusMessages[502],
            ];
        }

        $status = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        $decodedResponseBody = json_decode($responseBody, true);

        if ($status < 200 || $status >= 300) {
            $message = formatPrivySignError($status, $responseBody);
            Log::warning('Privy Sign rejected TTD PK request', [
                'status' => $status,
                'pinjaman_id' => $pinjamanId,
                'user_id' => $user->id ?? null,
                'privy_id_present' => ! empty($user->id_privy),
                'document_file_present' => ! empty($requestBody['document']['document_file']),
                'message' => $message,
            ]);

            return [
                'error' => true,
                'status' => $status,
                'message' => $message,
            ];
        }

        if (! is_array($decodedResponseBody) || ! isset($decodedResponseBody['data'])) {
            return [
                'error' => true,
                'status' => 502,
                'message' => $statusMessages[502],
            ];
        }

        return [
            'data' => $decodedResponseBody['data'],
        ];
    }
}


if (! function_exists('getDocumentPK')) {
    function getDocumentPK($user, $reference_number, $documentToken)
    {
        $loginDocument = loginPrivySign();
        $privySign = config('services.privy_sign');
        $urlDocument = rtrim((string) $privySign['base_url'], '/') . '/web/api/v2/doc-signing/status';
        $client = new Client(['verify' => true]);
        $requestBodyDocument = [
            "reference_number" => $reference_number,
            "channel_id" => $privySign['channel_id'],
            "document_token" => $documentToken,
            "info" => ""
        ];
        $signatureDocument = signature($requestBodyDocument, $privySign['api_key'], $privySign['secret_key'], 'POST');

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
