<?php
namespace app\models;
use \DataBase;
use \Model;

class UsuarioModel extends Model
{
	protected $table = "usuarios";
	protected $primaryKey = "id";

	public static function add($nombre, $apellido, $password, $email, $telefono, $estado,$userType,$userData){
		$model = new static();


		$sql[] = "INSERT INTO $model->table (nombre, apellido, email, pass)
							 VALUES('$nombre','$apellido','$email', '$password');";
		$sql[] = "SET @last_id = LAST_INSERT_ID();";

        switch ($userType) {
            case 'Admin':
                $sql[] = "INSERT INTO administrador (id) VALUES (@last_id);";
                break;

            case 'Superadmin':
                $sql[] = "INSERT INTO superadministrador (id_usuario) VALUES (@last_id);";
                break;

            case 'alumno':
                    $sql[] = "INSERT INTO alumnos ('legajo') VALUES (".$userData['legajo'].");";
                    break;
            default:
                # code...
                break;
        }
		try {
				$resultado = DataBase::transaction($sql);
				$result['state'] = $resultado['state']; //restulado puede ser TRUE si esta todo bien o FALSE
				$result['notification'] = $resultado['notification'];
		} catch (Exception $e) {
			 // echo 'Error en Archivo: ',  $e->getMessage(), "\n"; DESCOMENTAR PARA VER ERRORES
			  	$result['notification'] = $e->getMessage();
			 	$result['state']  = false;
		}

		return $result;
	}
}