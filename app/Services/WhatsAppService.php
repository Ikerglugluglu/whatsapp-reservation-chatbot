<?php
// Servicio de integracion con Twilio WhatsApp.
require_once __DIR__ . '/../../vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Security\RequestValidator;
use Twilio\TwiML\MessagingResponse;

class WhatsAppService
{
    private ?Client $client = null;
    private string $from;

    public function __construct()
    {
        // Credenciales Twilio via .env
        $sid = getenv('TWILIO_SID') ?: '';
        $token = getenv('TWILIO_TOKEN') ?: '';
        $this->from = getenv('TWILIO_FROM') ?: 'whatsapp:number_from_twilio';

        if ($sid !== '' && $token !== '') {
            $this->client = new Client($sid, $token);
        }
    }

    public function parseIncomingRequest(): array
    {
        // Lee POST de Twilio (o JSON de pruebas).
        $fromRaw = trim((string) ($_POST['From'] ?? ''));
        $body = trim((string) ($_POST['Body'] ?? ''));
        $profileName = trim((string) ($_POST['ProfileName'] ?? ''));

        // Thunder Client often sends JSON; support it as fallback.
        if ($fromRaw === '' || $body === '' || $profileName === '') {
            $rawInput = file_get_contents('php://input');
            if (is_string($rawInput) && $rawInput !== '') {
                $json = json_decode($rawInput, true);
                if (is_array($json)) {
                    $fromRaw = trim((string) ($json['From'] ?? $fromRaw));
                    $body = trim((string) ($json['Body'] ?? $body));
                    $profileName = trim((string) ($json['ProfileName'] ?? $profileName));
                }
            }
        }

        return [
            'from' => $this->stripWhatsAppPrefix($fromRaw),
            'body' => $body,
            'name' => $profileName,
        ];
    }

    public function validateRequest(array $post): bool
    {
        // Verifica firma de Twilio.
        $token = getenv('TWILIO_TOKEN') ?: '';
        if ($token === '') {
            return false;
        }

        $signature = (string) ($_SERVER['HTTP_X_TWILIO_SIGNATURE'] ?? '');
        if ($signature === '') {
            return false;
        }

        $url = function_exists('getWebhookUrl') ? getWebhookUrl() : '';
        if ($url === '') {
            return false;
        }

        $validator = new RequestValidator($token);
        return $validator->validate($signature, $url, $post);
    }

    public function respondWithTwiml(string $message): void
    {
        // Respuesta TwiML inmediata.
        $response = new MessagingResponse();
        $response->message($message);

        header('Content-Type: text/xml; charset=UTF-8');
        echo (string) $response;
        exit;
    }

    public function send(string $to, string $message = '', array $options = []): string
    {
        // Envia mensaje saliente por Twilio.
        if ($this->client === null) {
            throw new RuntimeException('Twilio client no configurado. Revisa TWILIO_SID/TWILIO_TOKEN');
        }

        $params = [
            'from' => $this->normalizeToWhatsApp($this->from),
        ];

        if ($message !== '') {
            $params['body'] = $message;
        }

        if (!empty($options['contentSid'])) {
            $params['contentSid'] = (string) $options['contentSid'];

            if (isset($options['contentVariables'])) {
                $vars = $options['contentVariables'];
                if (is_array($vars)) {
                    $params['contentVariables'] = json_encode($vars, JSON_UNESCAPED_UNICODE);
                } else {
                    $params['contentVariables'] = (string) $vars;
                }
            }
        }
        if (empty($params['contentSid'])) {
            $defaultContentSid = trim((string) (getenv('TWILIO_CONTENT_SID') ?: ''));
            if ($defaultContentSid !== '') {
                $params['contentSid'] = $defaultContentSid;
                $defaultVarsRaw = trim((string) (getenv('TWILIO_CONTENT_VARS') ?: ''));
                if ($defaultVarsRaw !== '') {
                    $decoded = json_decode($defaultVarsRaw, true);
                    $params['contentVariables'] = is_array($decoded)
                        ? json_encode($decoded, JSON_UNESCAPED_UNICODE)
                        : $defaultVarsRaw;
                }
            }
        }

        $msg = $this->client->messages->create(
            $this->normalizeToWhatsApp($to),
            $params
        );

        return $msg->sid;
    }

    private function normalizeToWhatsApp(string $value): string
    {
        if (str_starts_with($value, 'whatsapp:')) {
            return $value;
        }

        return 'whatsapp:' . $value;
    }

    private function stripWhatsAppPrefix(string $value): string
    {
        if (str_starts_with($value, 'whatsapp:')) {
            return substr($value, 9);
        }

        return $value;
    }
}


