<?php
namespace app\models;
use \DataBase;
use \Model;

class ClienteModel extends Model
{
	protected $table = "clientes";
	protected $primaryKey = "id_cliente";


	public static function getClientes($filtros = null){

		if (!isset($filtros['offset'])) {
		 	$offset = 0;
		 }else{
		 	$offset = $filtros['offset'];
		 }
		
		if (!isset($filtros['maxRow'])) {
		 	$maxRow = 12;
		 }else{
		 	$maxRow = $filtros['maxRow'];
		 }

		 if (!isset($filtros['buscar'])) {
		 	$buscarSql = '';
		 }else{
		 	$buscar = trim($filtros['buscar']);

		 	if (is_numeric($buscar)) {
		 		$buscarSql = "WHERE clientes.dni LIKE '%$buscar%' 
		 							OR  clientes.cuil_cuit LIKE '%$buscar%' 
		 							OR  clientes.telefono_numero LIKE '%$buscar%' 
		 							OR concat(clientes.telefono_caracteristica, clientes.telefono_numero) LIKE '%$buscar%'";
		 	}else{
		 		$buscarSql = "WHERE usuarios.nombre_usuario LIKE '%$buscar%' 
		 							OR  usuarios.apellido_usuario LIKE '%$buscar%' 
		 							OR  usuarios.email_usuario LIKE '%$buscar%' ";
		 	}
		 }
		 /*Cambiamos el tipo de consulta de un LINK a un FULLTEXT en caso de que la entrada de busqueda sea mas de 2 palabras*/
		 if (str_word_count($buscar)>1) {
		 	$buscarSql = "AND MATCH(usuarios.nombre_usuario, usuarios.apellido_usuario, usuarios.email_usuario) AGAINST ('$buscar')";
		 }


		$model = new static();

		/*Obtenemos el total de datos encontrados*/
		$sql = "SELECT clientes.*, usuarios.* from clientes
				INNER JOIN usuarios ON usuarios.id_usuario = clientes.id_usuario $buscarSql";
		$totalResultados = DataBase::rowCount($sql);
		/*Fin Obtenemos el total de datos encontrados - todas las filas*/


		$sql = "SELECT clientes.*, usuarios.* from clientes
				INNER JOIN usuarios ON usuarios.id_usuario = clientes.id_usuario $buscarSql ORDER BY usuarios.apellido_usuario ASC LIMIT $maxRow OFFSET $offset ";

		// echo "$sql";
		$result = DataBase::getRecords($sql);


		if ($result['status'] == true) {
			$resultado['status'] = $result['status'];
			$resultado['rows'] = $totalResultados;
			$resultado['resultado'] = $result['resultado'];
		}else{
			$resultado['status'] = $result['status'];
			$resultado['rows'] = $totalResultados;
			$resultado['resultado'] =$result['resultado'];
		}

		return $resultado;
	}

	public static function getClientebyToken($token)
	{
		$sql = "SELECT clientes.*, usuarios.* from clientes
				INNER JOIN usuarios ON usuarios.id_usuario = clientes.id_usuario WHERE token_usuario = '$token'";

		// echo "$sql";
		$result = DataBase::getRecord($sql);

		if ($result == false) {
			$resultado['status'] = false;
			$resultado['resultado'] = '';
		}else{
			$resultado['status'] = true;
			$resultado['resultado'] =$result;
		}

		return $resultado;
	}

	public static function getDomiciliosbyToken($token){
		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";

		$sql[] = "SELECT domicilios_cliente.*, localidades.localidad, localidades.codigopostal, provincias.provincia FROM domicilios_cliente 
					INNER JOIN provincias on provincias.id_provincia = domicilios_cliente.id_provincia
					INNER JOIN localidades on localidades.idDeProvLocalidad = domicilios_cliente.idDeProvLocalidad
					WHERE id_cliente = @id_cliente";


		// --------------------------------------
		try {
				$resultado = DataBase::transactionReturn($sql);
				$result['state'] = $resultado['state']; //restulado puede ser TRUE si esta todo bien o FALSE
				$result['notification'] = $resultado['notification'];
				$result['resultado'] = $resultado['resultado'];
		} catch (Exception $e) {
			 // echo 'Error en Archivo: ',  $e->getMessage(), "\n"; DESCOMENTAR PARA VER ERRORES
			  	$result['notification'] = $e->getMessage();
			 	$result['state']  = false;
		}

		return $result;
	}

