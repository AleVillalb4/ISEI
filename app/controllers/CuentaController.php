<?php

namespace app\controllers;

use app\controllers\UserController;
use app\models\UserModel;
use \Controller;
use \Response;
use \DataBase;




class CuentaController extends Controller
{
    public function actionIndex($var = null)
    {
        $this->actionLogin();
    }


    public function actionLogin()
    {
        static::path();
        $error = false;
        $datos['email']         = '';
        $datos['pass']          = '';

        $error_msg = array('email' => '', 'pass' => '');

        if (isset($_POST['send'])) {
            $datos['email']     = $_POST['email'];
            $datos['pass']     = $_POST['pass'];
            //validacion email
            $emailValidation = UserController::checkEmpty($datos['email'], 'e-mail', 5, 30);
            if ($emailValidation !== true) {
                $error_msg['email'] = $emailValidation;
                $error = true;
            }

            #Contraseña Validaciones
            $passwordValidation = UserController::checkEmpty($datos['pass'], 'contraseña', 6, 15);
            if ($passwordValidation !== true) {
                $error_msg['pass'] = $passwordValidation;
                $error = true;
            }

            //Comprobar existencia de correo electronico en DB

            $checkUsuario = UserController::getUser($datos['email']);
            var_dump($checkUsuario);
            if ($checkUsuario === false) {
                $error = true;
                $error_msg['email'] = 'El email no se encuentra registrado';
            } else {
                //Existe el correo
                if ($datos['pass'] !== $checkUsuario->pass) {
                    $error = true;
                    $error_msg['pass'] = 'Email o contraseña incorrecta';
                }
            }

            if ($error !== true) {
                $_SESSION['user']['nombre'] = $checkUsuario->nombre;
                $_SESSION['user']['apellido'] = $checkUsuario->apellido;
                $_SESSION['user']['tipo'] = $checkUsuario->tipo;
                $_SESSION['status'] = true;

                header('Location:'. self::$path .'miperfil');
            }
        }

        $nombre_de_archivoDeVista = 'login';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "ruta" => self::$path,
            "error" => $error,
            "error_msg" => $error_msg,
            "datos" => $datos
        ]; //array con parametros
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }


    public function validarCampo_ex_blank_caract($campo, $campo_descripcion = '', $minCaracteres = 0, $maxCaracteres = null)
    {
        /**
         * Valida un campo de formulario en PHP. Existencia, que no este en blanco y rango de caracteres (opcional)
         *
         * @param string $campo El nombre del campo a validar, el indice asociativo que se espera encontrar en la global $_POST.
         * @param int $minCaracteres (Opcional) El número mínimo de caracteres permitidos. Por defecto: 0.
         * @param int|null $maxCaracteres (Opcional) El número máximo de caracteres permitidos. Por defecto: null.
         *
         * @return array Un array con el estado de validación, el mensaje de error correspondiente y si es necesario el valor del campo saneado.
         * El estado es verdadero (true) si hay un error, y falso (false) si no hay errores.
         * El mensaje de error indica el motivo específico del error, o está vacío si no hay errores.
         */


        //si el etado de la validación llega sin cambios al final, se entiende que no hay errores

        $resultado = array(
            'estado' => true,
            'mensaje' => '',
            'valor' => ''
        );

        // Verificar si el campo existe
        if (!isset($_POST[$campo])) {
            $resultado['estado'] = true;
            $resultado['mensaje'] = 'Por favor, ingrese un ' . $campo_descripcion;
            return $resultado;
        } else {
            //El campo existe, se lo asigna a una variabla $campo
            $campo = $_POST[$campo];
        }

        // Verificar si el campo está en blanco
        $campo = trim($campo);
        if (empty($campo)) {
            $resultado['estado'] = true;
            $resultado['mensaje'] = 'El campo ' . $campo_descripcion . ' está en blanco.';
            return $resultado;
        } else {
            $resultado['valor'] = $campo;
        }

        // Saneamiento de caracteres especiales
        $campo = htmlspecialchars($campo, ENT_QUOTES, 'UTF-8');

        // Verificar longitud mínima y máxima del campo
        $longitud = strlen($campo);
        if ($minCaracteres > 0 && $longitud < $minCaracteres) {
            $resultado['estado'] = true;
            $resultado['mensaje'] = 'El campo ' . $campo_descripcion . ' debe tener al menos ' . $minCaracteres . ' caracteres.';
            return $resultado;
        }
        if ($maxCaracteres && $longitud > $maxCaracteres) {
            $resultado['estado'] = true;
            $resultado['mensaje'] = 'El campo ' . $campo_descripcion . ' debe tener como máximo ' . $maxCaracteres . ' caracteres.';
            return $resultado;
        } else {
            //$maxCaracteres es NULL
        }

        // Validación exitosa, devolver estado falso sin mensaje de error
        $resultado['estado'] = false;
        return $resultado;
    }



    public function actionAgregarAdmin()
    {

        $error = false;
        $error_msg = array();

        $error_msg['nombre'] = '';
        $error_msg['apellido'] = '';
        $error_msg['email'] = '';
        $error_msg['pass'] = '';
        $error_msg['pass_check'] = '';

        $datos['nombre']        = '';
        $datos['apellido']      = '';
        $datos['email']         = '';
        $datos['pass']          = '';
        $datos['pass_check']    = '';
        $tipo['tipo']    = '';

        $mensajeOk = '';
        $mensajeNoOk = '';

        if (isset($_POST['add'])) {
            $datos['nombre']    = $_POST['nombre'];
            $datos['apellido']  = $_POST['apellido'];
            $datos['email']     = $_POST['email'];
            $datos['pass']      = $_POST['pass'];
            $datos['pass_check'] = $_POST['pass_check'];

            // Validaciones de campo

            #Nombre Validaciones
            $nombreValidation = UserController::checkEmpty($datos['nombre'], 'nombre', 3, 30);
            if ($nombreValidation !== true) {
                $error_msg['nombre'] = $nombreValidation;
                $error = true;
            }

            #Apellido Validaciones
            $apellidoValidation = UserController::checkEmpty($datos['apellido'], 'apellido', 3, 30);
            if ($apellidoValidation !== true) {
                $error_msg['apellido'] = $apellidoValidation;
                $error = true;
            }

            #Email Validaciones
            $emailValidation = UserController::checkEmpty($datos['email'], 'e-mail', 6, 30);
            if ($emailValidation !== true) {
                $error_msg['email'] = $emailValidation;
                $error = true;
            } else {
                $emailValidation = UserController::checkEmail($datos['email'], $verificarDuplicado = true);
                if ($emailValidation !== true) {
                    $error_msg['email'] = 'El email ya se encuentra en uso';
                    $error = true;
                }
            }

            #Contraseña Validaciones
            $passwordValidation = UserController::checkEmpty($datos['pass'], 'contraseña', 6, 15);
            if ($passwordValidation !== true) {
                $error_msg['pass'] = $passwordValidation;
                $error = true;
            }

            $passwordConfirmationValidation = UserController::checkEmpty($datos['pass_check'], 'confirmación contraseña', 6, 15);
            if ($passwordConfirmationValidation !== true) {
                $error_msg['pass_check'] = $passwordConfirmationValidation;
                $error = true;
            }

            if (UserController::checkPasswordsMatch($datos['pass'], $datos['pass_check']) !== true) {
                $error_msg['pass_check'] = 'Las contraseñas no coinciden';
                $error = true;
            }


            //se comprueba si no hay errors y se procede a guardar en la base de datos        
            if ($error !== true) {
                // esta todo bien
                $crearAdmin = AdminController::addAdmin($datos, $tipo = 'administrador');
                //Devuelve un array asociativo con 2 indices "state" y "notification", si state esta en true, todo bien
                if ($crearAdmin['state'] === true) {
                    $mensajeOk = 'Adiministrador registrado con éxito!';
                } else {
                    $mensajeNoOk = 'Error registrando Usuario';
                }
            }
        }



        // Renderizar vista

        static::path();
        $nombre_de_archivoDeVista = 'altaAdmin';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "ruta" => self::$path,
            "error_msg" => $error_msg,
            "error" => $error,
            "datos" => $datos,
            "mensajeOk" => $mensajeOk,
            "mensajeNoOk" => $mensajeNoOk
        ]; //array con parametros
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }



    public function actionAgregarAlumno()
    {

        $error = false;
        $error_msg = array();

        $error_msg['nombre'] = '';
        $error_msg['apellido'] = '';
        $error_msg['email'] = '';
        $error_msg['pass'] = '';
        $error_msg['pass_check'] = '';

        $datos['nombre']        = '';
        $datos['apellido']      = '';
        $datos['email']         = '';
        $datos['pass']          = '';
        $datos['pass_check']    = '';
        $tipo['tipo']    = '';

        $mensajeOk = '';
        $mensajeNoOk = '';

        if (isset($_POST['add'])) {
            $datos['nombre']    = $_POST['nombre'];
            $datos['apellido']  = $_POST['apellido'];
            $datos['email']     = $_POST['email'];
            $datos['pass']      = $_POST['pass'];
            $datos['pass_check'] = $_POST['pass_check'];

            // Validaciones de campo

            #Nombre Validaciones
            $nombreValidation = UserController::checkEmpty($datos['nombre'], 'nombre', 3, 30);
            if ($nombreValidation !== true) {
                $error_msg['nombre'] = $nombreValidation;
                $error = true;
            }

            #Apellido Validaciones
            $apellidoValidation = UserController::checkEmpty($datos['apellido'], 'apellido', 3, 30);
            if ($apellidoValidation !== true) {
                $error_msg['apellido'] = $apellidoValidation;
                $error = true;
            }

            #Email Validaciones
            $emailValidation = UserController::checkEmpty($datos['email'], 'e-mail', 6, 30);
            if ($emailValidation !== true) {
                $error_msg['email'] = $emailValidation;
                $error = true;
            } else {
                $emailValidation = UserController::checkEmail($datos['email'], $verificarDuplicado = true);
                if ($emailValidation !== true) {
                    $error_msg['email'] = 'El email ya se encuentra en uso';
                    $error = true;
                }
            }

            #Contraseña Validaciones
            $passwordValidation = UserController::checkEmpty($datos['pass'], 'contraseña', 6, 15);
            if ($passwordValidation !== true) {
                $error_msg['pass'] = $passwordValidation;
                $error = true;
            }

            $passwordConfirmationValidation = UserController::checkEmpty($datos['pass_check'], 'confirmación contraseña', 6, 15);
            if ($passwordConfirmationValidation !== true) {
                $error_msg['pass_check'] = $passwordConfirmationValidation;
                $error = true;
            }

            if (UserController::checkPasswordsMatch($datos['pass'], $datos['pass_check']) !== true) {
                $error_msg['pass_check'] = 'Las contraseñas no coinciden';
                $error = true;
            }


            //se comprueba si no hay errors y se procede a guardar en la base de datos        
            if ($error !== true) {
                // esta todo bien
                $crearAlumno = AlumnoController::addAlumno($datos, $tipo = 'alumno');
                //Devuelve un array asociativo con 2 indices "state" y "notification", si state esta en true, todo bien
                if ($crearAlumno['state'] === true) {
                    $mensajeOk = 'Alumno registrado con éxito!';
                } else {
                    $mensajeNoOk = 'Error registrando Usuario';
                }
            }
        }



        // Renderizar vista

        static::path();
        $nombre_de_archivoDeVista = 'altaAlumno';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "ruta" => self::$path,
            "error_msg" => $error_msg,
            "error" => $error,
            "datos" => $datos,
            "mensajeOk" => $mensajeOk,
            "mensajeNoOk" => $mensajeNoOk
        ]; //array con parametros
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }



    public function actionMostrar()
    {


        $email['user']['id_usuario'] = $usuario->id_usuario;
        $email['user']['nombre'] = $usuario->nombre;
        $email['user']['apellido'] = $usuario->apellido;
        $email['user']['tipo'] = $usuario->tipo;

        // Obtener datos asociados al email
        $usuario = UserModel::findEmail($email);



        echo "Información del Usuario:<br>";
        echo "Nombre: " . $datos->nombre . "<br>";
        echo "Email: " . $datos->apellido . "<br>";
        echo "Nombre: " . $datos->email . "<br>";
        echo "Email: " . $datos->pass . "<br>";


        static::path();
        $nombre_de_archivoDeVista = 'mostrargpt';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "ruta" => self::$path
        ]; //array con parametros
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
}
