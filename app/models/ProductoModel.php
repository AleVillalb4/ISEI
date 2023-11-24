<?php
namespace app\models;
use \DataBase;
use \Model;

class ProductoModel extends Model
{
	protected $table = "productos";
	protected $primaryKey = "codigo";
	public static $productosRow;

	public static function getLastCodigo(){
		$model = new static();
		$sql = "SELECT MAX(CAST(codigo AS SIGNED)) as codigo FROM ".$model->table;
		$result = DataBase::getRecord($sql);

		if (!isset($result->codigo)) {
			$result->codigo = 0;
		}

		$result->codigo = intval($result->codigo);
		return $result->codigo;
	}

	public static function add($producto,$proveedores,$fabricante){
		$model = new static();

		/**/
		if ($fabricante['new'] === true) {
			$sql[] = "INSERT INTO fabricantes (fabricante) VALUES ('".$fabricante['fabricante']."')";
			$sql[] = "SET @fabricante_id = LAST_INSERT_ID();";
		}else{
			$sql[] = "SET @fabricante_id = ".$fabricante['fabricante'].";";
		}


		$sql[] = "INSERT INTO productos (
										codigo,
										descripcion,
										iva,
										keywords,
										detalles,
										especificaciones,
										relevancia_lista,
										fecha_alta,
										id_fabricante,
										fisico_largo_cm,
										fisico_ancho_cm,
										fisico_alto_cm,
										fisico_peso_gm
											)
							VALUES (
										'".$producto['codigo']."',
										'".$producto['descripcionTextarea']."',
										".$producto['iva'].",
										'".$producto['keywords']."',
										'".$producto['detalle_producto']."',
										'".$producto['detalle_tecnico_producto']."',
										".$producto['relevancia_lista'].",
										'".self::fechaAhora()."',
										@fabricante_id,
										".$producto['largoCm'].",
										".$producto['anchoCm'].",
										".$producto['altoCm'].",
										".$producto['peso']."
									)";

