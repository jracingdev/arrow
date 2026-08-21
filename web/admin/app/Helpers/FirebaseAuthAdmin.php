<?php

namespace App\Helpers;

class FirebaseAuthAdmin
{
    public static function projectId(): string
    {
        return (string) env('FIREBASE_PROJECT_ID', 'j-arrow');
    }

    public static function createCustomToken(string $uid, array $claims = []): ?string
    {
        $creds = FcmSender::credentials();
        if (!$creds || empty($creds['private_key']) || empty($creds['client_email'])) {
            return null;
        }

        $now = time();
        $payload = [
            'iss' => $creds['client_email'],
            'sub' => $creds['client_email'],
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.signin',
            'iat' => $now,
            'exp' => $now + 3600,
            'uid' => $uid,
        ];
        if (!empty($claims)) {
            $payload['claims'] = $claims;
        }

        return self::encodeJwt($payload, $creds['private_key']);
    }

    public static function lookupUidByPhone(string $e164): ?string
    {
        $access = FcmSender::accessToken(['https://www.googleapis.com/auth/identitytoolkit']);
        if (!$access) {
            return self::lookupUidInFirestore($e164);
        }

        $url = 'https://identitytoolkit.googleapis.com/v1/projects/'.self::projectId().'/accounts:lookup';
        $response = self::jsonPost($url, $access, ['phoneNumber' => [$e164]]);
        $uid = $response['users'][0]['localId'] ?? null;
        if (is_string($uid) && $uid !== '') {
            return $uid;
        }

        return self::lookupUidInFirestore($e164);
    }

    public static function ensureAuthUser(string $e164): ?string
    {
        $existing = self::lookupUidByPhone($e164);
        if ($existing) {
            return $existing;
        }

        $uid = bin2hex(random_bytes(16));
        $access = FcmSender::accessToken(['https://www.googleapis.com/auth/identitytoolkit']);
        if ($access) {
            $url = 'https://identitytoolkit.googleapis.com/v1/projects/'.self::projectId().'/accounts';
            $created = self::jsonPost($url, $access, [
                'localId' => $uid,
                'phoneNumber' => $e164,
            ]);
            if (!empty($created['localId'])) {
                return $created['localId'];
            }
            $retry = self::lookupUidByPhone($e164);
            if ($retry) {
                return $retry;
            }
        }

        return $uid;
    }

    protected static function lookupUidInFirestore(string $e164): ?string
    {
        $national = preg_replace('/^\+55/', '', $e164);
        $candidates = array_unique(array_filter([$e164, $national, ltrim((string) $national, '0')]));

        foreach ($candidates as $phone) {
            $docs = FirestoreHelper::queryCollection('users', 'phoneNumber', '==', $phone);
            if (is_array($docs) && !empty($docs[0]['id'])) {
                return (string) $docs[0]['id'];
            }
        }

        return null;
    }

    protected static function jsonPost(string $url, string $accessToken, array $body): array
    {
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $result = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode((string) $result, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected static function encodeJwt(array $payload, string $privateKey): ?string
    {
        $header = self::b64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $body = self::b64url(json_encode($payload));
        $signingInput = $header.'.'.$body;
        $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$ok) {
            return null;
        }

        return $signingInput.'.'.self::b64url($signature);
    }

    protected static function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