	public static function agregarDomicilio($datos){
		$token = $datos['token'];

		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";

					// cliente_direccion_cp
					// cliente_direccion_provincia
					// 
					// cliente_direccion_calle
					// cliente_direccion_numero
					// token

					// cliente_direccion_calle
					// cliente_direccion_calle1
					// cliente_direccion_calle2

					// cliente_direccion_indicaciones_adicionales
					// cliente_direccion_piso_depto

					// cliente_direccion_tel_alt
					// cliente_direccion_trabajo_casa
		$datos['cliente_direccion_calle2'] = htmlspecialchars($datos['cliente_direccion_calle2'], ENT_QUOTES);

		$sql[] = "INSERT INTO domicilios_cliente(id_cliente,
											    idDeProvLocalidad,
											    id_provincia,
											    Calle_Avenida,
											    numero_altura,
											    sin_numero,
											    piso_departamento,
											    telefono_alt,
											    entre_calle_1,
											    entre_calle_2,
											    casa_trabajo,
											    indicacioens_adicionales,
											    predeterminada)
					VALUES(@id_cliente,
							".$datos['form_LocalidadesProvincias'].",
							".$datos['cliente_direccion_provincia'].",
							'".$datos['cliente_direccion_calle']."',
							'".$datos['cliente_direccion_numero']."',
							'".$datos['cliente_direccion_numero_sn']."',
							'".$datos['cliente_direccion_piso_depto']."',
							'".$datos['cliente_direccion_tel_alt']."',
							'".$datos['cliente_direccion_calle1']."',
							'".$datos['cliente_direccion_calle2']."',
							'".$datos['cliente_direccion_trabajo_casa']."',
							'".$datos['cliente_direccion_indicaciones_adicionales']."',
							'".$datos['cliente_direccion_predeterminada']."')";

		// var_dump($sql);
		// --------------------------------------
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

	public static function getDomicilioByTokenyID($token, $id_domicilio){
		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";

		$sql[] = "SELECT domicilios_cliente.*, localidades.localidad, localidades.codigopostal, provincias.provincia FROM domicilios_cliente 
					INNER JOIN provincias on provincias.id_provincia = domicilios_cliente.id_provincia
					INNER JOIN localidades on localidades.idDeProvLocalidad = domicilios_cliente.idDeProvLocalidad
					WHERE id_cliente = @id_cliente AND domicilios_cliente.id_domicilio = $id_domicilio";


		// --------------------------------------
		try {
				$resultado = DataBase::transactionReturn($sql);
				$result['state'] = $resultado['state']; //restulado puede ser TRUE si esta todo bien o FALSE
				$result['notification'] = $resultado['notification'];
				$result['resultado'] = $resultado['resultado'];
		} catch (Exception $e) {
			 // echo 'Error en Archivo: ',  $e->getMessage(), "\n"; DESCOMENTAR PARA VER ERRORES
			  	$result['notification'] = $e->getMessage();
			 	$result['state']  = false;
		}

		return $result;
	}

	public static function editarDomicilio($datos){
		$token = $datos['token'];

		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";

		$datos['cliente_direccion_calle2'] = htmlspecialchars($datos['cliente_direccion_calle2'], ENT_QUOTES);

		$sql[] = "UPDATE domicilios_cliente 
					SET 
						idDeProvLocalidad=".$datos['form_LocalidadesProvincias'].",
						id_provincia=".$datos['cliente_direccion_provincia'].",
						Calle_Avenida='".$datos['cliente_direccion_calle']."',
						numero_altura='".$datos['cliente_direccion_numero']."',
						sin_numero='".$datos['cliente_direccion_numero_sn']."',
						piso_departamento='".$datos['cliente_direccion_piso_depto']."',
						telefono_alt='".$datos['cliente_direccion_tel_alt']."',
						entre_calle_1='".$datos['cliente_direccion_calle1']."',
						entre_calle_2='".$datos['cliente_direccion_calle2']."',
						casa_trabajo='".$datos['cliente_direccion_trabajo_casa']."',
						indicacioens_adicionales='".$datos['cliente_direccion_indicaciones_adicionales']."'
					 WHERE id_cliente = @id_cliente and id_domicilio = ".$datos['domicilio']."";

		// var_dump($sql);
		// --------------------------------------
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

	public static function eliminarDomicilio($token, $id_domicilio){

		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";


		$sql[] = "DELETE FROM domicilios_cliente WHERE id_cliente =  @id_cliente AND id_domicilio = $id_domicilio";

		// var_dump($sql);
		// --------------------------------------
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

	public static function defaultDomicilio($token, $id_domicilio){

		$model = new static();
		/*Se obtiene la id del udusario desde su token*/
		$sql[] = "SET @id_usuario = (SELECT id_usuario FROM usuarios WHERE token_usuario = '$token')";

		$sql[] = "SET @id_cliente = (SELECT id_cliente FROM clientes WHERE id_usuario = @id_usuario)";

		$sql[] = "UPDATE domicilios_cliente SET predeterminada='no' WHERE  id_cliente =  @id_cliente";

		$sql[] = "UPDATE domicilios_cliente SET predeterminada='si' WHERE  id_cliente =  @id_cliente AND id_domicilio = $id_domicilio";
		// var_dump($sql);
		// --------------------------------------
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
