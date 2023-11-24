<?php

namespace app\models;

use \DataBase;
use \Model;

class UserModel extends Model
{
	protected $table = "usuarios";
	protected $primaryKey = "id_usuario";
	protected $secundaryKey = "email";
	public $email;

	public static function add($datos, $tipo)
	{
		$model = new static();
		// echo "<pre>";
		// var_dump($userData);
		// echo "</pre>";
		$email 		= $datos['email'];
		$pass 	= $datos['pass'];
		$nombre 	= $datos['nombre'];
		$apellido 	= $datos['apellido'];
		$tipo 		= $tipo;

		$sql[] = "INSERT INTO $model->table (nombre, apellido, email, pass, tipo)
							 VALUES('$nombre', '$apellido', '$email', '$pass', '$tipo');";
		$sql[] = "SET @last_id = LAST_INSERT_ID();";

		if ($tipo == 'alumno') {
			$sql[] = "INSERT INTO alumnos(id_usuario) VALUES(@last_id)";

			// $telefono_numero 			=$userData['telefono_numero'];
			// $telefono_caracteristica	=$userData['telefono_caracteristica'];
			// $telefono_ws 				=$userData['telefono_ws'];
			// $dni 						=$userData['dni'];
			// $cuil_cuit 					=$userData['cuil_cuit'];
			// $observaciones_cliente 		=$userData['observaciones_cliente'];
			// $domicilio_comercial 		=$userData['domicilio_comercial'];
			// $razon_social 				=$userData['razon_social'];
			// $razon_social_mismonombre 	=$userData['razon_social_mismonombre'];
			// $condicion_iva 				=$userData['condicion_iva'];
			// $observaciones_facturacion 	=$userData['observaciones_facturacion'];
			// $tipo_registro 				=$userData['tipo_registro'];
			// $sql[] = "INSERT INTO clientes (telefono_numero,
			// 								telefono_caracteristica,
			// 								telefono_ws,
			// 								dni,
			// 								cuil_cuit,
			// 								observaciones_cliente,
			// 								domicilio_comercial,
			// 								razon_social,
			// 								razon_social_mismonombre,
			// 								condicion_iva,
			// 								observaciones_facturacion,
			// 								tipo_registro,
			// 								id_usuario,
			// 								conversion)
			// 			 VALUES($telefono_numero,
			// 			 		$telefono_caracteristica,
			// 			 		'$telefono_ws',
			// 			 		$dni,
			// 			 		$cuil_cuit,
			// 			 		'$observaciones_cliente',
			// 			 		'$domicilio_comercial',
			// 			 		'$razon_social',
			// 			 		'$razon_social_mismonombre',
			// 			 		'$condicion_iva',
			// 			 		'$observaciones_facturacion',
			// 			 		'$tipo_registro',
			// 			 		@last_id,
			// 			 		$conversion);";

			// $sql[] = "SET @idUsuario = (SELECT id_usuario FROM usuarios WHERE email_usuario = '$email')";
			/* RE VEEER */
			// $sql[] = "INSERT INTO notificaciones (id_usuario, tipo, id_tipo)
			// 				VALUES (@idUsuario, 'Usuario - $apellido, $nombre', @idUsuario)";
			/* FIN RE VEEER */
		} elseif ($tipo == 'administrador') {
			$sql[] = "INSERT INTO administrador(id_usuario) VALUES(@last_id)";
		}

		try {
			$resultado = DataBase::transaction($sql);
			$result['state'] = $resultado['state']; //restulado puede ser TRUE si esta todo bien o FALSE
			$result['notification'] = $resultado['notification'];
		} catch (Exception $e) {
			echo 'Error en Archivo: ',  $e->getMessage(), "\n";
			$result['notification'] = $e->getMessage();
			$result['state']  = false;
		}

		return $result;
	}

	public static function findEmail($email)
	{
		$model = new static();
		$sql = "SELECT * FROM {$model->table} WHERE {$model->secundaryKey} = :email";
		$params = ["email" => $email];
		$result = DataBase::getRecord($sql, $params);
		return $result;
	}




	public static function CheckEmail($email)
	{
		$model = new static();
		$sql = "SELECT " . $model->secundaryKey . " from " . $model->table . " where " . $model->secundaryKey . " = :email";
		$params = ["email" => $email];
		$result = DataBase::query($sql, $params);

		// var_dump($result);
		if ($result) {
			$model = true;
		} else {
			$model = false;
		}
		return $model;
	}

		// public static function findEmail($email){
	// 	$model = new static();
	// 	$sql = "SELECT * from ".$model->table." where ".$model->secundaryKey." = :email";
	// 	$params = ["email" => $email];
	// 	$result = DataBase::query($sql, $params);

	// 	if ($result) {
	// 		foreach ($result as $key => $value) {
	// 			$model->$key = $value;
	// 		}
	// 	}else{
	// 		$model = false;
	// 	}
	// 	return $result[0];
	// }
}
