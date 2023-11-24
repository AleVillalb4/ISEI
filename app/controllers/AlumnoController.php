<?php
namespace app\controllers;
use \Controller;
use \Response;
use \DataBase;


class AlumnoController extends UserController
{
    /*  .....................
	Atributos Principales
	.....................
*/
    protected $id_alumno;

    /*  .....................
	FIN - Atributos Principales
	.....................
*/

    public static function addAlumno($datos, $tipo){
        $agregarAlumno = self::add($datos, $tipo);
        return  $agregarAlumno;
    }




}
