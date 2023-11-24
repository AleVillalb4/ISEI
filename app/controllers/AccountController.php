<?php 
namespace app\controllers;
use \Controller;
use \Response;
use app\models\AccountModel;

class AccountController extends Controller
{
    // Constructor
    public function __construct(){
    	self::$sessionStatus = SessionController::sessionVerificacion();
    }

    public function actionIndex($var = null){
    	$this->actionLogin();
	}

    public function actionRegistro($id = null){

	}

	public function actionLogin(){
		static::path();

		if (self::$sessionStatus === 'Online') {
			header('Location: home');

		}elseif(self::$sessionStatus === 'OffLine'){
			$ejemplo = 123;
			Response::render($this->viewDir(__NAMESPACE__),"login", ["verEjemplo" => $ejemplo,
																		"title" => $this->title.' Cuenta',
																	 "headerTitle" => "Iniciar Sesión",
																	 "head" => SiteController::head(),
																	 "js" => self::loadJS(["path" => self::$path], 'account'),
																	 "jsLibreria" => self::loadLibreria(["path" => self::$path],'js','jquery.serializeObject.min')
	 																]);
		}
	}

	public function actionLogout(){
		session_unset();
		session_destroy();
		$_SESSION['SESSION']['STATUS'] = false;
		header('Location: ../home');
	}

}