		/* SQL de STOCK SUCURSAL */
		$sql[] = "INSERT INTO stock_sucursal  (
										codigo,
										id_sucursal,
										stock,
										alerta_stock,
										ubicacion
									)
							VALUES (
										'".$producto['codigo']."',
										'1',
										".$producto['stock'].",
										".$producto['alerta_stock'].",
										".$producto['box']."
									)";
		/* FIN --- SQL de SUCURSAL */

		$grupoActivo = $producto['form_grupo'] == 0 ? 'no' : 'si';
		$sql[] = "INSERT INTO precios_producto (
												codigo,
												id_grupo,
												ganancia,
												moneda_ref_producto,
												precio_publico_pesos,
												precio_costo_pesos,
												precio_publico_dolar,
												precio_costo_dolar,
												grupo_activo
												)
							VALUES(
												'".$producto['codigo']."',
												".$producto['form_grupo'].",
												".$producto['ganancia'].",
												".$producto['moneda_ref_producto'].",
												".$producto['precio_publico_pesos'].",
												".$producto['precio_costo_pesos'].",
												".$producto['precio_publico_dolar'].",
												".$producto['precio_costo_dolar'].",
												'".$grupoActivo."'
									)";

									// echo '-----------------';
									// var_dump($producto['cantidad_minima_envio_gratis']);
									// echo '-----------------';
		$sql[] = "INSERT INTO condicionales_producto (
												codigo,
												visibilidad,

												estado_especial,
												estado_especial_tipo,
												estado_especial_des,

												aplicar_descuento,
												tipo_descuento_aplicado,
												descuento_porcentual_porcentaje,
												descuento_por_precio_importe,
												descuento_porcentual_por_cantidad_porcentaje,
												descuento_porcentual_por_cantidad_cant_minima,

												envio_gratis,
												envio_gratis_por_cant_min,
												envio_gratis_cantidad_min
												)
							VALUES(
												'".$producto['codigo']."',
												".$producto['item_visible'].",

												'".$producto['etiqueta_estado_especial']."',
												'".$producto['estadoEspecial']."',
												'".$producto['estado_especial_descripcion']."',

												'".$producto['aplicar_descuento_condicional']."',
												'".$producto['condicional_tipo_descuento']."',
												".$producto['precio_descuento_porcentaje'].",
												".$producto['precio_descuento'].",
												".$producto['descuento_porcentaje_x_cantidad'].",
												".$producto['cantidad_minima_descuento'].",

												'".$producto['envio_gratis']."',
												'".$producto['cantidad_minima_envio_gratis_estado']."',
												".$producto['cantidad_minima_envio_gratis']."
									)";

		
		/* SQL de CATEGORIAS*/
		$sql[] ="INSERT INTO categorias_producto (id_categoria, id_subcategoria,codigo)
				VALUES(".$producto["form_categoria"].",".$producto["form_subcategoria"].",".$producto['codigo'].")";
		/* FIN ------------- SQL de CATEGORIAS*/

		/* SQL de PROVEEDORES*/
		$sqlProveedores = "INSERT INTO productos_proveedores (	codigo,
																codigo_proveedor,
																id_proveedor,
																descripcion,
																precio_p_proveedor,
																precio_c_proveedor,
																precio_p_d_proveedor,
																precio_c_d_proveedor,
																link_proveedor,
																fecha_alta)
							VALUES ";
		foreach ($producto['proveedor'] as $indiceProveedor => $idProveedor) {
			$sqlProveedores .= "(".$producto['codigo'].",
								'".$producto['codigo_proveedor'][$indiceProveedor]."',
								".$producto['proveedor'][$indiceProveedor].",
								'".$producto['nombreProducto_Proveedor'][$indiceProveedor]."',
								".$producto['precio_p_proveedor'][$indiceProveedor].",
								".$producto['precio_c_proveedor'][$indiceProveedor].",
								".$producto['precio_p_d_proveedor'][$indiceProveedor].",
								".$producto['precio_c_d_proveedor'][$indiceProveedor].",
								'".$producto['link_proveedor'][$indiceProveedor]."',
								'".self::fechaAhora()."'
								)";
		    if ($indiceProveedor === array_key_last($producto['proveedor'])) {
		       $sqlProveedores .= ''; //'LAST ELEMENT!'
		    }else{
		    	$sqlProveedores .=',';
		    }
		}

		$sql[] = $sqlProveedores;

    	/* FIN ------------- SQL de PROVEEDORES*/

		/* SQL de CARACTERISTICAS ESPECIALES*/
		if ($producto["agregar_caracteristicas"] == true) {
			$sqlCaracteristicas = "INSERT INTO caracteristicas_de_producto (codigo,
																			caracteristica,
																			detalle)
								VALUES ";

			foreach ($producto["caracteristica_des"] as $iCaract => $valor) {
				$sqlCaracteristicas .= "(".$producto['codigo'].",
									'".$producto['caracteristica_des'][$iCaract]."',
									'".$producto['caracteristica_val'][$iCaract]."'
									)";
			    if ($iCaract === array_key_last($producto['caracteristica_des'])) {
			       $sqlCaracteristicas .= ''; //'LAST ELEMENT!'
			    }else{
			    	$sqlCaracteristicas .=',';
			    }
			}

			$sql[] .= $sqlCaracteristicas;
		}

    	/* FIN ------------- SQL de CARACTERISTICAS ESPECIALES*/

		// if ($producto['ArchivoRelacionado'] !== false) {
		// 	$sql[] = "INSERT INTO archivos (codigo,descripcion,tipo)VALUES(".$producto['codigo'].",'".$producto['archivo_descripcion']."','N/N')";
		// }
		
		/* SQL de Imagenes*/
			/*Agregar nueva imagen*/
			if ($producto['imagenDefault'] == true) {
				$sql[] = "INSERT INTO imagenes (codigo,img_position,img_nombre) 
						  		 VALUES (".$producto['codigo'].",0,'default_img')";
			}else{
				for ($i=0; $i < $producto['cantidad_imagenes_producto']; $i++) { 
					$sql[] = "INSERT INTO imagenes (codigo,img_position,img_nombre) 
							   VALUES (".$producto['codigo'].",$i,'".$producto['codigo']."_$i')";
				}

			}
		/* FIN ------------- SQL de Imagenes*/

		// echo '<pre>';		
		// var_dump($sql);
		// echo '</pre>';

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

	public static function edit($producto, $proveedores, $fabricante){
		$model = new static();

		/**/
		if ($fabricante['new'] === true) {
			$sql[] = "INSERT INTO fabricantes (fabricante) VALUES ('".$fabricante['fabricante']."')";
			$sql[] = "SET @fabricante_id = LAST_INSERT_ID();";
		}else{
			$sql[] = "SET @fabricante_id = ".$fabricante['fabricante'].";";
		}

		/* SQL de DATOS PRODUCTO */
			$sql[] = "UPDATE productos 
						SET descripcion 		= '".$producto['descripcionTextarea']."',
							iva 				= ".$producto['iva'].",
							keywords 			= '".$producto['keywords']."',
							detalles 			= '".$producto['detalle_producto']."',
							especificaciones 	= '".$producto['detalle_tecnico_producto']."',
							relevancia_lista 	= ".$producto['relevancia_lista'].",
							fisico_largo_cm 	= ".$producto['largoCm'].",
							fisico_ancho_cm 	= ".$producto['anchoCm'].",
							fisico_alto_cm 		= ".$producto['altoCm'].",
							fisico_peso_gm		= ".$producto['peso'].",
							fecha_ultima_mod	= '".self::fechaAhora()."',
							id_fabricante		= @fabricante_id
						WHERE codigo = '".$producto['codigo']."'";
		/* FIN --- SDATOS PRODUCTO */				

		/* SQL de STOCK SUCURSAL */
			$sql[] = "UPDATE stock_sucursal  
							SET stock 			= ".$producto['stock'].",
								alerta_stock 	= ".$producto['alerta_stock'].",
								ubicacion 		= ".$producto['box']."
							WHERE codigo = '".$producto['codigo']."'";
		/* FIN --- SQL de SUCURSAL */

		// /* SQL de PRECIOS PRODUCTO */
			$grupoActivo = $producto['form_grupo'] == 0 ? 'no' : 'si';
			$sql[] = "UPDATE precios_producto 
						SET id_grupo 				= ".$producto['form_grupo'].",
							ganancia 				= ".$producto['ganancia'].",
							moneda_ref_producto		= ".$producto['moneda_ref_producto'].",
							precio_publico_pesos	= ".$producto['precio_publico_pesos'].",
							precio_costo_pesos		= ".$producto['precio_costo_pesos'].",
							precio_publico_dolar	= ".$producto['precio_publico_dolar'].",
							precio_costo_dolar		= ".$producto['precio_costo_dolar'].",
							grupo_activo			= '".$grupoActivo."'
						WHERE codigo = '".$producto['codigo']."'";
		// /* FIN --- SQL de PRECIOS PRODUCTO */

		/* SQL de CATEGORIAS*/
			$sql[] ="UPDATE categorias_producto SET id_categoria = ".$producto["form_categoria"].", id_subcategoria = ".$producto["form_subcategoria"]." WHERE codigo = '".$producto['codigo']."'";
		/* FIN ------------- SQL de CATEGORIAS*/


		/* SQL de CARACTERISTICAS*/
			$caracteristicasEliminar = array();
			$caracteristicasAgregar = '';

		
			/* MODIFICAR CARACTERISTICAS */
				/*Se arma el SQL responsable de actualizar los datos de las caracteristicas que no se van a eliminar*/
				$caractToModCARACT ='';
				$caractToModDET ='';
				$caractIDS = '';
				foreach ($producto['caracteristicas_modificar'] as $indiceCM => $objCaracteristica) {
					if ($objCaracteristica->accion == 'ACTUALIZAR') {

						$caractToModCARACT .="WHEN $objCaracteristica->id_caracteristica_producto THEN '$objCaracteristica->caracteristica' "."\n";
						$caractToModDET .="WHEN $objCaracteristica->id_caracteristica_producto THEN '$objCaracteristica->detalle' "."\n"; 
						$caractIDS .=$objCaracteristica->id_caracteristica_producto.',';

					}elseif ($objCaracteristica->accion == 'ELIMINAR') {
						$caracteristicasEliminar[] = $objCaracteristica->id_caracteristica_producto;
					}
				}

				$caractIDS = substr($caractIDS, 0, -1);

				//Se verifica si hay alguna caracteristica a ser actualizada
				if (!empty($caractToModCARACT)) {
					$sql[] = "UPDATE caracteristicas_de_producto
								   SET caracteristica = CASE id_caracteristica_producto
									                    $caractToModCARACT 
									                    ELSE caracteristica
									                    END,
								   	   detalle = CASE id_caracteristica_producto 
														$caractToModDET
									                    ELSE detalle
									                    END
								 WHERE id_caracteristica_producto IN($caractIDS);";
				}

			/* FIN MODIFICAR CARACTERISTICAS */

			/* ELIMINAR CARACTERISTICAS */
				/*Si existen caracteristicas para eliminar, se genera el Mysql para proceder a eliminarlos*/
				if (count($caracteristicasEliminar)>0) {
					$IdTodel = '';
					for ($delI=0; $delI < count($caracteristicasEliminar); $delI++) { 

						if ($delI == (count($caracteristicasEliminar)-1)) {
							$IdTodel .=$caracteristicasEliminar[$delI];
						}else{
							$IdTodel .=$caracteristicasEliminar[$delI].',';
						}
					}
					$sql[] = "DELETE FROM caracteristicas_de_producto WHERE id_caracteristica_producto in ($IdTodel)";
				}
			/* FIN ELIMINAR CARACTERISTICAS */

			/* AGREGAR CARACTERISTICAS */
				if (isset($producto['caracteristicas_nuevas'])) {
					if (count($producto['caracteristicas_nuevas'])>0) {
						$sqlCaracteristicas = "INSERT INTO caracteristicas_de_producto (codigo,
																						caracteristica,
																						detalle)
											VALUES ";

						foreach ($producto["caracteristicas_nuevas"] as $iCaract => $valor) {
							$sqlCaracteristicas .= "(".$producto['codigo'].",
												'".$producto['caracteristicas_nuevas'][$iCaract]['caracteristica']."',
												'".$producto['caracteristicas_nuevas'][$iCaract]['detalle'] ."'
												)";
						    if ($iCaract === array_key_last($producto['caracteristicas_nuevas'])) {
						       $sqlCaracteristicas .= ''; //'LAST ELEMENT!'
						    }else{
						    	$sqlCaracteristicas .=',';
						    }
						}

						$sql[] = $sqlCaracteristicas;
					}
				}
			/* FIN ------------- SQL de CARACTERISTICAS*/

			/* MODIFICAR PROVEEDORES */
				/*Se arma el SQL responsable de actualizar los datos de los proveedores que no se van a eliminar*/
				$ToMod_codigo_proveedor='';
				$ToMod_id_proveedor='';
				$ToMod_descripcion='';
				$ToMod_precio_p_proveedor='';
				$ToMod_precio_c_proveedor='';
				$ToMod_precio_p_d_proveedor='';
				$ToMod_precio_c_d_proveedor='';
				$ToMod_link_proveedor='';
				$proveeIDS = '';

				foreach ($producto['proveedores'] as $indicePM => $value) {
					if ($producto['proveedores'][$indicePM]['proveedor_accion'] == 'actualizar') {
						$ToMod_codigo_proveedor		.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['codigo_proveedor']."\n";
						$ToMod_id_proveedor		 	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['proveedor_id']."\n";
						$ToMod_descripcion		 	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN '".$producto['proveedores'][$indicePM]['nombreProducto_Proveedor']."'"."\n";
						$ToMod_precio_p_proveedor	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['precio_p_proveedor']."\n";
						$ToMod_precio_c_proveedor	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['precio_c_proveedor']."\n";
						$ToMod_precio_p_d_proveedor	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['precio_p_d_proveedor']."\n";
						$ToMod_precio_c_d_proveedor	.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN ".$producto['proveedores'][$indicePM]['precio_c_d_proveedor']."\n";
						$ToMod_link_proveedor		.="WHEN ".$producto['proveedores'][$indicePM]['id_proveedor_producto'] ." THEN '".$producto['proveedores'][$indicePM]['link_proveedor']."'"."\n";
						$proveeIDS .=$producto['proveedores'][$indicePM]['id_proveedor_producto'].',';
					}
				}

				if (!empty($proveeIDS)) {
					$proveeIDS = substr($proveeIDS, 0, -1);
				}

				$sql[] = "UPDATE productos_proveedores
							   SET  codigo_proveedor = CASE id_producto_proveedores
								                    $ToMod_codigo_proveedor 
								                    ELSE codigo_proveedor
								                    END,
								    id_proveedor = CASE id_producto_proveedores
								                    $ToMod_id_proveedor 
								                    ELSE id_proveedor
								                    END,
								    descripcion = CASE id_producto_proveedores
								                    $ToMod_descripcion 
								                    ELSE descripcion
								                    END,
								    precio_p_proveedor = CASE id_producto_proveedores
								                    $ToMod_precio_p_proveedor 
								                    ELSE precio_p_proveedor
								                    END,
								    precio_c_proveedor = CASE id_producto_proveedores
								                    $ToMod_precio_c_proveedor 
								                    ELSE precio_c_proveedor
								                    END,
								    precio_p_d_proveedor = CASE id_producto_proveedores
								                    $ToMod_precio_p_d_proveedor 
								                    ELSE precio_p_d_proveedor
								                    END,
								    precio_c_d_proveedor = CASE id_producto_proveedores
								                    $ToMod_precio_c_d_proveedor 
								                    ELSE precio_c_d_proveedor
								                    END,
								    link_proveedor = CASE id_producto_proveedores
								                    $ToMod_link_proveedor 
								                    ELSE link_proveedor
								                    END
							 WHERE id_producto_proveedores IN($proveeIDS);";
	
				/* FIN ------------ actualizar proveedores */

				/* Se arma el SQL responsable de agregar los nuevos proveedores*/
					$agregarProveedor = false;
					$sqlProveedores = "INSERT INTO productos_proveedores (	codigo,
																			codigo_proveedor,
																			id_proveedor,
																			descripcion,
																			precio_p_proveedor,
																			precio_c_proveedor,
																			precio_p_d_proveedor,
																			precio_c_d_proveedor,
																			link_proveedor,
																			fecha_alta)
										VALUES ";
					foreach ($producto['proveedores'] as $indiceProveedor => $idProveedor) {
						if ($producto['proveedores'][$indiceProveedor]['proveedor_accion'] == 'agregar') {
							$sqlProveedores .= "(".$producto['codigo'].",
												'".$producto['proveedores'][$indiceProveedor]['codigo_proveedor']."',
												".$producto['proveedores'][$indiceProveedor]['proveedor_id'].",
												'".$producto['proveedores'][$indiceProveedor]['nombreProducto_Proveedor']."',
												".$producto['proveedores'][$indiceProveedor]['precio_p_proveedor'].",
												".$producto['proveedores'][$indiceProveedor]['precio_c_proveedor'].",
												".$producto['proveedores'][$indiceProveedor]['precio_p_d_proveedor'].",
												".$producto['proveedores'][$indiceProveedor]['precio_c_d_proveedor'].",
												'".$producto['proveedores'][$indiceProveedor]['link_proveedor']."',
												'".self::fechaAhora()."'
												),";
							$agregarProveedor = true;
					    }
					}

					/*Se comprueba si hay proveedores para agregar y se agrega el SQL para la sentencia final*/
					if ($agregarProveedor == true) {
						$sqlProveedores = substr($sqlProveedores, 0, -1);
						$sql[] = $sqlProveedores;
					}
			    /* FIN ------------- SQL de agregar PROVEEDORES*/

				/* Se arma el SQL responsable de ELIMINAR proveedores*/
					$idProve_to_del= '';

					foreach ($producto['proveedores_del'] as $idDel_prove => $value) {
						if ($producto['proveedores_del'][$idDel_prove]['estado'] == 'eliminar') {
							$idProve_to_del = $producto['proveedores_del'][$idDel_prove]['id_proveedor_producto'].',';
						}
					}
					
					if (!empty($idProve_to_del)) {
						$idProve_to_del = substr($idProve_to_del, 0, -1);
						$sqlDel_proveedores = "DELETE FROM productos_proveedores WHERE id_producto_proveedores in($idProve_to_del)";
						// var_dump($sqlDel_proveedores);
						$sql[] = $sqlDel_proveedores;
					}
				/* FIN ------------- SQL de ELIMINAR PROVEEDORES*/
			/* FIN MODIFICAR PROVEEDORES */

			/* MOFIFICAR IMAGENES DE PRODUCTO */
				/*Parte encargada de eliminar una imagen*/
					if (isset($producto['imgDEL'])) {
						if (count($producto['imgDEL'])>0) {
							$sqlEliminarImagenes = '(';
							foreach ($producto['imgDEL'] as $indiceDelImg => $idDelImg) {
								$sqlEliminarImagenes .= $idDelImg.',';
							}

							$sqlEliminarImagenes = substr($sqlEliminarImagenes, 0, -1);
							$sqlEliminarImagenes .= ')';

							$sql[] = "DELETE FROM imagenes WHERE id_imagen in $sqlEliminarImagenes";
						}
					}
				/*FIN ------- Parte encargada de eliminar una imagen*/

				/* Parte encargada de modificar la imagen principal */
					if (isset($producto['imgPrin'])) {
						if (!empty($producto['imgPrin'])) {
							$sql[] = "SET @id_img_principal_actual = (SELECT id_imagen FROM imagenes WHERE img_position = 0 AND codigo = '".$producto['codigo']."')";
							$sql[] = "SET @img_position_imgToPrin =(SELECT img_position FROM imagenes WHERE id_imagen = ".$producto['imgPrin'].")";
							$sql[] = "UPDATE imagenes SET img_position = @img_position_imgToPrin WHERE id_imagen = @id_img_principal_actual";
							$sql[] = "UPDATE imagenes SET img_position = 0 WHERE id_imagen = ".$producto['imgPrin']."";
						}
					}
				/* FIN ------- Parte encargada de modificar la imagen principal */

				/* Parte encargada de AGREGAR imagenes*/
					/*Agregar nueva imagen*/
					if ($producto['cargarImagenes'] == true) {
						/* Control de imagenes existente en servidor */
							//Obtenemos la información de las imagenes relacionadas al producto
							$GETimagenesDeProducto = ImagenModel::getImagenesProducto($producto['codigo']);

							//Establecemos una bandera en caso de que el prefijo de posicion de img se tenga que continuar
							$continuar_posicion = false;

							if ($GETimagenesDeProducto['status'] == true) {
								$imagenesDeProducto = $GETimagenesDeProducto['resultado'];
								
								if (count($imagenesDeProducto)>0) {
									$continuar_posicion = true;
									//Obtenemos los n° de los datos obtenidos de la DB, de los nombres de las imagenes, para evitar renombrar una imagen existente 
									for ($i=0; $i < count($imagenesDeProducto) ; $i++) { 
										$numeroImagenes[] = intval(str_replace($producto['codigo'].'_', '', $imagenesDeProducto[$i]->img_nombre));
									}

									//Se calcula el prefijo numérico de mayor valor, para usar un prefijo mayor en el nombre de en la nueva imagen a cargar
									$numeroMayordeImagenes = 0;
									for ($i=0; $i < count($numeroImagenes); $i++) { 
										if ($numeroImagenes[$i] >= $numeroMayordeImagenes) {
											$numeroMayordeImagenes = $numeroImagenes[$i];
										}
									}

									//Aumentamos en 1, el prefijo numérico de mayor valor de las imagenes registradas
									$numeroMayordeImagenes++;
								}
							}

						/* FIN --- Control de imagenes existente en servidor */
						for ($i=0; $i < $producto['cantidad_imagenes_producto']; $i++) { 
							$sql[] = "INSERT INTO imagenes (codigo,img_position,img_nombre) 
									   VALUES (".$producto['codigo'].",$numeroMayordeImagenes,'".$producto['codigo']."_$numeroMayordeImagenes')";
							$numeroMayordeImagenes++;
						}
					}
				/* FIN ----- SQL de Parte encargada de AGREGAR imagenes*/

			/* FIN ------------- MOFIFICAR IMAGENES DE PRODUCTO */

			/* MOFIFICAR CONDICIONALES PRODUCTO */
			var_dump($producto['envio_gratis']);
				$sql[] = "UPDATE condicionales_producto
								SET visibilidad = ".($producto['item_visible'] ? '1' : '0').",

									estado_especial = '".$producto['etiqueta_estado_especial']."',
									estado_especial_tipo = '".$producto['estadoEspecial']."',
									estado_especial_des = '".$producto['estado_especial_descripcion']."',

									aplicar_descuento	=	'".$producto['aplicar_descuento_condicional']."',
									tipo_descuento_aplicado	=	'".$producto['condicional_tipo_descuento']."',
									descuento_porcentual_porcentaje	=	".$producto['precio_descuento_porcentaje'].",
									descuento_por_precio_importe	=	".$producto['precio_descuento'].",
									descuento_porcentual_por_cantidad_porcentaje	= 	".$producto['descuento_porcentaje_x_cantidad'].",
									descuento_porcentual_por_cantidad_cant_minima	=	".$producto['cantidad_minima_descuento'].",

									envio_gratis =	'".$producto['envio_gratis']."',
									envio_gratis_por_cant_min =	'".$producto['cantidad_minima_envio_gratis_estado']."',
									envio_gratis_cantidad_min = ".$producto['cantidad_minima_envio_gratis']."

								WHERE codigo = '".$producto['codigo']."'";
			/* FIN ------------- CONDICIONALES PRODUCTO */

			// var_dump($sql);
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

	public static function getProductoSimple($codigo){
		/*Obtener dato de producto que no esta en un grupo*/
		$model = new static();

		$sql = "SELECT producto.*, 
					   fabricante.fabricante, 
					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_sucursal\": \"',id_sucursal,'\",\"stock\": \"',stock,'\",\"cantidad_vendidos\": \"',cantidad_vendidos,'\",\"alerta_stock\": \"',alerta_stock,'\",\"ubicacion\": \"',	ubicacion,'\",\"id_stock_sucursal\": \"',id_stock_sucursal,'\"}') SEPARATOR '&') 
					   		FROM stock_sucursal WHERE stock_sucursal.codigo = producto.codigo) as stock,

					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_precios_producto \": \"',id_precios_producto,'\",\"id_sucursal\": \"',id_sucursal,'\",\"id_grupo\": \"',id_grupo,'\",\"ganancia\": \"',ganancia,'\",\"moneda_ref_producto\": \"',moneda_ref_producto,'\",\"precio_publico_pesos\": \"',precio_publico_pesos,'\",\"precio_costo_pesos\": \"',precio_costo_pesos,'\",\"precio_publico_dolar\": \"',precio_publico_dolar,'\",\"precio_costo_dolar\": \"',precio_costo_dolar,'\",\"grupo_activo\": \"',grupo_activo,'\"}') SEPARATOR '&')
					   		FROM precios_producto WHERE precios_producto.codigo = producto.codigo) as precios,

					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_condicionales \": \"',id_condicionales,'\",\"visibilidad\": \"',visibilidad,'\",\"estado_especial\": \"',estado_especial,'\",\"estado_especial_tipo\": \"',estado_especial_tipo,'\",\"estado_especial_des\": \"',estado_especial_des,'\",\"aplicar_descuento\": \"',aplicar_descuento,'\",\"tipo_descuento_aplicado\": \"',tipo_descuento_aplicado,'\",\"descuento_porcentual_porcentaje\": \"',descuento_porcentual_porcentaje,'\",\"descuento_por_precio_importe\": \"',descuento_por_precio_importe,'\",\"descuento_porcentual_por_cantidad_porcentaje\": \"',descuento_porcentual_por_cantidad_porcentaje,'\",\"descuento_porcentual_por_cantidad_cant_minima\": \"',descuento_porcentual_por_cantidad_cant_minima,'\",\"envio_gratis\": \"',envio_gratis,'\",\"envio_gratis_por_cant_min\": \"',envio_gratis_por_cant_min,'\",\"envio_gratis_cantidad_min\": \"',envio_gratis_cantidad_min,'\",\"id_sucursal\": \"',id_sucursal,'\"}') SEPARATOR '&')
					   		FROM condicionales_producto WHERE condicionales_producto.codigo = producto.codigo) as condicionales,

					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_producto_proveedores\": \"',id_producto_proveedores ,'\",\"codigo_proveedor\": \"',codigo_proveedor,'\",\"id_proveedor\": \"',productos_proveedores.id_proveedor,'\",\"descripcion\": \"',descripcion,'\",\"precio_p_proveedor\": \"',precio_p_proveedor,'\",\"precio_c_proveedor\": \"',precio_c_proveedor,'\",\"precio_p_d_proveedor\": \"',precio_p_d_proveedor,'\",\"precio_c_d_proveedor\": \"',precio_c_d_proveedor,'\",\"link_proveedor\": \"',link_proveedor,'\",\"fecha_alta\": \"',fecha_alta,'\",\"proveedor\": \"',proveedor,'\",\"infoFn\": \"',infoFn,'\"}') SEPARATOR '&')
					   		FROM productos_proveedores
					   		INNER JOIN proveedores ON proveedores.id_proveedor = productos_proveedores.id_proveedor 
					   		WHERE productos_proveedores.codigo = producto.codigo ) as proveedores,

					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_imagen\": \"',id_imagen,'\",\"img_position\": \"',img_position,'\",\"img_nombre\": \"',img_nombre,'\"}') SEPARATOR '&')
					   		FROM imagenes
					   		WHERE imagenes.codigo = producto.codigo ) as imagenes,

					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"id_caracteristica_producto\": \"',id_caracteristica_producto,'\",\"caracteristica\": \"',caracteristica,'\",\"detalle\": \"',detalle,'\"}') SEPARATOR '&')
					   		FROM caracteristicas_de_producto
					   		WHERE caracteristicas_de_producto.codigo = producto.codigo ) as caracteristicas,

					   	categorias_producto.id_categoria,
					   	categorias_producto.id_subcategoria,
					   	categorias.categoria,
					   	subcategorias.subcategoria

				FROM productos producto
					INNER join fabricantes fabricante ON fabricante.id_fabricante = producto.id_fabricante
					INNER join stock_sucursal stock ON stock.codigo = producto.codigo
					INNER join categorias_producto ON categorias_producto.codigo = producto.codigo
					INNER join categorias ON categorias_producto.id_categoria = categorias.id_categoria
					INNER join subcategorias ON categorias_producto.id_subcategoria  = subcategorias.id_subcategoria


				WHERE producto.codigo = $codigo";

		// var_dump($sql);
		$result = DataBase::getRecord($sql);

		return $result;
	}

	public static function getProductos($n_of_records_per_page = 12, $offset = 0, $buscar = null, $panelDeControl = null, $filtros = null){

		/**
		 * Class getProductos
		 * @param  [<Type>] [name] [<description>]
		 * @return [Type] [<description>]
		 *
		 * @param  int 		$n_of_records_per_page 	numero de registros devueltos por pagina
		 * @param  int 		$offset  				posicion del puntero en los registros devueltos
		 * @param  string 	$buscar  				string de busqueda de producto/s
		 * @param  bool 	$panelDeControl 		bandera de origen de consulta
		 * @param  string 	$filtros 				filtros de busqueda
		 * @return OBJ 		$result 				array con objetos de productos devueltos
		 */
		
		$model = new static();

		$sql = "SELECT producto.*, 
						fabricante.fabricante,
					   	categorias_producto.id_categoria, 
					   	categorias_producto.id_subcategoria ,
					   	categorias.categoria,
					   	subcategorias.subcategoria,
					   	sucursales.sucursal,
					   	precios_producto.*,
					   	stock_sucursal.*,
					   	condicionales_producto.*,
					   	grupos_de_productos.nombre_grupo as g_nombre_grupo,
					   	grupos_de_productos.precio_publico_pesos as g_precio_publico_pesos,
					   	grupos_de_productos.precio_costo_pesos as g_precio_costo_pesos,
					   	grupos_de_productos.precio_publico_dolar as g_precio_publico_dolar,
					   	grupos_de_productos.precio_costo_dolar as g_precio_costo_dolar,
					   	grupos_de_productos.moneda_ref as g_moneda_ref,
					   	grupos_de_productos.sub_grupo as g_sub_grupo,
					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"img_position\": \"',img_position,'\", \"img_nombre\": \"',img_nombre,'\"}') SEPARATOR ',') FROM imagenes WHERE imagenes.codigo = producto.codigo) as imagenes
				FROM $model->table producto
				INNER JOIN fabricantes fabricante ON fabricante.id_fabricante = producto.id_fabricante
				INNER JOIN categorias_producto ON categorias_producto.codigo = producto.codigo
				INNER JOIN categorias ON categorias.id_categoria = categorias_producto.id_categoria
				INNER JOIN subcategorias ON subcategorias.id_subcategoria = categorias_producto.id_subcategoria
				INNER JOIN precios_producto ON precios_producto.codigo = producto.codigo
				INNER JOIN stock_sucursal ON stock_sucursal.codigo = producto.codigo
				INNER JOIN sucursales ON precios_producto.id_sucursal = sucursales.id_sucursal
				INNER JOIN condicionales_producto ON condicionales_producto.codigo = producto.codigo
				INNER JOIN grupos_de_productos ON grupos_de_productos.id_grupo = precios_producto.id_grupo
				INNER JOIN imagenes ON imagenes.codigo = producto.codigo 
				#buscar#
				GROUP by producto.codigo
				";


		/* Buscar SETEO */
		if (isset($buscar)) {
			if (!empty($buscar)) {
				if (strlen($buscar) == 4 && $buscar > 0 && $buscar < 9999) {
					$buscar = $buscar;
				}

				$sqlBUSCAR = "AND MATCH(producto.descripcion, producto.keywords) AGAINST ('$buscar')";

				$sql = str_replace('#buscar#', $sqlBUSCAR, $sql);
			}else{
				$sql = str_replace('#buscar#', '', $sql);
			}
		}else{
			$sql = str_replace('#buscar#', '', $sql);
		}

			/*-
				filtros de busqueda
				-por caja/ubicacion
				-por stock
				-por categoria
				-por subcategoria
				-por color?
				-por ofertas/descuentos
				-ocultos


				
			-*/
		/* Fin --- Buscar SETEO */



		// if (isset($panelDeControl)) {
		// 	$mostrarItemsOcultos = '';
		// }else{
		// 	$mostrarItemsOcultos = "INNER JOIN condicionales_producto ON condicionales_producto.visibilidad = 1 AND condicionales_producto.codigo = productos.codigo";
		// }


		// $sqlFiltros = '';
		// $sqlFiltroJOIN = '';
		// $productosEnOFerta = '';
		
		/** RE-ESCRIBIR TODA ESTA PARTE **/
		// if (isset($filtros)) {
			
		// 	if (strtoupper($filtros) == 'OFERTAS' ) {
		// 		$sqlFiltroJOIN = '';
		// 		if (!empty($mostrarItemsOcultos)) {
		// 			$productosEnOFerta = 'and productos.en_oferta = "si"';
		// 		}else{
		// 			$productosEnOFerta = 'WHERE productos.en_oferta = "si"';
		// 		}
		// 	}else{
		// 		$filtros = explode('_', $filtros);
		// 		if (count($filtros)>1) {
		// 			$filtroCategoria = $filtros[1]; //Id Categoria principal
		// 			$sqlFiltroJOIN .= 'INNER JOIN categoria_producto ON categoria_producto.codigo = productos.codigo AND categoria_producto.id_categoria = '.$filtroCategoria.' AND productos.stock > 0';
		// 			if (isset($filtros[2])) {
		// 				$filtroSubcategoria = $filtros[2];
		// 				$sqlFiltroJOIN .=' AND categoria_producto.id_subcategoria = '.$filtroSubcategoria.' AND productos.stock > 0';
		// 			}
		// 		}else{
		// 			$sqlFiltroJOIN .= 'INNER JOIN categoria_producto ON categoria_producto.codigo = productos.codigo AND categoria_producto.id_categoria = 8 AND productos.stock > 0';
		// 		}
		// 	}

		// }
		/** FIN --- RE-ESCRIBIR TODA ESTA PARTE **/

		// $sql = "SELECT productos.*, grupos_de_productos.*, img.img_nombre as imagen_principal from ".$model->table." 
		// 		INNER JOIN imagenes img ON img.codigo = productos.codigo AND img.img_posicion = 0
		// 		INNER JOIN grupos_de_productos ON grupos_de_productos.id_grupo = productos.id_grupo
		// 		".$sqlFiltroJOIN."
		// 	 ".$mostrarItemsOcultos."
		// 	 ".$productosEnOFerta."
		// 	 ORDER BY CASE 
		// 	 WHEN stock > 0 THEN 0 ELSE 1 END, prioridad_en_busqueda desc, descripcion LIMIT $offset, $n_of_records_per_page";

		// $sqlSinLimit = "SELECT productos.*, grupos_de_productos.*, img.img_nombre as imagen_principal from ".$model->table." 
		// 		INNER JOIN imagenes img ON img.codigo = productos.codigo AND img.img_posicion = 0
		// 		INNER JOIN grupos_de_productos ON grupos_de_productos.id_grupo = productos.id_grupo
		// 		".$sqlFiltroJOIN."
		// 	 ".$mostrarItemsOcultos."
		// 	  ".$productosEnOFerta."
		// 	 ORDER BY CASE 
		// 	 WHEN stock > 0 THEN 0 ELSE 1 END, prioridad_en_busqueda desc, descripcion";
		// 	 // ORDER BY  prioridad_en_busqueda desc, descripcion LIMIT $offset, $n_of_records_per_page";
		// // echo "$sql";

		if (isset($filtros)) {
			self::$productosRow = DataBase::rowCount($sqlSinLimit);
		}else{
			self::$productosRow = self::totalProductos();
		}

		// if (isset($buscar)) {
		// 	if (!empty($buscar)) {
		// 		if (strlen($buscar) == 4 && $buscar > 0 && $buscar < 9999) {
		// 			$buscar = $buscar;
		// 		}

		// 		if (isset($_SESSION['USER'])) {
		// 			if (strlen($buscar) == 2 && is_numeric($buscar) ){
		// 				if ($_SESSION['USER']['type'] ==  'administrador') {

		// 					if (!empty($mostrarItemsOcultos) || !empty($productosEnOFerta) ){
		// 						$buscarenbox = "and productos.box = $buscar";
		// 					}else{
		// 						$buscarenbox = "WHERE productos.box = $buscar";
		// 					}

		// 				}else{
		// 					$buscarenbox = '';
		// 				}
		// 			}else{
		// 				$buscarenbox = '';
		// 			}
		// 		}else{
		// 			$buscarenbox = '';
		// 		}

		// 		$simoloPlus = strpos($buscar, '+');

		// 		if ($simoloPlus === false) {
		// 			$modobooleano = 'IN BOOLEAN MODE';
		// 		}else{
		// 			$modobooleano = '';
		// 		}

		// 		$buscar = addslashes($buscar);

		// 		$buscar= htmlspecialchars($buscar, ENT_QUOTES);
		// 		// var_dump($buscar);
		// 		  if (strlen($buscarenbox)>0) {
		// 			  $sql="SELECT
		// 					    productos.*,
		// 					    grupos_de_productos.*,
		// 					    img.img_nombre AS imagen_principal,
		// 					    categorias.categoria AS categoria,
		// 					    subcategorias.subcategoria AS subcategoria
		// 					FROM
		// 						    productos
		// 						INNER JOIN imagenes img ON
		// 						    img.codigo = productos.codigo AND img.img_posicion = 0
		// 						INNER JOIN categoria_producto ON categoria_producto.codigo = productos.codigo
		// 						INNER JOIN categorias ON categorias.id_categoria = categoria_producto.id_categoria
		// 						INNER JOIN subcategorias ON subcategorias.id_subcategoria = categoria_producto.id_subcategoria
		// 						INNER JOIN grupos_de_productos ON productos.id_grupo = grupos_de_productos.id_grupo
		// 						$mostrarItemsOcultos

		// 						$buscarenbox
								
		// 						ORDER BY 
		// 						 	 productos.box DESC,
		// 						     productos.stock DESC,
		// 						     productos.descripcion 
		// 					";
		// 		  }else{			  	
		// 			  $sql="SELECT
		// 					    productos.*,
		// 					    grupos_de_productos.*,
		// 					    img.img_nombre AS imagen_principal,
		// 					    categorias.categoria AS categoria,
		// 					    subcategorias.subcategoria AS subcategoria,
		// 					    MATCH (categorias.categoria)  AGAINST('$buscar')   relevancia2,
		// 					    MATCH (subcategorias.subcategoria)  AGAINST('$buscar')   relevancia3,
		// 					    IF(stock <= 0, 0,     MATCH(
		// 					        productos.descripcion,
		// 					        productos.codigo,
		// 					        productos.keywords
		// 					    ) AGAINST('+$buscar' $modobooleano)) as relevanciaFinal
		// 					FROM
		// 						    productos
		// 						INNER JOIN imagenes img ON
		// 						    img.codigo = productos.codigo AND img.img_posicion = 0
		// 						INNER JOIN categoria_producto ON categoria_producto.codigo = productos.codigo
		// 						INNER JOIN categorias ON categorias.id_categoria = categoria_producto.id_categoria
		// 						INNER JOIN subcategorias ON subcategorias.id_subcategoria = categoria_producto.id_subcategoria
		// 						INNER JOIN grupos_de_productos ON productos.id_grupo = grupos_de_productos.id_grupo
		// 						$mostrarItemsOcultos

		// 						AND MATCH(productos.codigo, productos.descripcion, productos.keywords) AGAINST ('$buscar') 
		// 						ORDER BY 
		// 						     relevanciaFinal DESC, 
		// 						     relevancia3 DESC, 
		// 						     relevancia2 DESC, 
		// 						     stock DESC,
		// 						     prioridad_en_busqueda DESC, 
		// 						     descripcion 
		// 					";
		// 		  }

		// 			$params = ["buscar" => $buscar];

		// 			self::$productosRow = DataBase::rowCount($sql);

		// 			$sql .="LIMIT $offset, $n_of_records_per_page"; 
		// 			// var_dump($sql);
		// 	}
		// }

		/*Grupo set*/
			// $sql2 = "SELECT dolar FROM dolar ORDER BY id_dolar DESC LIMIT 1";

			// $dolarResult = DataBase::getRecords($sql2);
			// $dolar = $dolarResult[0]->dolar;

			// $result = DataBase::getRecords($sql);

			// for ($i=0; $i < count($result); $i++) { 

			// 	if ($result[$i]->id_grupo != 0) {

			// 		if ($result[$i]->moneda_ref == 1) {
			// 			$result[$i]->precio = $result[$i]->precio_publico_pesos;
			// 			$result[$i]->precio_costo = $result[$i]->precio_costo_pesos;

			// 			$result[$i]->precio_neto = $result[$i]->precio;
			// 		}elseif ($result[$i]->moneda_ref == 2) {
			// 			$result[$i]->precio = $result[$i]->precio_publico_dolar*$dolar;
			// 			$result[$i]->precio_costo = $result[$i]->precio_costo_dolar*$dolar;

			// 			$result[$i]->precio_neto = $result[$i]->precio_publico_dolar*$dolar;
			// 		}
			// 	}else{
			// 		/*Seteo de Precio - El precio Neto */
			// 		if ($result[$i]->moneda_ref_producto == '2') {
			// 			$result[$i]->precio_neto = $result[$i]->dolar_venta*$dolar;
			// 		}else{
			// 			$result[$i]->precio_neto = $result[$i]->precio;
			// 		}
			// 		/* FIN Seteo de Precio - El precio Neto */
			// 	}
			// 	unset($result[$i]->precio_publico_pesos);
			// 	unset($result[$i]->precio_costo_pesos);
			// 	unset($result[$i]->precio_publico_dolar);
			// 	unset($result[$i]->precio_costo_dolar);
			// 	unset($result[$i]->moneda_ref);
			// 	unset($result[$i]->nombre_grupo);
			// 	unset($result[$i]->id_grupo);
			// }
		/* - ----------Grupo set*/


		// var_dump($sql);
		$result = DataBase::getRecords($sql);

		// echo '<pre>';
		// var_dump($result['resultado']);
		// echo '</pre>';
		return $result;
	}

	public static function getProductosLight($n_of_records_per_page = 12, $offset = 0, $buscar = null, $filtros = null){

		/**
		 * Class getProductos
		 * @param  [<Type>] [name] [<description>]
		 * @return [Type] [<description>]
		 *
		 * @param  int 		$n_of_records_per_page 	numero de registros devueltos por pagina
		 * @param  int 		$offset  				posicion del puntero en los registros devueltos
		 * @param  string 	$buscar  				string de busqueda de producto/s
		 * @param  bool 	$panelDeControl 		bandera de origen de consulta
		 * @param  string 	$filtros 				filtros de busqueda
		 * @return OBJ 		$result 				array con objetos de productos devueltos
		 */
		
		$model = new static();

		$sql = "SELECT 	producto.codigo,
						producto.descripcion,
						producto.iva,
						producto.relevancia_lista,
						producto.id_fabricante,
						producto.fisico_largo_cm,
						producto.fisico_ancho_cm,
						producto.fisico_alto_cm,
						producto.fisico_peso_gm,
						fabricante.fabricante,
					   	categorias_producto.id_categoria, 
					   	categorias_producto.id_subcategoria ,
					   	categorias.categoria,
					   	subcategorias.subcategoria,
					   	sucursales.sucursal,
					   	precios_producto.*,
					   	stock_sucursal.*,
					   	condicionales_producto.*,
					   	grupos_de_productos.nombre_grupo as g_nombre_grupo,
					   	grupos_de_productos.precio_publico_pesos as g_precio_publico_pesos,
					   	grupos_de_productos.precio_costo_pesos as g_precio_costo_pesos,
					   	grupos_de_productos.precio_publico_dolar as g_precio_publico_dolar,
					   	grupos_de_productos.precio_costo_dolar as g_precio_costo_dolar,
					   	grupos_de_productos.moneda_ref as g_moneda_ref,
					   	grupos_de_productos.sub_grupo as g_sub_grupo,
					   	(SELECT GROUP_CONCAT( DISTINCT CONCAT('{\"img_position\": \"',img_position,'\", \"img_nombre\": \"',img_nombre,'\"}') SEPARATOR ',') FROM imagenes WHERE imagenes.codigo = producto.codigo) as imagenes,
					    IF(precios_producto.grupo_activo = 'si'
					       ,IF(
					           	grupos_de_productos.moneda_ref = 2
					       		,ROUND(grupos_de_productos.precio_costo_dolar * (SELECT dolar FROM dolar WHERE id_dolar =(SELECT MAX(id_dolar) FROM dolar)), 2)
					       		,ROUND(grupos_de_productos.precio_publico_pesos, 2)
					       	  )
					       ,IF(
					           precios_producto.moneda_ref_producto = 2
					           		,ROUND(precios_producto.precio_publico_dolar * (SELECT dolar FROM dolar WHERE id_dolar =(SELECT MAX(id_dolar) FROM dolar)), 2)
					       			,ROUND(precios_producto.precio_publico_pesos, 2)
					       	   )
					      ) 
					       as precio_publico_final_sin_condicionantes
				FROM $model->table producto
				INNER JOIN fabricantes fabricante ON fabricante.id_fabricante = producto.id_fabricante
				INNER JOIN categorias_producto ON categorias_producto.codigo = producto.codigo
				INNER JOIN categorias ON categorias.id_categoria = categorias_producto.id_categoria
				INNER JOIN subcategorias ON subcategorias.id_subcategoria = categorias_producto.id_subcategoria
				INNER JOIN precios_producto ON precios_producto.codigo = producto.codigo
				INNER JOIN stock_sucursal ON stock_sucursal.codigo = producto.codigo
				INNER JOIN sucursales ON precios_producto.id_sucursal = sucursales.id_sucursal
				INNER JOIN condicionales_producto ON condicionales_producto.codigo = producto.codigo
				INNER JOIN grupos_de_productos ON grupos_de_productos.id_grupo = precios_producto.id_grupo
				INNER JOIN imagenes ON imagenes.codigo = producto.codigo 
				#buscar#
				WHERE producto.type = 'item' #and#
				GROUP by producto.codigo
				";



		/* Buscar SETEO */
		if (isset($buscar)) {
			if (!empty($buscar)) {
				if (strlen($buscar) == 4 && $buscar > 0 && $buscar < 9999) {
					$buscar = $buscar;
				}

				$sqlBUSCAR = "AND MATCH(producto.descripcion, producto.keywords) AGAINST ('$buscar') ";

				$sql = str_replace('#buscar#', $sqlBUSCAR, $sql);
			}else{
				$sql = str_replace('#buscar#', '', $sql);
			}
		}else{
			$sql = str_replace('#buscar#', '', $sql);
		}

		/*FILTROS -------------------------------------*/
		$componentes_order = array();
		$componentes_and = array();



// stock
// alfabetico
// engrupo
// porCodigo


		if (isset($filtros)) {
			/* ----- Ubicación ----- */
				if (isset($filtros['box'])) {
					if (!empty($filtros['box'])) {
						if (is_numeric($filtros['box'])) {
							$componentes_and[] = ' AND stock_sucursal.ubicacion = '.$filtros['box'].'';
						}else{						
							switch ($filtros['box']) {
								case '1_9':
									$componentes_order[] ="stock_sucursal.ubicacion ASC ";
									break;
								case '9_1':
									$componentes_order[] ="stock_sucursal.ubicacion DESC ";
									break;
								case 't_box':
									break;
								default:
									break;
							}
						}
					}
				}
			/* ----- FIN Ubicación ----- */

			/* ----- Precio ----- */
				if (isset($filtros['precio'])) {
					if (!empty($filtros['precio'])) {
						switch ($filtros['precio']) {
							case 'mayor':
								$componentes_order[] ="precio_publico_final_sin_condicionantes DESC";
								break;
							case 'menor':
								$componentes_order[] ="precio_publico_final_sin_condicionantes ASC";
								break;
							case 'm_vendido':
								$componentes_order[] ="stock_sucursal.cantidad_vendidos DESC";
								break;
							case 'me_vendido':
								/*menos vendidos*/
								$componentes_order[] ="stock_sucursal.cantidad_vendidos ASC";
								break;
							case 't_precio':
								break;
							default:
								break;
						}
					}
				}
			/* ----- FIN Precio ----- */

			/* ----- categoria ----- */
				if (isset($filtros['categoria'])) {
					if (!empty($filtros['categoria'])) {
						if (is_numeric($filtros['categoria'])) {
							$componentes_and[] = ' AND categorias.id_categoria = '.$filtros['categoria'].'';
						}
					}
				}
			/* ----- FIN categoria ----- */

			/* ----- categoria ----- */
				if (isset($filtros['subcategoria'])) {
					if (!empty($filtros['subcategoria'])) {
						if (is_numeric($filtros['subcategoria'])) {
							$componentes_and[] = ' AND subcategorias.id_subcategoria = '.$filtros['subcategoria'].'';
						}
					}
				}
			/* ----- FIN categoria ----- */

			/* ----- fabricante ----- */
				if (isset($filtros['fabricante'])) {
					if (!empty($filtros['fabricante'])) {
						if (is_numeric($filtros['fabricante'])) {
							$componentes_and[] = ' AND fabricante.id_fabricante = '.$filtros['fabricante'].'';
						}
					}
				}
			/* ----- FIN fabricante ----- */
		}

		/* ORDER SENTENCIAS */
			$order_by_sql = 'ORDER BY ';

			if (count($componentes_order) > 0) {
				if (count($componentes_order) == 1) {
					$order_by_sql .= $componentes_order[0];
				}else{
					for ($i=0; $i < count($componentes_order); $i++) { 
						$order_by_sql .= $componentes_order[$i].',';
					}

					$order_by_sql = substr($order_by_sql, 0, -1);
				}

				$sql .= ' '.$order_by_sql.' ';
			}
		/* --- fin ORDER SENTENCIAS */

		/* and SENTENCIAS */
			$and_sql = '';

			if (count($componentes_and) > 0) {
				if (count($componentes_and) == 1) {
					$and_sql .= $componentes_and[0];
				}else{
					for ($i=0; $i < count($componentes_and); $i++) { 
						$and_sql .= $componentes_and[$i];
					}
				}

				$sql = str_replace('#and#', $and_sql, $sql);
			}else{
				$sql = str_replace('#and#', '', $sql);
			}
		/* --- fin and SENTENCIAS */


		/*fin - FILTROS -------------------------------------*/

		//DATO NECESARIO PARA PAGINACIÓN
		$sqlSinLimit = $sql;
		$cantidadDeFilas= DataBase::rowCount($sqlSinLimit);


		//Se agrega el offset y número de filas por vista
		$sql .="LIMIT $offset, $n_of_records_per_page"; 

		// var_dump($sql);

		$result = DataBase::getRecords($sql);

		/*Se devuele la cantidad de filas de la consulta sin LIMIT ni OFFSET*/
		$result['numero_de_filas'] = $cantidadDeFilas;

		return $result;
	}
	
	public static function getCaracteristicas($codigo)
	{
		/*Obtener caracteristicas especiales de un producto*/
		$model = new static();
		$sql ="SELECT * FROM caracteristicas_de_producto WHERE codigo = $codigo";

		$result = DataBase::getRecords($sql);

		return $result;
	}

	public static function totalProductos($filter = null){
		$model = new static();
		$sql="SELECT COUNT(*) as total FROM $model->table
				INNER JOIN condicionales_producto 
					ON condicionales_producto.visibilidad = 1 AND condicionales_producto.codigo = productos.codigo";
		$result = DataBase::query($sql);
		return intval($result[0]->total);
	}

	public static function getUbicaciones($sucursal = 1){
		$model = new static();
		$sql = "SELECT ubicacion FROM stock_sucursal WHERE id_sucursal = $sucursal GROUP BY ubicacion;";

		$result = DataBase::getRecords($sql);

		if ($result['status'] == true) {
			$result = $result['resultado'];
		}else{
			DataBase::errorBox($result['resultado']);
			$result = $result['status'];
		}
		return $result;
	}
}