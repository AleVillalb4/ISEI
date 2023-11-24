<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;


class MiPerfilController extends Controller
{

    public function actionIndex($var = null)
    {
        //   echo 'Estoy en mi perfil ';
        // echo $_SESSION['user']['tipo'];
        // echo $_SESSION['user']['nombre'];
        // echo $_SESSION['user']['apellido'];
        switch ($_SESSION['user']['tipo']) {
            case 'administrador':
                $this->perfilAdmin();
                break;
            case 'alumno':
                $this->perfilAlumno();
                break;
            default:
                echo 'No puede ser default';
                break;
        }
    }

    protected function perfilAdmin()

    {
        //Pasar la vista con los parametros de sesion como sesion.
        //  echo 'Bienvenido Administrador ' . $_SESSION['user']['nombre'].' ' . $_SESSION['user']['apellido'];
        static::path();
        $nombre_de_archivoDeVista = 'adminuser';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "ruta" => self::$path,
            "nombre" => $_SESSION['user']['nombre'],
            "tipo" => $_SESSION['user']['tipo'],

        ]; //array con parametros
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);


    }


    protected function perfilAlumno()
    {
        echo 'Bienvenido Alumno ' . $_SESSION['user']['nombre'] . ' ' . $_SESSION['user']['apellido'];
    }


    public function actionMisDatos($var = null)
    {
        var_dump($_SESSION);
        if ($_SESSION['status'] === true) {
            echo 'Tus datos son estos ';
            $usuario = UserController::getUser($_SESSION['user']['id_usuario']);
            echo "<pre>";
            var_dump($usuario);
            echo "</pre>";
        } else {
            //podria ir al login porque se necesita la sesión, ojo que si vas al 404 te crea una sesion pero con un status false.
            //$this->action404();
        }
    }


}
