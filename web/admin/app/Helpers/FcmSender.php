<?php

namespace App\Helpers;

use Google\Client as Google_Client;
use Illuminate\Support\Facades\Storage;

class FcmSender
{
    public static function credentialsPath(): string
    {
        return storage_path('app/firebase/credentials.json');
    }

    public static function credentials(): ?array
    {
        if (!Storage::disk('local')->has('firebase/credentials.json')) {
            return null;
        }
        $raw = file_get_contents(self::credentialsPath());
        $json = json_decode($raw, true);
        return is_array($json) ? $json : null;
    }

    public static function accessToken(array $scopes): ?string
    {
        $creds = self::credentials();
        if (!$creds) {
            return null;
        }

        $client = new Google_Client();
        $client->setAuthConfig(self::credentialsPath());
        $client->addScope($scopes);
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        return $token['access_token'] ?? null;
    }

    /**
     * @return array{ok: bool, result?: mixed, error?: string}
     */
    public static function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        $token = trim($token);
        if ($token === '') {
            return ['ok' => false, 'error' => 'missing_fcm_token'];
        }

        $accessToken = self::accessToken(['https://www.googleapis.com/auth/firebase.messaging']);
        if (!$accessToken) {
            return ['ok' => false, 'error' => 'missing_credentials'];
        }

        $projectId = env('FIREBASE_PROJECT_ID', 'j-arrow');
        $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';

        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'HIGH',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['ok' => false, 'error' => $error];
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded = json_decode($result, true);

        if ($status >= 200 && $status < 300) {
            return ['ok' => true, 'result' => $decoded];
        }

        return ['ok' => false, 'error' => $decoded['error']['message'] ?? $result, 'result' => $decoded];
    }
}
