<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;

class PanelController extends Controller
{

    public function actionIndex($var = null)
    {
        echo "Estoy en panel";
        echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
        //Destruimos la sesion
        // session_destroy();
        //Existe la sesion? Está seteado? Existe porque está seteado en el index.php
        var_dump(isset($_SESSION));

        //Instanciamos user controller con el metodo getUser para buscar como parametro el email.
            $email = "fotegui@gmail.com";
            $usuario = UserController::getUser($email);
            echo "<pre>";
            var_dump($usuario);
            echo "</pre>";

        //Al OBJETO $_session le creamos un array con indice asociativo, que dentro tiene otro array de indice asociativo, al cual luego le seteamos la variable $tipo;
        $_SESSION['user']['id_usuario'] = $usuario->id_usuario;
        $_SESSION['user']['nombre'] = $usuario->nombre;
        $_SESSION['user']['apellido'] = $usuario->apellido;
        $_SESSION['user']['tipo'] = $usuario->tipo;
        echo $_SESSION['user']['tipo'];
        var_dump(__NAMESPACE__);
        //bandera para verificar el estado de la sesión, en este caso está logueado y la variable datos de sesion esta cargado con los datos del usuario.
        $_SESSION['status'] = true;


        
    }


    public function actionLogOut($var = null)
    {
        session_destroy();
    }
}
