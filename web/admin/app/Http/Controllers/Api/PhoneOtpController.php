<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FcmSender;
use App\Helpers\FirebaseAuthAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PhoneOtpController extends Controller
{
    private const TTL_SECONDS = 300;
    private const MAX_ATTEMPTS = 5;

    public function send(Request $request)
    {
        $e164 = $this->normalizePhone($request->input('phone'));
        $sessionId = trim((string) $request->input('sessionId'));
        $fcmToken = trim((string) $request->input('fcmToken', ''));

        if (!$e164) {
            return response()->json(['success' => false, 'message' => 'Número de telefone inválido.'], 422);
        }
        if ($sessionId === '' || strlen($sessionId) < 8) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 422);
        }

        $rateKey = 'otp:rate:'.$e164;
        $rate = (int) Cache::get($rateKey, 0);
        if ($rate >= 5) {
            return response()->json(['success' => false, 'message' => 'Muitas tentativas. Aguarde um minuto.'], 429);
        }
        Cache::put($rateKey, $rate + 1, 60);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_DEFAULT);

        Cache::put('otp:challenge:'.$e164, [
            'hash' => $hash,
            'sessionId' => $sessionId,
            'attempts' => 0,
            'createdAt' => time(),
        ], self::TTL_SECONDS);

        $title = 'Código Arrow';
        $body = 'Seu código de verificação é '.$code;
        $fcm = ['ok' => false];
        if ($fcmToken !== '') {
            $fcm = FcmSender::sendToToken($fcmToken, $title, $body, [
                'type' => 'phone_otp',
                'code' => $code,
            ]);
            if (empty($fcm['ok'])) {
                Log::warning('Arrow OTP FCM failed', ['phone' => $e164, 'error' => $fcm['error'] ?? null]);
            }
        }

        return response()->json([
            'success' => true,
            'sessionId' => $sessionId,
            'expiresIn' => self::TTL_SECONDS,
            'fcmDelivered' => !empty($fcm['ok']),
            // Código no próprio aparelho que pediu (HTTPS). Sem SMS.
            'displayCode' => $code,
            'channel' => 'app',
        ]);
    }

    public function verify(Request $request)
    {
        $e164 = $this->normalizePhone($request->input('phone'));
        $sessionId = trim((string) $request->input('sessionId'));
        $code = preg_replace('/\D/', '', (string) $request->input('code'));

        if (!$e164 || $sessionId === '' || strlen($code) !== 6) {
            return response()->json(['success' => false, 'message' => 'Dados inválidos.'], 422);
        }

        $challenge = Cache::get('otp:challenge:'.$e164);
        if (!is_array($challenge) || ($challenge['sessionId'] ?? '') !== $sessionId) {
            return response()->json(['success' => false, 'message' => 'Código expirado. Solicite outro.'], 400);
        }

        $attempts = (int) ($challenge['attempts'] ?? 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::forget('otp:challenge:'.$e164);
            return response()->json(['success' => false, 'message' => 'Muitas tentativas. Solicite outro código.'], 429);
        }

        if (!password_verify($code, $challenge['hash'] ?? '')) {
            $challenge['attempts'] = $attempts + 1;
            Cache::put('otp:challenge:'.$e164, $challenge, self::TTL_SECONDS);
            return response()->json(['success' => false, 'message' => 'Código inválido.'], 400);
        }

        Cache::forget('otp:challenge:'.$e164);

        $uid = FirebaseAuthAdmin::ensureAuthUser($e164);
        if (!$uid) {
            return response()->json(['success' => false, 'message' => 'Falha ao criar sessão.'], 500);
        }

        $customToken = FirebaseAuthAdmin::createCustomToken($uid, ['phone' => $e164]);
        if (!$customToken) {
            return response()->json(['success' => false, 'message' => 'Credencial Firebase ausente no servidor.'], 500);
        }

        return response()->json([
            'success' => true,
            'customToken' => $customToken,
            'uid' => $uid,
        ]);
    }

    protected function normalizePhone($raw): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '55') && strlen($digits) >= 12) {
            $digits = $digits;
        } elseif (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55'.$digits;
        } else {
            return null;
        }

        if (strlen($digits) < 12 || strlen($digits) > 13) {
            return null;
        }

        return '+'.$digits;
    }
}
