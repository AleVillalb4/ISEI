<?php 
namespace app\controllers;
use \Controller;
use \Exception; //Permite usar Exception
use \ErrorException; //Permite usar Exception

class SessionController extends Controller
{
   
    private static function setSession(){
        #SC-01 Al primer ingreso de un usuario al sistema asigna un estado de FALSE a la variable de control STATUS
    	if (!isset($_SESSION['SESSION']['STATUS'])) {
    		$_SESSION['SESSION']['STATUS'] = false;
    	}

        if (!isset($_SESSION['USER']['type'])) {
            $_SESSION['USER']['type'] = 'Invitado';
        }
    }

	public static function sessionVerificacion(){
		self::setSession();

        #SC-02 Devuelve el estado de session (Logueado o No) del usuario como un string "OnLine" o "OffLine"
		if ($_SESSION['SESSION']['STATUS']) {
			$status = 'Online';
		}else{
			$status = 'OffLine';
		}
		
		return $status;
	}

    public static function setSessionData($userEmailOrID){

        	$usuario = UserController::getUser($userEmailOrID);

            if (isset($usuario->nombre_usuario) && isset($usuario->apellido_usuario) && isset($usuario->tipo_usuario) && isset($usuario->id_usuario)) {
                
                session_regenerate_id(true);

            	$_SESSION['SESSION']['STATUS'] = true;
            	$_SESSION['USER']['name'] 		= $usuario->nombre_usuario;
            	$_SESSION['USER']['lastName'] 	= $usuario->apellido_usuario;
            	$_SESSION['USER']['type'] 		= $usuario->tipo_usuario;
            	$_SESSION['USER']['id_usuario']	= $usuario->id_usuario;
                $_SESSION['USER']['token']       = $usuario->token_usuario;
                $result = true;
            }else{
                $result = false;
            }

        return $result;
    }

    public static function onlyAdmins(){
        static::path();
        $continuar = false;
        /*Verificación de cuenta de usuario--------------*/
        $ruta = self::$path.'404';
        if (self::$sessionStatus === 'OffLine') {
            header("Location: $ruta");
        }elseif(self::$sessionStatus === 'Online'){
            if ($_SESSION['USER']['type'] !=  'administrador') {
                header("Location: $ruta");
            }else{
                $continuar = true;
            }
        }
        /*Verificación de cuenta de usuario-----------FIN*/

        return $continuar;
    }

    public static function onlySeller(){
        static::path();
        $continuar = false;
        /*Verificación de cuenta de usuario--------------*/
        $ruta = self::$path.'404';
        if (self::$sessionStatus === 'OffLine') {
            header("Location: $ruta");
        }elseif(self::$sessionStatus === 'Online'){
            if ($_SESSION['USER']['type'] !=  'vendedor') {
                header("Location: $ruta");
            }else{
                $continuar = true;
            }
        }
        /*Verificación de cuenta de usuario-----------FIN*/

        return $continuar;
    }

    public static function onlyUsers(){
        static::path();
        $continuar = false;
        /*Verificación de cuenta de usuario--------------*/
        $ruta = self::$path.'404';
        if (self::$sessionStatus === 'OffLine') {
            header("Location: $ruta");
        }elseif(self::$sessionStatus === 'Online'){
            if ($_SESSION['USER']['type'] ==  'administrador' || $_SESSION['USER']['type'] ==  'cliente') {
                $continuar = true;
            }else{
                header("Location: $ruta");
            }
        }
        /*Verificación de cuenta de usuario-----------FIN*/

        return $continuar;
    }
}