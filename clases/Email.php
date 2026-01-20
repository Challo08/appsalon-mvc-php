<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public $email;
    public $nombre;
    public $apellido;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email  = $email;
        $this->nombre = $nombre;
        $this->token  = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //$mail->setFrom($_ENV['EMAIL_USER']);
        $mail->setFrom('cuentas@appsalon.com');
        // $mail->addAddress($this->email, $this->nombre);
        $mail->addAddress('cuentas@appsalon.com', $this->nombre);
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='{$_ENV['APP_URL']}/confirmar-cuenta?token={$this->token}'>Confirmar Cuenta</a>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function enviarInstrucciones()
    {

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //$mail->setFrom($_ENV['EMAIL_USER']);
        $mail->setFrom('cuentas@appsalon.com');
        // $mail->addAddress($this->email, $this->nombre);
        $mail->addAddress('cuentas@appsalon.com', $this->nombre);
        $mail->Subject = 'Restablecer contraseña';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Solicitaste restablecer tu contraseña, solo debes presionar en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='{$_ENV['APP_URL']}/recuperar?token={$this->token}'>Restablecer contraseña</a>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function confirmarCita($fecha, $hora, $servicios, $total)
    {

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //$mail->setFrom($_ENV['EMAIL_USER']);
        $mail->setFrom('cuentas@appsalon.com');
        // $mail->addAddress($this->email, $this->nombre);
        $mail->addAddress('cuentas@appsalon.com', $this->nombre);
        $mail->Subject = 'Confirmación de cita - App Salón';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $listaServicios = '<ul>';
        foreach ($servicios as $servicio) {
            $listaServicios .= "<li>{$servicio->nombre} - $"
                . number_format($servicio->precio, 0, ',', '.') . "</li>";
        }

        $listaServicios .= '</ul>';

        $contenido = '<html>';
        $contenido .= "<p>Hola <strong>" . $this->nombre . "</strong> Tu cita ha sido <strong>confirmada exitosamente</strong></p>";
        $contenido .= "<h3>📋 Resumen de la cita:</h3>";
        $contenido .= "<p><strong>📅 Fecha:</strong> {$fecha}</p>";
        $contenido .= "<p><strong>⏰ Hora:</strong> {$hora}</p>";
        $contenido .= "<h4>💇 Servicios seleccionados:</h4>";
        $contenido .= "{$listaServicios}";
        $contenido .= "<p><strong>💰 Total a pagar:</strong> $" . number_format($total, 0, ',', '.') . "</p>";
        $contenido .= "<hr>";
        $contenido .= "<p>Gracias por confiar en <strong>Tendencia Peluqueria</strong> ✂️</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function citaAgendada($fecha, $hora, $servicios, $total)
    {

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //$mail->setFrom($_ENV['EMAIL_USER']);
        $mail->setFrom('cuentas@appsalon.com');
        // $mail->addAddress($this->email, $this->nombre);
        $mail->addAddress('cuentas@appsalon.com', $this->nombre);
        $mail->Subject = '📢 Nueva cita agendada';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

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

        $contenido = '<html>';
        $contenido .= "<h2>📅 Nueva cita agendada</h2>";
        $contenido .= "<p><strong>Cliente:</strong> {$this->nombre}</p>";
        $contenido .= "<p><strong>Email del cliente:</strong> {$this->email}</p>";
        $contenido .= "<p><strong>Fecha:</strong> {$fecha}</p>";
        $contenido .= "<p><strong>Hora:</strong> {$hora}</p>";
        $contenido .= "<h4>Servicios:</h4>";
        $contenido .= "{$listaServicios}";
        $contenido .= "<p><strong>Total:</strong> $" . number_format($total, 0, ',', '.') . "</p>";
        $contenido .= "<hr>";
        $contenido .= "<p>Este correo fue generado automáticamente por App Salon.</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }
}
