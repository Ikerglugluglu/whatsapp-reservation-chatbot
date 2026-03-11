<?php
// Servicio de gestion de reservas (panel admin).
declare(strict_types=1);

final class ReservationAdminService
{
    private DbReservas $reservas;

    public function __construct(DbReservas $reservas)
    {
        $this->reservas = $reservas;
    }

    public function purgeExpired(int $hours = 24): void
    {
        // Limpia reservas caducadas.
        $this->reservas->purgeExpired();
    }

    public function getActive(int $hours = 24): array
    {
        // Reservas activas.
        return $this->reservas->getActive($hours);
    }

    public function getToday(): array
    {
        // Reservas de hoy.
        return $this->reservas->getToday();
    }

    public function handleCreate(array &$errors, string &$success, array &$form): void
    {
        // Valida y crea una reserva.
        $nombre = trim((string) ($_POST['create_nombre'] ?? ''));
        $telefono = trim((string) ($_POST['create_telefono'] ?? ''));
        $fecha = trim((string) ($_POST['create_fecha_reserva'] ?? ''));

        $form = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'fecha_reserva' => $fecha,
        ];

        if ($nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (strlen($nombre) > 100) {
            $errors[] = 'El nombre no puede superar 100 caracteres.';
        }

        if ($telefono !== '' && strlen($telefono) > 20) {
            $errors[] = 'El telefono no puede superar 20 caracteres.';
        }

        if ($fecha === '') {
            $errors[] = 'La fecha reservada es obligatoria.';
        }

        if (empty($errors)) {
            try {
                $this->reservas->insert($nombre, $telefono, $fecha);
                $success = 'Reserva creada correctamente.';
                $form = [
                    'nombre' => '',
                    'telefono' => '',
                    'fecha_reserva' => '',
                ];
            } catch (PDOException $e) {
                if ((int) $e->getCode() === 23000) {
                    $errors[] = 'Esa fecha/hora ya esta reservada.';
                } else {
                    $errors[] = 'No se pudo crear la reserva.';
                }
                error_log('Create reservation error: ' . $e->getMessage());
            } catch (Throwable $e) {
                $errors[] = 'No se pudo crear la reserva.';
                error_log('Create reservation error: ' . $e->getMessage());
            }
        }
    }

    public function handleUpdate(array &$errors, string &$success): void
    {
        // Valida y actualiza una reserva.
        $id = (int) ($_POST['update_reserva'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $fecha = trim((string) ($_POST['fecha_reserva'] ?? ''));

        if ($id <= 0) {
            $errors[] = 'Reserva invalida.';
        }
        if ($nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (strlen($nombre) > 100) {
            $errors[] = 'El nombre no puede superar 100 caracteres.';
        }
        if ($telefono !== '' && strlen($telefono) > 20) {
            $errors[] = 'El telefono no puede superar 20 caracteres.';
        }
        if ($fecha === '') {
            $errors[] = 'La fecha reservada es obligatoria.';
        }

        if (empty($errors)) {
            try {
                $this->reservas->update($id, $nombre, $telefono, $fecha);
                $success = 'Reserva actualizada correctamente.';
            } catch (PDOException $e) {
                if ((int) $e->getCode() === 23000) {
                    $errors[] = 'Esa fecha/hora ya esta reservada.';
                } else {
                    $errors[] = 'No se pudo actualizar la reserva.';
                }
                error_log('Update reservation error: ' . $e->getMessage());
            } catch (Throwable $e) {
                $errors[] = 'No se pudo actualizar la reserva.';
                error_log('Update reservation error: ' . $e->getMessage());
            }
        }
    }

    public function handleDelete(int $id): void
    {
        // Elimina una reserva.
        if ($id > 0) {
            $this->reservas->delete($id);
        }
    }
}
