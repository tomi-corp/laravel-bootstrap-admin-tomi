<?php

use App\Models\privy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Google\Auth\OAuth2;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Firebase\JWT\JWT;
use Google\Client as GoogleClient;
use GuzzleHttp\Client as GuzzleClient;
// use Google_Client;

if (!function_exists('sendNotification')) {
    function sendNotification($token, $title, $body)
    {

        if (App::environment('production')) {
            $projectId = config('services.fcm.project_id_production');
        } elseif (App::environment('local')) {
            $projectId = config('services.fcm.project_id');
        }

        if (!$projectId) {
            return [
                'success' => false,
                'message' => 'FCM project_id not configured',
            ];
        }


        if (App::environment('production')) {
            $credentialsPath = Storage::path('fcm/fcmProduction.json');
        } elseif (App::environment('local')) {
            $credentialsPath = Storage::path('fcm/fcmDevelopment.json');
        }

        if (!file_exists($credentialsPath)) {
            return [
                'success' => false,
                'message' => 'FCM credentials file not found',
            ];
        }

        $guzzleClient = new GuzzleClient([
            'verify' => false,
        ]);

        $client = new GoogleClient();

        $client->setHttpClient($guzzleClient);

        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        try {
            $client->refreshTokenWithAssertion();
            $accessToken = $client->getAccessToken()['access_token'];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to obtain access token',
                'error' => $e->getMessage(),
            ];
        }


        if ($token != "news") {
            $notificationPayload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ];
        } else if ($token == "news") {
            $notificationPayload = [
                'message' => [
                    'topic' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ];
        }


        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withOptions([
                'verify' => false,
            ])
            ->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                $notificationPayload
            );

        return true;
    }
}
