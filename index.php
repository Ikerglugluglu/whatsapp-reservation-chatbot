<?php
require_once __DIR__ . '/config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (isset($_GET['webhook_info']) && $_GET['webhook_info'] === '1') {
            header('Content-Type: text/plain; charset=UTF-8');
            $webhookUrl = getWebhookUrl();
            echo "Webhook WhatsApp activo.\n";
            echo "Usa esta URL en Twilio Sandbox (When a message comes in):\n";
            echo $webhookUrl . "\n\n";
            echo "Nota: http://127.0.0.1:4040/inspect/http es solo el panel de inspeccion local de ngrok, no es la URL publica del webhook.";
            exit;
        }

        header('Location: panel_login.php');
        exit;
    }

    $whatsapp = new WhatsAppService();
    if (!$whatsapp->validateRequest($_POST)) {
        error_log('Webhook error: invalid Twilio signature or missing token');
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Forbidden';
        exit;
    }

    $handler = new WhatsAppWebhookHandler(new DbReservas(), $whatsapp);
    $handler->handle();
} catch (Throwable $e) {
    error_log('Webhook error: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Error interno';
}
