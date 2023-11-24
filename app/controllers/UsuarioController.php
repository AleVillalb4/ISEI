<?php
namespace app\controllers;
use \Controller;
use \Response;
use \DataBase;
use app\models\UsuarioModel;


class UsuarioController extends Controller
{
    /*  .....................
	Atributos Principales
	.....................
*/
    protected static $nombre;
    protected static $apellido;
    protected static $password;
    protected static $email;
    protected static $telefono;
    protected static $estado;
    protected static $userType;

    /*  .....................
	FIN - Atributos Principales
	.....................
*/


/*    public function actionIndex($var = null)    {
        echo 'Hola';
    }
*/
    public function setNombre($nombre){self::$nombre = $nombre;}

    public function setApellido($apellido){self::$apellido = $apellido;}

    public function setPassword($password){self::$password = $password;}

    public function setEmail($email){self::$email = $email;}

    public function setTelefono($telefono){self::$telefono = $telefono;}

    public function setEstado($estado)
    {
        self::$estado = $estado;
    }

    public function setUserType($userType)
    {
        self::$userType = $userType;
    }

    public function getNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getApellido($apellido){
        $this->apellido = $apellido;
    }

    public function getPassword($password){
        $this->password = $password;
    }

    public function getEmail($email){
        $this->email = $email;
    }

    public function getTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function getEstado($estado){
        $this->estado = $estado;
    }

    public function getUserType($userType){
        $this->userType = $userType;
    }


    public static function add(){
        $result = UsuarioModel::add(self::$nombre,
                                    self::$apellido,
                                    self::$password,
                                    self::$email,
                                    self::$telefono,
                                    self::$estado,
                                    self::$userType,
                                    $userData = []
                                );

        return $result;
    }
}
