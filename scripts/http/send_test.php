<?php
// Script de prueba para enviar mensajes por WhatsApp (Twilio).
declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config.php';
require_once $root . '/app/Services/WhatsAppService.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    // Lee parametros de entrada (GET/POST).
    $to = trim((string) ($_POST['to'] ?? $_GET['to'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? $_GET['body'] ?? 'Mensaje de prueba desde CHAT-BOT'));
    $contentSid = trim((string) ($_POST['contentSid'] ?? $_GET['contentSid'] ?? ''));
    $contentVariablesRaw = $_POST['contentVariables'] ?? $_GET['contentVariables'] ?? '';

    if ($to === '') {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'error' => 'Falta parametro to',
            'example' => 'scripts/http/send_test.php?to=+34600000000&body=hola',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $service = new WhatsAppService();
    $options = [];

    if ($contentSid !== '') {
        $options['contentSid'] = $contentSid;
        if ($contentVariablesRaw !== '') {
            $decoded = json_decode((string) $contentVariablesRaw, true);
            $options['contentVariables'] = is_array($decoded)
                ? $decoded
                : (string) $contentVariablesRaw;
        }
    }

    // Envia mensaje y responde con SID.
    $sid = $service->send($to, $body, $options);
    echo json_encode([
        'ok' => true,
        'sid' => $sid,
        'to' => $to,
        'mode' => $contentSid !== '' ? 'template_or_content' : 'text',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}


