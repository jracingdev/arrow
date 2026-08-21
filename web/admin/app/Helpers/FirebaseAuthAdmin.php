<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class FirebaseAuthAdmin
{
    public static function projectId(): string
    {
        $fromConfig = trim((string) config('firebase.project_id', ''));
        if ($fromConfig !== '') {
            return $fromConfig;
        }
        $creds = FcmSender::credentials();

        return (string) ($creds['project_id'] ?? 'j-arrow');
    }

    public static function apiKey(): string
    {
        return trim((string) config('firebase.api_key', ''));
    }

    /**
     * @return array{method: string, customToken?: string, email?: string, password?: string}|null
     */
    public static function issueAuthSession(string $uid): ?array
    {
        $token = self::createCustomToken($uid);
        if (is_string($token) && $token !== '') {
            return ['method' => 'custom', 'customToken' => $token];
        }

        $passwordSession = self::issuePasswordSession($uid);
        if ($passwordSession) {
            Log::warning('Arrow OTP using password session fallback', ['uid' => $uid]);
            return array_merge(['method' => 'password'], $passwordSession);
        }

        return null;
    }

    /**
     * Mint a Firebase Auth custom token and verify it against Identity Toolkit
     * before handing it to the browser.
     */
    public static function createCustomToken(string $uid, array $claims = []): ?string
    {
        $creds = FcmSender::credentials();
        if (!$creds || empty($creds['private_key']) || empty($creds['client_email'])) {
            return null;
        }

        $uid = trim($uid);
        if ($uid === '' || strlen($uid) > 128) {
            Log::error('Arrow custom token: uid inválido');
            return null;
        }

        $credProject = (string) ($creds['project_id'] ?? '');
        $envProject = self::projectId();
        if ($credProject !== '' && $credProject !== $envProject) {
            Log::error('Arrow custom token: project mismatch', [
                'credentials' => $credProject,
                'env' => $envProject,
            ]);
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

        $kid = $creds['private_key_id'] ?? null;
        $kid = is_string($kid) && $kid !== '' ? $kid : null;
        $privateKey = self::normalizePrivateKey((string) $creds['private_key']);

        $candidates = [];

        $local = self::encodeJwt($payload, $privateKey, $kid);
        if ($local) {
            $candidates[] = $local;
        }

        if (class_exists(JWT::class)) {
            try {
                $candidates[] = JWT::encode($payload, $privateKey, 'RS256', $kid);
            } catch (\Throwable $e) {
                Log::warning('Arrow JWT::encode failed', ['error' => $e->getMessage()]);
            }
        }

        $blob = self::signBlob($creds['client_email'], $payload, $kid);
        if ($blob) {
            $candidates[] = $blob;
        }

        $seen = [];
        foreach ($candidates as $jwt) {
            if (!is_string($jwt) || isset($seen[$jwt])) {
                continue;
            }
            $seen[$jwt] = true;
            $error = self::identityToolkitRejects($jwt);
            if ($error === null) {
                return $jwt;
            }
            Log::warning('Arrow custom token rejected by Identity Toolkit', [
                'error' => $error,
                'iss' => $creds['client_email'],
                'project' => $credProject ?: $envProject,
            ]);
        }

        return null;
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

    /**
     * @return array{email: string, password: string}|null
     */
    protected static function issuePasswordSession(string $uid): ?array
    {
        $access = FcmSender::accessToken(['https://www.googleapis.com/auth/identitytoolkit']);
        if (!$access) {
            return null;
        }

        $lookupUrl = 'https://identitytoolkit.googleapis.com/v1/projects/'.self::projectId().'/accounts:lookup';
        $found = self::jsonPost($lookupUrl, $access, ['localId' => [$uid]]);
        $email = $found['users'][0]['email'] ?? '';
        if (!is_string($email) || $email === '') {
            $email = 'otp.'.preg_replace('/[^a-zA-Z0-9]/', '', $uid).'@arrow.app.br';
        }

        $password = bin2hex(random_bytes(16));
        $updateUrl = 'https://identitytoolkit.googleapis.com/v1/projects/'.self::projectId().'/accounts:update';
        $updated = self::jsonPost($updateUrl, $access, [
            'localId' => $uid,
            'email' => $email,
            'password' => $password,
        ]);

        if (!empty($updated['error']['message'])) {
            $updated = self::jsonPost($updateUrl, $access, [
                'localId' => $uid,
                'password' => $password,
            ]);
        }

        if (!empty($updated['error']['message'])) {
            Log::error('Arrow password session failed', ['error' => $updated['error']['message'], 'uid' => $uid]);
            return null;
        }

        return ['email' => $email, 'password' => $password];
    }

    /**
     * @return string|null Error message, or null if the token is accepted.
     */
    public static function identityToolkitRejects(string $customToken): ?string
    {
        $apiKey = self::apiKey();
        if ($apiKey === '') {
            return 'missing_api_key';
        }

        $url = 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken?key='.rawurlencode($apiKey);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'token' => $customToken,
            'returnSecureToken' => true,
        ]));
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded = json_decode((string) $result, true);

        if ($status >= 200 && $status < 300 && !empty($decoded['idToken'])) {
            return null;
        }

        return $decoded['error']['message'] ?? ('HTTP '.$status);
    }

    /** Firebase Admin uses IAM signBlob, not signJwt. */
    protected static function signBlob(string $clientEmail, array $payload, ?string $kid): ?string
    {
        $access = FcmSender::accessToken(['https://www.googleapis.com/auth/cloud-platform']);
        if (!$access) {
            return null;
        }

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        if ($kid) {
            $header['kid'] = $kid;
        }
        $signingInput = self::b64url(json_encode($header, JSON_UNESCAPED_SLASHES))
            .'.'
            .self::b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $url = 'https://iamcredentials.googleapis.com/v1/projects/-/serviceAccounts/'
            .rawurlencode($clientEmail).':signBlob';
        $response = self::jsonPost($url, $access, [
            'payload' => base64_encode($signingInput),
        ]);

        $signed = $response['signedBlob'] ?? null;
        if (!is_string($signed) || $signed === '') {
            if (!empty($response['error']['message'])) {
                Log::warning('Arrow IAM signBlob failed', ['error' => $response['error']['message']]);
            }
            return null;
        }

        $raw = base64_decode($signed, true);
        if ($raw === false) {
            return null;
        }

        return $signingInput.'.'.self::b64url($raw);
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
        $result = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode((string) $result, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected static function normalizePrivateKey(string $key): string
    {
        $key = str_replace(["\r\n", "\r"], "\n", $key);
        $key = str_replace('\\n', "\n", $key);

        return $key;
    }

    protected static function encodeJwt(array $payload, string $privateKey, ?string $kid = null): ?string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        if ($kid) {
            $header['kid'] = $kid;
        }
        $headerPart = self::b64url(json_encode($header, JSON_UNESCAPED_SLASHES));
        $bodyPart = self::b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signingInput = $headerPart.'.'.$bodyPart;
        $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$ok) {
            Log::error('Arrow openssl_sign failed');
            return null;
        }

        return $signingInput.'.'.self::b64url($signature);
    }

    protected static function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
