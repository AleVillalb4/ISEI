<?php
namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;

class HomeController extends Controller
{

	// Constructor
	public function __construct()
	{
		self::$sessionStatus = SessionController::sessionVerificacion();
	}

	public function actionIndex($var = null)
	{
		echo '<br>Estas en Index de home <br>';
		echo 'variable: '.$var;
		static::path();
		Response::render($this->viewDir(__NAMESPACE__), "index", [
			"ruta" => self::$path,
			"title" => $this->title . ' Cuenta',
			"headerTitle" => "Iniciar Sesión",
			"head" => SiteController::head(),
			
			
		]);
	}

	public function action404()
	{
		echo '404';
	}

	public function actionContacto()
	{
		echo '<br>Estas en Contacto de home <br>';
	}
}
