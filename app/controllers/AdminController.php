<?php
namespace app\controllers;
use \Controller;
use \Response;
use \DataBase;


class AdminController extends UserController
{
    /*  .....................
	Atributos Principales
	.....................
*/
    protected $id_admin;

    /*  .....................
	FIN - Atributos Principales
	.....................
*/

    public static function addAdmin($datos, $tipo){
        $agregarAdmin = self::add($datos, $tipo);
        return  $agregarAdmin;
    }

    
}
