<?php
// Handler del webhook de WhatsApp (mensajes entrantes).
declare(strict_types=1);

final class WhatsAppWebhookHandler
{
    private DbReservas $reservas;
    private WhatsAppService $service;

    public function __construct(DbReservas $reservas, WhatsAppService $service)
    {
        $this->reservas = $reservas;
        $this->service = $service;
    }

    public function handle(): void
    {
        // Punto de entrada: interpreta comando y responde.
        $incoming = $this->service->parseIncomingRequest();
        $from = (string) ($incoming['from'] ?? '');
        $body = strtolower(trim((string) ($incoming['body'] ?? '')));
        $contactName = trim((string) ($incoming['name'] ?? ''));

        if ($contactName === '') {
            $contactName = $from;
        }
        $contactName = mb_substr($contactName, 0, 100);

        if ($from === '' || $body === '') {
            $this->respond('No pude leer el mensaje. Escribe ayuda.');
        }

        if ($this->isHelp($body)) {
            $this->respond($this->helpMessage());
        }

        if ($this->isHorarios($body)) {
            $this->handleHorarios();
        }

        if ($body === 'mis reservas') {
            $this->handleMisReservas($from);
        }

        $targetDateTime = $this->parseReservationDateTime($body);
        if ($targetDateTime !== null) {
            $this->handleReserva($targetDateTime, $from, $contactName);
        }

        $this->respond("No entendi tu mensaje.\n" . $this->helpMessage());
    }

    private function respond(string $message): void
    {
        // Helper para responder.
        $this->service->respondWithTwiml($message);
    }

    private function isHelp(string $body): bool
    {
        return $body === 'ayuda' || $body === 'menu' || $body === 'help';
    }

    private function isHorarios(string $body): bool
    {
        return $body === 'horarios' || str_contains($body, 'horario');
    }

    private function helpMessage(): string
    {
        return "Comandos disponibles:\n"
            . "1) horarios\n"
            . "2) reservar FECHA HORA (ej: reservar 2026-03-10 18:00)\n"
            . "   Tambien: reservar 10/03/2026 18:00 o reservar lunes 18:00\n"
            . "   Duracion fija: 1 hora. Puedes reservar en cualquier minuto.\n"
            . "3) mis reservas\n"
            . "4) ayuda";
    }

    private function getCurrentWeekRange(): array
    {
        $tz = new DateTimeZone(date_default_timezone_get());
        $now = new DateTimeImmutable('now', $tz);
        $start = $now->modify('monday this week')->setTime(0, 0, 0);
        $end = $start->modify('+6 days')->setTime(23, 59, 59);
        return [$start, $end];
    }

