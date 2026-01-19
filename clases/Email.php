<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email  = $email;
        $this->nombre = $nombre;
        $this->token  = $token;
    }

    private function configurarSMTP(PHPMailer $mail)
    {
        $mail->isSMTP();
        $mail->Host       = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USER'];
        $mail->Password   = $_ENV['EMAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $_ENV['EMAIL_PORT'];

        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Timeout = 30;

        // FROM debe ser el mismo correo de Gmail
        $mail->setFrom('tendencia01@tendencia-peluqueria.online', 'Tendencia Peluqueria');
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirma tu cuenta';

            $mail->Body = "
                <p><strong>Hola {$this->nombre}</strong></p>
                <p>Has creado tu cuenta en App Salon.</p>
                <p>
                    <a href='{$_ENV['APP_URL']}/confirmar-cuenta?token={$this->token}'>
                        Confirmar Cuenta
                    </a>
                </p>
                <p>Si no solicitaste esta cuenta, ignora este mensaje.</p>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Email confirmación error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public function enviarInstrucciones()
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Restablecer contraseña';

            $mail->Body = "
                <p><strong>Hola {$this->nombre}</strong></p>
                <p>Solicitaste restablecer tu contraseña.</p>
                <p>
                    <a href='{$_ENV['APP_URL']}/recuperar?token={$this->token}'>
                        Restablecer contraseña
                    </a>
                </p>
                <p>Si no fuiste tú, ignora este mensaje.</p>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Email recuperación error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public function confirmarCita($fecha, $hora, $servicios, $total)
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirmación de Cita - App Salón';

            $listaServicios = '<ul>';
            foreach ($servicios as $servicio) {
                $listaServicios .= "<li>{$servicio->nombre} - $"
                    . number_format($servicio->precio, 0, ',', '.') . "</li>";
            }

            $listaServicios .= '</ul>';

            // Contenido del correo
            $mail->Body = "
            <h2>Hola {$this->nombre} 👋</h2>

            <p>Tu cita ha sido <strong>confirmada exitosamente</strong>.</p>

            <h3>📋 Resumen de la cita</h3>

            <p><strong>📅 Fecha:</strong> {$fecha}</p>
            <p><strong>⏰ Hora:</strong> {$hora}</p>

            <h4>💇 Servicios seleccionados:</h4>
            {$listaServicios}

            <p><strong>💰 Total a pagar:</strong> $
                " . number_format($total, 0, ',', '.') . "
            </p>

            <hr>

            <p>Gracias por confiar en <strong>Tendencia Peluqueria</strong> ✂️</p>
        ";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Email de confirmación de citas error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public function citaAgendada($fecha, $hora, $servicios, $total)
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            // 📩 DESTINATARIO FIJO (ADMINISTRADOR)
            $mail->addAddress(
                'challo2341@gmail.com',
                'Administrador App Salon'
            );

            $mail->Subject = '📢 Nueva cita agendada';

            // Servicios
            if (!is_array($servicios) || empty($servicios)) {
                $listaServicios = '<p>No se registraron servicios</p>';
            } else {
                $listaServicios = '<ul>';
                foreach ($servicios as $servicio) {
                    $listaServicios .= "<li>{$servicio->nombre} - $"
                        . number_format($servicio->precio, 0, ',', '.') . "</li>";
                }
                $listaServicios .= '</ul>';
            }

            // Contenido del correo
            $mail->Body = "
            <h2>📅 Nueva cita agendada</h2>

            <p><strong>Cliente:</strong> {$this->nombre}</p>
            <p><strong>Email del cliente:</strong> {$this->email}</p>

            <p><strong>Fecha:</strong> {$fecha}</p>
            <p><strong>Hora:</strong> {$hora}</p>

            <h4>Servicios:</h4>
            {$listaServicios}

            <p><strong>Total:</strong> $
                " . number_format($total, 0, ',', '.') . "
            </p>

            <hr>
            <p>Este correo fue generado automáticamente por App Salon.</p>
        ";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Error citaAgendada: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
