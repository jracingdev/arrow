<?php

namespace App\Helpers;

use Google\Client as Google_Client;

class FcmSender
{
    public const ROLE_TOPICS = ['customer', 'vendor', 'driver', 'provider', 'worker'];

    public static function credentialsPath(): string
    {
        $configured = config('firebase.credentials');
        if (is_string($configured) && trim($configured) !== '' && is_file(trim($configured))) {
            return trim($configured);
        }

        return storage_path('app/firebase/credentials.json');
    }

    public static function credentials(): ?array
    {
        $path = self::credentialsPath();
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $raw = @file_get_contents($path);
        $json = json_decode((string) $raw, true);

        return is_array($json) ? $json : null;
    }

    public static function projectId(): string
    {
        $fromConfig = trim((string) config('firebase.project_id', ''));
        if ($fromConfig !== '') {
            return $fromConfig;
        }

        $creds = self::credentials();
        if (!empty($creds['project_id'])) {
            return (string) $creds['project_id'];
        }

        return 'j-arrow';
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

        return self::sendMessage(self::buildMessage(['token' => $token], $title, $body, $data));
    }

    /**
     * @return array{ok: bool, result?: mixed, error?: string}
     */
    public static function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $topic = trim($topic);
        if ($topic === '' || !preg_match('/^[A-Za-z0-9-_.~%]+$/', $topic)) {
            return ['ok' => false, 'error' => 'invalid_topic'];
        }

        return self::sendMessage(self::buildMessage(['topic' => $topic], $title, $body, $data));
    }

    /**
     * Send to many tokens. Empty tokens are skipped.
     *
     * @param  array<int, string>  $tokens
     * @return array{sent: int, failed: int, skipped: int, errors: array<int, string>}
     */
    public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $errors = [];
        $seen = [];

        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                $skipped++;
                continue;
            }
            if (isset($seen[$token])) {
                $skipped++;
                continue;
            }
            $seen[$token] = true;

            $result = self::sendToToken($token, $title, $body, $data);
            if (!empty($result['ok'])) {
                $sent++;
                continue;
            }

            $failed++;
            $error = self::publicError($result['error'] ?? 'fcm_send_failed');
            if (count($errors) < 8 && !in_array($error, $errors, true)) {
                $errors[] = $error;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<int, string>  $topics
     * @return array{sent: int, failed: int, skipped: int, errors: array<int, string>}
     */
    public static function sendToTopics(array $topics, string $title, string $body, array $data = []): array
    {
        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $errors = [];
        $seen = [];

        foreach ($topics as $topic) {
            $topic = trim((string) $topic);
            if ($topic === '') {
                $skipped++;
                continue;
            }
            if (isset($seen[$topic])) {
                $skipped++;
                continue;
            }
            $seen[$topic] = true;

            $result = self::sendToTopic($topic, $title, $body, $data);
            if (!empty($result['ok'])) {
                $sent++;
                continue;
            }

            $failed++;
            $error = self::publicError($result['error'] ?? 'fcm_send_failed');
            if (count($errors) < 8 && !in_array($error, $errors, true)) {
                $errors[] = $error;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    public static function publicError(?string $error): string
    {
        $error = trim((string) $error);
        if ($error === '') {
            return 'Falha ao enviar pelo FCM.';
        }

        $error = preg_replace('/Bearer\s+[A-Za-z0-9._\-~+\/]+=*/i', 'Bearer [redacted]', $error) ?? $error;
        $error = preg_replace('/private_key[^,]{0,40}/i', 'private_key [redacted]', $error) ?? $error;

        return $error;
    }

    /**
     * @param  array<string, string>  $target  token|topic
     * @return array<string, mixed>
     */
    private static function buildMessage(array $target, string $title, string $body, array $data = []): array
    {
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        if (!isset($stringData['click_action'])) {
            $stringData['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';
        }

        $message = array_merge($target, [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
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
        ]);

        if ($stringData !== []) {
            $message['data'] = $stringData;
        }

        return $message;
    }

    /**
     * @param  array<string, mixed>  $message
     * @return array{ok: bool, result?: mixed, error?: string}
     */
    private static function sendMessage(array $message): array
    {
        $accessToken = self::accessToken(['https://www.googleapis.com/auth/firebase.messaging']);
        if (!$accessToken) {
            return ['ok' => false, 'error' => 'Arquivo de credenciais Firebase ausente ou inválido.'];
        }

        $projectId = self::projectId();
        if ($projectId === '') {
            return ['ok' => false, 'error' => 'FIREBASE_PROJECT_ID vazio. Use config(), não env(), após config:cache.'];
        }

        $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);

            return ['ok' => false, 'error' => $error !== '' ? $error : 'curl_failed'];
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded = json_decode((string) $result, true);

        if ($status >= 200 && $status < 300) {
            return ['ok' => true, 'result' => $decoded];
        }

        $apiError = is_array($decoded) ? ($decoded['error']['message'] ?? null) : null;

        return [
            'ok' => false,
            'error' => self::publicError($apiError ?: 'FCM HTTP '.$status),
            'result' => is_array($decoded) ? $decoded : null,
        ];
    }
}
