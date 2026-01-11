<?php

namespace Controllers;

use MVC\Router;

class CitaController
{
    public static function index(Router $router)
    {
        session_start();
        //debuguear($_SESSION);

        isAuth(); // Comprueba que el usuario haya iniciado sesión

        // Aquí va la vista
        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
}