    private function normalizeDay(string $day): string
    {
        $normalized = mb_strtolower(trim($day), 'UTF-8');
        if (function_exists('iconv')) {
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized);
            if (is_string($ascii) && $ascii !== '') {
                $normalized = $ascii;
            }
        }
        return preg_replace('/[^a-z]/', '', $normalized) ?? '';
    }

    private function spanishDayName(DateTimeInterface $date): string
    {
        $days = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ];

        return $days[(int) $date->format('N')] ?? '';
    }

    private function parseReservationDateTime(string $body): ?DateTimeImmutable
    {
        // Parseo de fecha/hora soportando varios formatos.
        $clean = strtolower(trim($body));
        if (!preg_match('/^(reservar|reserva)\s+(.+)$/', $clean, $cmd)) {
            return null;
        }

        $payload = trim($cmd[2]);
        $tz = new DateTimeZone(date_default_timezone_get());

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})\s+(\d{1,2}):(\d{2})$/', $payload, $m)) {
            return DateTimeImmutable::createFromFormat('Y-m-d H:i', sprintf(
                '%04d-%02d-%02d %02d:%02d',
                (int) $m[1],
                (int) $m[2],
                (int) $m[3],
                (int) $m[4],
                (int) $m[5]
            ), $tz) ?: null;
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})$/', $payload, $m)) {
            return DateTimeImmutable::createFromFormat('d/m/Y H:i', sprintf(
                '%02d/%02d/%04d %02d:%02d',
                (int) $m[1],
                (int) $m[2],
                (int) $m[3],
                (int) $m[4],
                (int) $m[5]
            ), $tz) ?: null;
        }

        if (preg_match('/^([a-zA-Z]+)\s+(\d{1,2}):(\d{2})$/', $payload, $m)) {
            $weekdayRaw = $this->normalizeDay($m[1]);
            $hour = (int) $m[2];
            $minute = (int) $m[3];

            if ($hour > 23 || $minute > 59) {
                return null;
            }

            $weekdayMap = [
                'lunes' => 1,
                'martes' => 2,
                'miercoles' => 3,
                'jueves' => 4,
                'viernes' => 5,
                'sabado' => 6,
                'domingo' => 7,
            ];

            if (!isset($weekdayMap[$weekdayRaw])) {
                return null;
            }

            $now = new DateTimeImmutable('now', $tz);
            $todayWeekday = (int) $now->format('N');
            $targetWeekday = $weekdayMap[$weekdayRaw];
            $daysToAdd = ($targetWeekday - $todayWeekday + 7) % 7;

            $target = $now->setTime($hour, $minute)->modify('+' . $daysToAdd . ' days');
            if ($daysToAdd === 0 && $target <= $now) {
                $target = $target->modify('+7 days');
            }

            return $target;
        }

        return null;
    }

    private function handleHorarios(): void
    {
        // Responde con reservas de la semana.
        [$weekStart, $weekEnd] = $this->getCurrentWeekRange();
        $start = $weekStart->format('Y-m-d H:i:s');
        $end = $weekEnd->format('Y-m-d H:i:s');

        $rows = $this->reservas->getBetween($start, $end);
        if (empty($rows)) {
            $this->respond(
                "No hay reservas para esta semana.\n"
                . "Puedes reservar cualquier fecha/hora con: reservar 2026-03-10 18:00"
            );
        }

        $lines = ["Reservas de esta semana:"];
        foreach ($rows as $row) {
            $dt = new DateTimeImmutable($row['fecha_reserva']);
            $lines[] = '- ' . $this->spanishDayName($dt) . ' ' . $dt->format('d/m/Y H:i');
        }
        $lines[] = '';
        $lines[] = 'Para reservar: reservar 2026-03-10 18:00';

        $this->respond(implode("\n", $lines));
    }

    private function handleMisReservas(string $from): void
    {
        // Lista ultimas reservas del telefono.
        $rows = $this->reservas->getByTelefono($from, 5);
        if (empty($rows)) {
            $this->respond('No tienes reservas registradas.');
        }

        $lines = ["Tus ultimas reservas:"];
        foreach ($rows as $row) {
            if (!empty($row['fecha_reserva'])) {
                $dt = new DateTimeImmutable($row['fecha_reserva']);
                $lines[] = '#' . $row['id'] . ' - ' . $this->spanishDayName($dt) . ' ' . $dt->format('d/m/Y H:i');
            } else {
                $lines[] = '#' . $row['id'] . ' - ' . $row['dia'] . ' ' . $row['hora'] . ' (' . $row['fecha'] . ')';
            }
        }

        $this->respond(implode("\n", $lines));
    }

    private function handleReserva(DateTimeImmutable $targetDateTime, string $from, string $contactName): void
    {
        // Crea reserva con historial y confirma al usuario.
        $now = new DateTimeImmutable('now', new DateTimeZone(date_default_timezone_get()));
        if ($targetDateTime <= $now) {
            $this->respond('La fecha/hora debe ser futura. Ejemplo: reservar 2026-03-10 18:00');
        }

        try {
            $this->reservas->reserveWithHistory($contactName, $from, $targetDateTime);
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'SLOT_TAKEN') {
                $this->respond('Esa franja de 1 hora ya esta reservada. Elige otra.');
            }
            throw $e;
        }

        $this->respond('Reserva confirmada: ' . $this->spanishDayName($targetDateTime) . ' ' . $targetDateTime->format('d/m/Y H:i'));
    }
}

