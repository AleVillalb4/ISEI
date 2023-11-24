<?php 
namespace app\controllers;
use \Controller;

class SiteController extends Controller
{
    // Constructor
    public function __construct(){
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

	public static function head(){
		static::ruta();
		$camino = self::$ruta;
		$camino = str_replace('&', '', $camino);
		$head = file_get_contents(APP_PATH.'/views/inc/head.php');
		$head = str_replace('#PATH#', $camino, $head);
		return $head;
	}

	public static function menu(){
	}

	public static function header($from_request = null){
	}

	public static function footer(){
		static::path();
		$var = '';
		$footer = file_get_contents(APP_PATH.'/views/inc/footer.php');
		$footer_registro = 'un dato';
		$arr = array(
		    "#var#"		=> $var,
		    "#FOOTER_DATO#" => $footer_registro,
		    "#PATH#" 			=> self::$path);
		$footer = strtr($footer,$arr);
		return $footer;
	}

}
