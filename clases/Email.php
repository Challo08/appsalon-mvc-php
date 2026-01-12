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
        $mail->setFrom('challo2341@gmail.com', 'Tendencia Peluqueria');
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
}
