<?php

namespace App\Http\Controllers;

use App\Helpers\FcmSender;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id = '')
    {
        return view('notification.index')->with('id', $id);
    }

    public function send($id = '')
    {
        return view('notification.send')->with('id', $id);
    }

    public function broadcastnotification(Request $request)
    {
        @set_time_limit(180);

        $subject = trim((string) $request->input('subject', $request->input('title', '')));
        $message = trim((string) $request->input('message', ''));
        $audience = strtolower(trim((string) $request->input('audience', 'role')));
        $role = strtolower(trim((string) $request->input('role', '')));
        $topic = trim((string) $request->input('topic', ''));
        $tokens = $this->normalizeTokens($request->input('tokens_json', $request->input('tokens', $request->input('fcm'))));

        if ($subject === '' || $message === '') {
            return response()->json([
                'success' => false,
                'message' => 'Informe o título e a mensagem da notificação.',
                'sent' => 0,
                'failed' => 0,
                'skipped' => 0,
            ]);
        }

        if (!FcmSender::credentials()) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo de credenciais Firebase não encontrado. Envie o JSON da conta de serviço em Configurações ou copie-o para storage/app/firebase/credentials.json.',
                'sent' => 0,
                'failed' => 0,
                'skipped' => 0,
            ]);
        }

        $allowedRoles = FcmSender::ROLE_TOPICS;
        $topics = [];

        if (!in_array($audience, ['all', 'role', 'topic', 'user'], true)) {
            $audience = $role !== '' ? 'role' : 'all';
        }

        if ($audience === 'all') {
            $topics = $allowedRoles;
        } elseif ($audience === 'role') {
            if (!in_array($role, $allowedRoles, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Papel inválido. Use customer, vendor, driver, provider ou worker.',
                    'sent' => 0,
                    'failed' => 0,
                    'skipped' => 0,
                ]);
            }
            $topics = [$role];
        } elseif ($audience === 'topic') {
            if ($topic === '' || !preg_match('/^[A-Za-z0-9-_.~%]+$/', $topic)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe um tópico FCM válido.',
                    'sent' => 0,
                    'failed' => 0,
                    'skipped' => 0,
                ]);
            }
            $topics = [$topic];
        } elseif ($audience === 'user') {
            if ($tokens === []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este usuário não tem token FCM válido. Peça para abrir o app e aceitar notificações.',
                    'sent' => 0,
                    'failed' => 0,
                    'skipped' => 0,
                ]);
            }
        }

        $data = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'admin_broadcast',
            'id' => '1',
            'status' => 'done',
        ];

        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $errors = [];

        // Broadcasts go to FCM topics (apps subscribe on login). Firestore tokens are often
        // stale (NotRegistered) or from another Firebase project (SenderId mismatch).
        if (in_array($audience, ['all', 'role', 'topic'], true)) {
            $tokens = [];
        }

        if ($audience === 'user' && $tokens !== []) {
            $tokenResult = FcmSender::sendToTokens($tokens, $subject, $message, $data);
            $sent += $tokenResult['sent'];
            $failed += $tokenResult['failed'];
            $skipped += $tokenResult['skipped'];
            $errors = array_merge($errors, $tokenResult['errors']);
        }

        if ($topics !== []) {
            $topicResult = FcmSender::sendToTopics($topics, $subject, $message, $data);
            $sent += $topicResult['sent'];
            $failed += $topicResult['failed'];
            $skipped += $topicResult['skipped'];
            $errors = array_merge($errors, $topicResult['errors']);
        }

        $errors = array_values(array_unique($errors));
        $success = $sent > 0 && $failed === 0;
        $partial = $sent > 0 && $failed > 0;

        if ($sent === 0 && $failed === 0) {
            return response()->json([
                'success' => false,
                'partial' => false,
                'message' => 'Nenhum destinatário com token ou tópico para enviar.',
                'sent' => 0,
                'failed' => 0,
                'skipped' => $skipped,
                'errors' => $errors,
            ]);
        }

        if ($success && $topics !== []) {
            $messageText = 'Notificação enviada aos tópicos: '.implode(', ', $topics).'. Quem abriu o app recente (e aceitou notificações) deve receber.';
        } else {
            $messageText = $success
                ? 'Notificação enviada com sucesso ('.$sent.' envio(s)).'
                : ($partial
                    ? 'Envio parcial: '.$sent.' ok, '.$failed.' falha(s).'
                    : 'Falha ao enviar notificação ('.$failed.' falha(s)).');
        }

        if ($errors !== []) {
            $messageText .= ' '.implode(' ', array_slice($errors, 0, 3));
        }

        return response()->json([
            'success' => $success,
            'partial' => $partial,
            'message' => $messageText,
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    public function sendNotification(Request $request)
    {
        $fcmToken = trim((string) $request->input('fcm', ''));
        $title = trim((string) $request->input('title', $request->input('subject', '')));
        $body = trim((string) $request->input('message', ''));
        $payload = $request->input('payload');

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            $payload = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($payload)) {
            $payload = [];
        }

        if ($fcmToken === '') {
            return response()->json([
                'success' => false,
                'message' => 'Token FCM vazio. Envio ignorado.',
            ]);
        }

        $result = FcmSender::sendToToken($fcmToken, $title, $body, $payload);
        if (!empty($result['ok'])) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação enviada com sucesso.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => FcmSender::publicError($result['error'] ?? 'Falha ao enviar notificação.'),
        ]);
    }

    /**
     * @param  mixed  $tokens
     * @return array<int, string>
     */
    private function normalizeTokens($tokens): array
    {
        if (is_string($tokens)) {
            $decoded = json_decode($tokens, true);
            $tokens = is_array($decoded) ? $decoded : (preg_split('/[\s,]+/', $tokens) ?: []);
        }
        if (!is_array($tokens)) {
            return [];
        }

        $out = [];
        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token !== '') {
                $out[] = $token;
            }
        }

        return array_values(array_unique($out));
    }
}
