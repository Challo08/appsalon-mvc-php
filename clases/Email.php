<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
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
        $mail->Port       = $_ENV['EMAIL_PORT'];



        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        // FROM válido (correo registrado en Brevo)
        $mail->setFrom('challo2341@gmail.com', 'Tendencia Peluqueria');
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            $mail->addAddress($this->email);
            $mail->Subject = 'Confirma tu cuenta';

            $contenido  = "<html>";
            $contenido .= "<p><strong>Hola {$this->nombre}</strong>, has creado tu cuenta en App Salon.</p>";
            $contenido .= "<p>Confirma tu cuenta aquí:</p>";
            $contenido .= "<p><a href='{$_ENV['APP_URL']}/confirmar-cuenta?token={$this->token}'>Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si no solicitaste esta cuenta, ignora este mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Error Email Confirmación: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function enviarInstrucciones()
    {
        $mail = new PHPMailer(true);

        try {
            $this->configurarSMTP($mail);

            $mail->addAddress($this->email);
            $mail->Subject = 'Restablece tu contraseña';

            $contenido  = "<html>";
            $contenido .= "<p><strong>Hola {$this->nombre}</strong>, solicitaste restablecer tu contraseña.</p>";
            $contenido .= "<p>Haz clic aquí:</p>";
            $contenido .= "<p><a href='{$_ENV['APP_URL']}/recuperar?token={$this->token}'>Restablecer Password</a></p>";
            $contenido .= "<p>Si no fuiste tú, ignora este mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Error Email Recuperación: " . $mail->ErrorInfo);
            return false;
        }
    }
}
