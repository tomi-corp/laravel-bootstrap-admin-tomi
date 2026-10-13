<?php

namespace App\Services;

use App\Exceptions\PrivyDigitalIdException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PrivyDigitalIdClient
{
    private const BASE_URLS = [
        'sandbox' => 'https://stg-api-digitalid.privy.id',
        'production' => 'https://api-digitalid.privy.id',
    ];

    private const DEFAULT_ISSUER = 'https://digitalid.privy.id/';

    private const DEFAULT_USER_AGENT = 'tomi-api';

    private const HTTP_TIMEOUT_SECONDS = 30;

    private const CLOCK_SKEW_SECONDS = 60;

    private const JWKS_TTL_SECONDS = 600;

    private const STATUS_TO_PRIVY_CODE = [
        400 => 'invalid_input',
        401 => 'unauthorized',
        404 => 'not_found',
        409 => 'invalid_state',
        410 => 'session_expired',
        423 => 'session_blocked',
        429 => 'rate_limited',
    ];

    private const PRIVY_ERRORS = [
        'invalid_input' => ['privy_invalid_input', 'Request ke Privy ditolak (input tidak valid)', 502],
        'unauthorized' => ['privy_unauthorized', 'API key Privy ditolak (cek konfigurasi Privy Digital ID)', 502],
        'not_found' => ['privy_not_found', 'Resource tidak ditemukan di Privy', 502],
        'invalid_state' => ['session_invalid_state', 'Aksi tidak diizinkan pada tahap sesi saat ini', 409],
        'session_expired' => ['session_expired', 'Sesi verifikasi sudah kedaluwarsa', 410],
        'session_blocked' => ['session_blocked', 'Sesi verifikasi diblokir oleh Privy', 423],
        'rate_limited' => ['privy_rate_limited', 'Terlalu banyak request ke Privy, coba lagi beberapa saat', 429],
    ];

    private const PRIVY_ERRORS_BY_KIND = [
        'exchange' => [
            'invalid_input' => ['exchange_code_invalid', 'Kode exchange tidak valid', 400],
            'not_found' => ['exchange_code_invalid', 'Kode exchange tidak ditemukan atau sudah dipakai', 404],
            'session_expired' => ['exchange_code_expired', 'Kode exchange sudah kedaluwarsa atau sudah dipakai', 410],
        ],
    ];

    public function createSession(array $options = []): array
    {
        $data = $this->privyRequest('POST', '/v1/sessions', null, 'sessions', $options);

        if (empty($data['session_id']) || empty($data['session_url'])) {
            throw new PrivyDigitalIdException(
                'Respons create session dari Privy tidak lengkap (session_id/session_url kosong)',
                'privy_invalid_response',
            );
        }

        return [
            'session_id' => (string) $data['session_id'],
            'session_url' => $data['session_url'],
            'expires_at' => $data['expires_at'] ?? null,
        ];
    }

    public function exchangeCode(string $code, array $options = []): array
    {
        $data = $this->privyRequest('POST', '/v1/sessions/exchange', ['code' => $code], 'exchange', $options);

        if (empty($data['session_id']) || empty($data['token'])) {
            throw new PrivyDigitalIdException(
                'Respons exchange dari Privy tidak lengkap (session_id/token kosong)',
                'privy_invalid_response',
            );
        }

        return [
            'session_id' => (string) $data['session_id'],
            'event' => $data['event'] ?? null,
            'token' => $data['token'],
        ];
    }

    public function verifyToken(string $token, ?int $now = null): array
    {
        $this->ensureSodiumAvailable();

        $config = $this->config();
        $parts = explode('.', $token);

        if (count($parts) !== 3 || in_array('', $parts, true)) {
            throw new PrivyDigitalIdException('JWT tidak valid (harus 3 bagian)', 'jwt_malformed', 401);
        }

        $header = $this->base64UrlJson($parts[0], 'header');

        if (($header['alg'] ?? null) !== 'EdDSA') {
            throw new PrivyDigitalIdException('Algoritma JWT tidak diizinkan: '.($header['alg'] ?? ''), 'jwt_bad_alg', 401);
        }

        if (empty($header['kid'])) {
            throw new PrivyDigitalIdException('Header JWT tanpa kid', 'jwt_malformed', 401);
        }

        $jwk = $this->signingJwk($header['kid'], $config);
        $signature = $this->base64UrlDecode($parts[2]);
        $message = $parts[0].'.'.$parts[1];
        $publicKey = $this->ed25519PublicKeyFromJwk($jwk);

        if (! \sodium_crypto_sign_verify_detached($signature, $message, $publicKey)) {
            throw new PrivyDigitalIdException('Tanda tangan JWT tidak valid', 'jwt_bad_signature', 401);
        }

        $payload = $this->base64UrlJson($parts[1], 'payload');
        $nowSec = $now ?? time();

        if (($payload['iss'] ?? null) !== $config['issuer']) {
            throw new PrivyDigitalIdException('Issuer JWT tidak sesuai: '.($payload['iss'] ?? ''), 'jwt_bad_iss', 401);
        }

        $audiences = is_array($payload['aud'] ?? null) ? array_map('strval', $payload['aud']) : [(string) ($payload['aud'] ?? '')];

        if (! in_array($config['merchant_id'], $audiences, true)) {
            throw new PrivyDigitalIdException('Audience JWT bukan Merchant ID ini', 'jwt_bad_aud', 401);
        }

        if (! isset($payload['exp']) || ! is_numeric($payload['exp'])) {
            throw new PrivyDigitalIdException('JWT tanpa klaim exp', 'jwt_no_exp', 401);
        }

        if ($nowSec > (int) $payload['exp'] + self::CLOCK_SKEW_SECONDS) {
            throw new PrivyDigitalIdException('JWT sudah kedaluwarsa', 'jwt_expired', 401);
        }

        if (isset($payload['nbf']) && is_numeric($payload['nbf']) && $nowSec + self::CLOCK_SKEW_SECONDS < (int) $payload['nbf']) {
            throw new PrivyDigitalIdException('JWT belum berlaku (nbf)', 'jwt_not_yet_valid', 401);
        }

        return $payload;
    }

    public function decryptIdentity(string $ciphertextB64, ?string $privateKey = null): array
    {
        if ($ciphertextB64 === '') {
            throw new PrivyDigitalIdException('data.my_identity kosong', 'identity_missing', 422);
        }

        $pem = $this->normalizePrivateKeyPem($privateKey ?? $this->config()['private_key']);
        $ciphertext = base64_decode($ciphertextB64, true);

        if ($ciphertext === false) {
            throw new PrivyDigitalIdException('data.my_identity bukan base64 valid', 'identity_invalid', 422);
        }

        $plain = null;
        $ok = openssl_private_decrypt($ciphertext, $plain, $pem, \OPENSSL_PKCS1_PADDING);

        if (! $ok || ! is_string($plain)) {
            throw new PrivyDigitalIdException('Dekripsi identitas gagal (cek private key RSA)', 'identity_decrypt_failed', 422);
        }

        $data = json_decode($plain, true);

        if (! is_array($data)) {
            throw new PrivyDigitalIdException('Hasil dekripsi identitas bukan JSON', 'identity_not_json', 422);
        }

        return $data;
    }

    private function privyRequest(string $method, string $path, ?array $body, string $kind, array $options = []): array
    {
        $config = $this->config();

        try {
            $client = new Client([
                'timeout' => self::HTTP_TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);

            $response = match ($method) {
                'POST' => $client->request('POST', $config['base_url'].$path, [
                    'headers' => $this->headers($config, $options),
                    'json' => $body ?? new \stdClass(),
                ]),
                default => throw new PrivyDigitalIdException('HTTP method tidak didukung', 'internal_error', 500),
            };
        } catch (PrivyDigitalIdException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            throw new PrivyDigitalIdException(
                'Privy tidak merespons ('.$e->getMessage().')',
                'privy_unreachable',
                504,
            );
        }

        $status = $response->getStatusCode();
        $data = json_decode((string) $response->getBody(), true);
        $data = is_array($data) ? $data : [];

        if ($status >= 200 && $status < 300) {
            return $data;
        }

        $privyCode = $data['error']['code']
            ?? self::STATUS_TO_PRIVY_CODE[$status]
            ?? null;
        $known = self::PRIVY_ERRORS_BY_KIND[$kind][$privyCode]
            ?? self::PRIVY_ERRORS[$privyCode]
            ?? null;

        if ($known) {
            throw new PrivyDigitalIdException($known[1], $known[0], $known[2], $status, $privyCode, $data);
        }

        throw new PrivyDigitalIdException(
            'Privy mengembalikan HTTP '.$status,
            'privy_http_error',
            502,
            $status,
            $privyCode,
            $data,
        );
    }

    private function config(): array
    {
        $env = strtolower((string) config('services.privy_digital_id.env', 'sandbox'));
        $baseUrl = rtrim((string) (config('services.privy_digital_id.api_base_url') ?: self::BASE_URLS[$env] ?? ''), '/');
        $config = [
            'base_url' => $baseUrl,
            'api_key' => config('services.privy_digital_id.merchant_api_key'),
            'merchant_id' => (string) config('services.privy_digital_id.merchant_id'),
            'issuer' => config('services.privy_digital_id.issuer', self::DEFAULT_ISSUER),
            'private_key' => config('services.privy_digital_id.private_key'),
            'application_id' => (string) config('services.privy_digital_id.application_id'),
            'client_id' => (string) config('services.privy_digital_id.client_id'),
            'client_secret' => (string) config('services.privy_digital_id.client_secret'),
        ];

        $missing = [];
        foreach ([
            'base_url' => 'DIGITAL_IDV2_PRIVY_ENV / DIGITAL_IDV2_PRIVY_BASE_URL',
            'api_key' => 'DIGITAL_IDV2_PRIVY_MERCHANT_API_KEY',
            'merchant_id' => 'DIGITAL_IDV2_PRIVY_MERCHANT_ID',
            'application_id' => 'DIGITAL_IDV2_PRIVY_APPLICATION_ID',
            'client_id' => 'DIGITAL_IDV2_PRIVY_CLIENT_ID',
            'client_secret' => 'DIGITAL_IDV2_PRIVY_CLIENT_SECRET',
        ] as $key => $name) {
            if ($config[$key] === '' || $config[$key] === null) {
                $missing[] = $name;
            }
        }

        if ($missing) {
            throw new PrivyDigitalIdException(
                'Konfigurasi Privy Digital ID belum lengkap: '.implode(', ', $missing),
                'config_missing',
                500,
            );
        }

        return $config;
    }

    private function headers(array $config, array $options = []): array
    {
        $timestamp = now()->toISOString();

        return [
            'X-API-Key' => $config['api_key'],
            'X-Application-ID' => $config['application_id'],
            'Client-ID' => $config['client_id'],
            'X-Request-ID' => (string) Str::uuid(),
            'timestamp' => $timestamp,
            'signature' => hash_hmac('sha256', $timestamp, $config['client_secret']),
            'User-Agent' => $options['user_agent'] ?? self::DEFAULT_USER_AGENT,
            'Content-Type' => 'application/json',
        ];
    }

    private function signingJwk(string $kid, array $config): array
    {
        $cacheKey = 'privy_digital_id_jwks:'.sha1($config['base_url']);
        $keys = Cache::remember($cacheKey, self::JWKS_TTL_SECONDS, fn () => $this->fetchJwks($config));
        $jwk = collect($keys)->firstWhere('kid', $kid);

        if (! $jwk) {
            Cache::forget($cacheKey);
            $keys = Cache::remember($cacheKey, self::JWKS_TTL_SECONDS, fn () => $this->fetchJwks($config));
            $jwk = collect($keys)->firstWhere('kid', $kid);
        }

        if (! $jwk) {
            throw new PrivyDigitalIdException('Kunci publik dengan kid "'.$kid.'" tidak ada di JWKS Privy', 'jwt_unknown_kid', 401);
        }

        if (($jwk['kty'] ?? null) !== 'OKP' || ($jwk['crv'] ?? null) !== 'Ed25519') {
            throw new PrivyDigitalIdException('Kunci kid "'.$kid.'" bukan Ed25519', 'jwt_bad_key', 401);
        }

        return $jwk;
    }

    private function fetchJwks(array $config): array
    {
        try {
            $client = new Client([
                'timeout' => self::HTTP_TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);
            $response = $client->request('GET', $config['base_url'].'/.well-known/jwks.json', [
                'headers' => ['User-Agent' => self::DEFAULT_USER_AGENT],
            ]);
        } catch (GuzzleException $e) {
            throw new PrivyDigitalIdException('Gagal mengambil JWKS Privy ('.$e->getMessage().')', 'jwks_unreachable', 504);
        }

        $status = $response->getStatusCode();
        $data = json_decode((string) $response->getBody(), true);

        if ($status !== 200 || ! is_array($data) || ! is_array($data['keys'] ?? null)) {
            throw new PrivyDigitalIdException('JWKS Privy tidak valid (HTTP '.$status.')', 'jwks_invalid');
        }

        return $data['keys'];
    }

    private function base64UrlJson(string $value, string $what): array
    {
        $decoded = $this->base64UrlDecode($value);
        $data = json_decode($decoded, true);

        if (! is_array($data)) {
            throw new PrivyDigitalIdException('JWT '.$what.' tidak valid', 'jwt_malformed', 401);
        }

        return $data;
    }

    private function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        if ($decoded === false) {
            throw new PrivyDigitalIdException('JWT tidak valid', 'jwt_malformed', 401);
        }

        return $decoded;
    }

    private function ed25519PublicKeyFromJwk(array $jwk): string
    {
        $publicKey = $this->base64UrlDecode((string) ($jwk['x'] ?? ''));
        $publicKeyBytes = \constant('SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES');

        if (strlen($publicKey) !== $publicKeyBytes) {
            throw new PrivyDigitalIdException('Ukuran public key Ed25519 tidak valid', 'jwt_bad_key', 401);
        }

        return $publicKey;
    }

    private function ensureSodiumAvailable(): void
    {
        if (! \function_exists('sodium_crypto_sign_verify_detached') || ! \defined('SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES')) {
            throw new PrivyDigitalIdException('Ekstensi PHP Sodium belum aktif untuk verifikasi JWT Privy', 'sodium_missing', 500);
        }
    }

    private function normalizePrivateKeyPem(?string $raw): string
    {
        if (! $raw) {
            throw new PrivyDigitalIdException('DIGITAL_IDV2_PRIVY_PRIVATE_KEY belum di-set', 'config_missing', 500);
        }

        $pem = trim(str_replace('\\n', "\n", $raw));

        if (! str_contains($pem, '-----BEGIN')) {
            $decoded = base64_decode($pem, true);

            if (is_string($decoded) && str_contains($decoded, '-----BEGIN')) {
                $pem = trim($decoded);
            }
        }

        if (! str_contains($pem, '-----BEGIN')) {
            throw new PrivyDigitalIdException('DIGITAL_IDV2_PRIVY_PRIVATE_KEY bukan PEM / base64 PEM yang valid', 'config_invalid', 500);
        }

        return $pem;
    }
}
