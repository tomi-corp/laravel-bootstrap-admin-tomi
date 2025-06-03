<?php

use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;

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
            "template_name" => 'otp_dumi',
        ];

        $res = $client->request('POST', $uri, [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'APP-ID' => '9fd6d403-3660-4c7e-8a42-5fb048a64ec8',
                'API-Key' => 'cHSGtrnSxHQHARI5uNSxTk8nnm+UsTyD',
                'Content-Type' => 'application/json'
            ]
        ])->getBody()->getContents();

        $response_json = json_decode($res);
        return response()->json($response_json, 200);
    }
}
