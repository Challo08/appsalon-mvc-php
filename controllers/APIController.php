<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController
{
    public static function index()
    {
        $servicios = Servicio::all();
        //debuguear($servicios); // Un arreglo asociativo en PHP es igual a un objeto en JS
        echo json_encode($servicios, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function guardar()
    {
        //Almacena la cita y devuelve el id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        //Almacena las citas y el servicio

        //Almacena los servicios con el id de la cita
        $idServicios = explode(",", $_POST['servicios']); // Se usa explode para convertirlo a un arreglo

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServico = new CitaServicio($args);
            $citaServico->guardar();
        }



        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar()
    {
        //debuguear($_POST);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id']; //Se lee el id
            $cita = Cita::find($id); //Se encuentra el id
            $cita->eliminar(); // Se elimina el id
            //debuguear($cita);
            header('Location:' . $_SERVER['HTTP_REFERER']); // Se redireccionar a la misma página
        }
    }
}
