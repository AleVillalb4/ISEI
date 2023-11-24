<script>
/*---script encargado de la vista previa de las imagenes en el formulario de alta de producto ---------------------------*/
$('document').ready(function () {
/*script encargado de la vista previa de archivo pdf en alta de producto*/
    $("#pdf_producto").change(function (e) {
        console.log(e.originalEvent.srcElement.files.length);
         $('#labelArchivo').html($("#pdf_producto")[0].files[0]['name']);
    });
});

var archivoDeImagenes = new Array();

$(document).ready(function() {
	$('#img_producto').change(function () {
		var fileInput = document.getElementById('img_producto');
		// console.log('cambiando');
		if (fileInput.files.length>1) {
			for (var imagenes = 0; imagenes < fileInput.files.length; imagenes++) {
				var file = fileInput.files[imagenes];
				fileCheckAdd(file);
			}
		}else{
			var file = fileInput.files[0];
			fileCheckAdd(file);
		}

		function fileCheckAdd(file) {
			var indiceAsociativoDeImagen = file['name'].trim();
			indiceAsociativoDeImagen = indiceAsociativoDeImagen.replace(/\s/g,''); 
			indiceAsociativoDeImagen = indiceAsociativoDeImagen.split('.')[0];

			var imagenesCargadas = Object.keys(archivoDeImagenes);
			var generarMiniatura = true;

			if (imagenesCargadas.length >= 0) {
				for (var i = 0; i < imagenesCargadas.length; i++) {
					if (indiceAsociativoDeImagen == imagenesCargadas[i]) {
						console.log('La imágen seleccionada ya se encuentra en la lista.');
						if (archivoDeImagenes[indiceAsociativoDeImagen].status == false) {
							// alert('ya existia, se regenera minitura');
							generarMiniatura = true;
						}else{
							notificacion('La imágen seleccionada ya se encuentra en la lista.', 'alerta', 0,99);
							generarMiniatura = false;
						}
					}
				}
			}

			if (generarMiniatura == true) {
				archivoDeImagenes[indiceAsociativoDeImagen] =file;
				archivoDeImagenes[indiceAsociativoDeImagen].status =true;
				crearMiniatura(indiceAsociativoDeImagen,archivoDeImagenes[indiceAsociativoDeImagen]);
			}
		}

		function crearMiniatura(indiceAsociativo,file){
				if (file.status !== false) {				
		            var img = document.createElement("img");
		            img.id = indiceAsociativo;
		            var reader = new FileReader();
		            reader.onloadend = function () {
		                img.src = reader.result;
		            }

		            reader.readAsDataURL(file);
		            $("#imagenes").append(img);

					$("#"+indiceAsociativo).click(function(event) {
					 	archivoDeImagenes[indiceAsociativo].status =false;
						 $("#"+indiceAsociativo).remove();
					});
				}else{
					console.log('falso');
				}
		}
	})
});
/*---FIN script encargado de la vista previa de las imagenes en el formulario de alta de producto ---------------------------*/

/*---Alta--------------------------------------------------------*/

$(document).ready(function() {
	// $('#addProducto').submit(function(event) {
	// 	event.preventDefault();
	// 	addProducto();
	// });	

	/*--Input de Fabricante*/

	$('#fabricante').click(function(event) {
		$('#fabricante').change(function(event) {
			if ($('#fabricante').val() == 'otro') {
				$('#fabricante_otro').attr('type', 'text');
				$('#fabricante_otro').focus();
				$(this).hide();
			}
		});
	});

	$('#fabricante_otro').focusout(function(event) {
		if ($('#fabricante_otro').val() == '') {
			$('#fabricante_otro').attr('type', 'hidden');
			$('#fabricante').find('.optionDefault').attr('disabled',false);
			$('#fabricante').val(0);
			$('#fabricante').find('.optionDefault').attr('disabled',true);
			$('#fabricante').show();
		}
	});

	/*--FIN --------------Input de Fabricante*/


	/*-- Select Categoria */
	$('#categoria').change(function(event) {
		// alert('cambiando categoria id: '+$('#categoria').val());
		getSubCategorias($('#categoria').val());
	});
	/*--FIN -------------- Select Categoria*/
});

/* -- UX UI js*/
$(document).ready(function() {
	filtroC_check();

	$('#filtros_char').click(function(event) {
		filtroC_toggle();
	});
});

	function filtroC_toggle(){
		$.ajax({
			url: '#path#UserInterface/c_filtroToggle',
			type: 'post',
			dataType: 'json',
		})
		.done(function(response) {
			// console.log("success");
			// console.log(response);

			// $('#subcategoria_cont').html(response);
			if(response.status == true){
				if(response.resultado == '0'){
					$('#filtros_char').removeClass('boton-filtro-activo');
					$('#dash_bar_filters').removeClass('dash_bar_filters_deploy');
				}else{
					$('#filtros_char').addClass('boton-filtro-activo');
					$('#dash_bar_filters').addClass('dash_bar_filters_deploy');
				}
				
			}else{
				notificacion(response.notification, 'error', 0.5,99);
			}

		})
		.fail(function(response) {
			console.log("error");
			// console.log(response);
		})
		.always(function() {
			// console.log("complete");
		});
	}

	function filtroC_check(){
		$.ajax({
			url: '#path#UserInterface/C_filtrocheck',
			type: 'post',
			dataType: 'json',
		})
		.done(function(response) {
			// console.log("success");
			// console.log(response);

			// $('#subcategoria_cont').html(response);
			if(response.status == true){
				if(response.resultado == '0'){
					$('#filtros_char').removeClass('boton-filtro-activo');
					$('#dash_bar_filters').removeClass('dash_bar_filters_deploy');
				}else{
					$('#filtros_char').addClass('boton-filtro-activo');
					$('#dash_bar_filters').addClass('dash_bar_filters_deploy');
				}
				
			}else{
				notificacion(response.notification, 'error', 0.5,99);
			}

		})
		.fail(function(response) {
			console.log("error");
			// console.log(response);
		})
		.always(function() {
			// console.log("complete");
		});
	}
/* -- FIN UX UI js */


/*-- Cargar Select Sub-Categoria */
	function getSubCategorias(id_categoria) {
		$.ajax({
			url: '#path#subcategoria/GetSubCategorias',
			type: 'post',
			dataType: 'html',
			data: {id_categoria: id_categoria},
		})
		.done(function(response) {
			// console.log("success");
			// console.log(response);

			$('#subcategoria_cont').html(response);

		})
		.fail(function(response) {
			console.log("error");
			// console.log(response);
		})
		.always(function() {
			// console.log("complete");
		});
	}
/*--FIN Cargar Select Sub-Categoria */

/*vista previa de valor de prioridad en lista de busqueda de producto*/
	function rangeEdit(){	
		$('#range_value').html($('#relevancia_lista').val());
	}
/*--FIN vista previa de valor de prioridad en lista de busqueda de producto*/

/*-- Moneda de referencia */
	$(document).ready(function() {
		$('#moneda_ref_producto').change(function(event) {
			if($(this).val() == 1){
				$('#moneda_arg').addClass('moneda_seleccionada');
				$('#moneda_usa').removeClass('moneda_seleccionada');
			}else{
				$('#moneda_usa').addClass('moneda_seleccionada');
				$('#moneda_arg').removeClass('moneda_seleccionada');
			}
		});
	});
/*-- FIN Moneda de referencia */

/*Calcular ganancias*/
	function calcularPrecioForm(moneda){
		if (moneda == 'pesos') {
			var precio_costo = parseFloat($('#producto_precio_costo').val());
			var porcentaje = parseFloat($('#producto_ganancia').val())/100;

			var precio = precio_costo + (precio_costo*porcentaje);
			$('#producto_precio').attr('value', '');
			$('#producto_precio').val(precio.toFixed(2));
			$('#producto_precio').css('color', 'red');

			setTimeout(function() {$('#producto_precio').css('color', 'initial');}, 500);
		}else{
			if (moneda == 'dolares') {
			var precio_costo = parseFloat($('#producto_precio_dolar_costo').val());
			var porcentaje = parseFloat($('#producto_ganancia').val())/100;

			var precio = precio_costo + (precio_costo*porcentaje);
			$('#producto_precio_dolar_publico').attr('value', '');
			$('#producto_precio_dolar_publico').val(precio.toFixed(2));
			$('#producto_precio_dolar_publico').css('color', 'red');

			pesificarCampo($('#producto_precio_dolar_publico').val(),$('#dolar_cotizacion').html(),'dash_dolar_publico');
			setTimeout(function() {$('#producto_precio_dolar_publico').css('color', 'initial');}, 500);
			}
		}
	}
/*--FIN Calcular ganancias*/

/*-- Grupo datos*/
	$(document).ready(function() {
		$('#grupo_select').change(function(event) {
				grupoValores();
		});
	});

	function grupoValores(){
		var id_grupo = $('#grupo_select option:selected').val();

		if(id_grupo == 0){
			$('.grupo_set').hide();
			$('.grupo_set_not_set').show();
			
		}else{

			$('.grupo_set').show();
			$('.grupo_set_not_set').hide();

			alert(id_grupo);
			$.ajax({
				url: '#path#grupo/GetGrupoValores',
				type: 'post',
				dataType: 'json',
				data: {id: id_grupo},
			})
			.done(function(data) {
				console.log("success");
				console.log(data);
				$('#grupo_set_moneda_ref').html(data.moneda_ref_text);
				$('#grupo_set_precio').html(data.precio_publico_pesos);
				$('#grupo_set_precio_costo').html(data.precio_costo_pesos);
				$('#grupo_set_dolar_venta').html(data.precio_publico_dolar);
				$('#grupo_set_dolar_costo').html(data.precio_costo_dolar);
			})
			.fail(function(response) {
				console.log("error");
				console.log(response);
			})
			.always(function() {
				console.log("complete");
			})
		}
	}
/*--FIN Grupo datos*/

/*-- condicionales de descuento*/
	$(document).ready(function() {
		condicional_descuento();

		$('#aplicar_descuento_condicional').change(function(event) {
			condicional_descuento();
		});

		$('#condicional_tipo_descuento').change(function(event) {
			condicional_descuento();
		});
	});

	function condicional_descuento(){
		var aplicar_descuento = $('#aplicar_descuento_condicional option:selected').val();
		var condicional_descuento_seleccionado = $('#condicional_tipo_descuento option:selected').val();

		if(aplicar_descuento == 'si'){
			$('.condicional_descuentos_set').show();
			switch(condicional_descuento_seleccionado) {
			  case 'x_porcentaje':
					$('#descuento_x_precio').hide();
					$('#descuento_x_portencaje').show();
					$('#descuento_x_cantidad').hide();
			    break;
			  case 'x_precio':
					$('#descuento_x_precio').show();
					$('#descuento_x_portencaje').hide();
					$('#descuento_x_cantidad').hide();
			    break;
			  case 'x_cantidad':
					$('#descuento_x_precio').hide();
					$('#descuento_x_portencaje').hide();
					$('#descuento_x_cantidad').show();
			    break;
			  default:
			    alert('Error tipo de descuento');
			}
		}else{
			$('.condicional_descuentos_set').hide();
		}

	}

/*--FIN condicionales de descuento*/

/*-- condicionales de envio*/
	$(document).ready(function() {
		condicional_envio();

		 $('#envio_gratis').change(function(event) {
		 	condicional_envio();
		 });
	});
	function condicional_envio() {
		var condicional_envio_seleccionado = $('#envio_gratis option:selected').val();

		if(condicional_envio_seleccionado == 'cantidad'){
			$('.condicional_envio_set').show();
		}else{
			$('.condicional_envio_set').hide();
		}
	}
/*--FIN condicionales de envio*/


/*-- Caracteristicas adicionales*/
	// $(document).ready(function() {

	// });
	function delCaracteristicaFields(id){
		$('#'+id).remove();
	}

	function agregarCaracteristicaFields(){
		var caracteristicasEnForm = $('#grupo_de_caracteristicas').find('[data-caracteristica="producto_caracteristica"]');
		var newCaracteristicaField = 0;

		if(parseInt(caracteristicasEnForm.length) > 0){	
			for (var i = 0; i < caracteristicasEnForm.length; i++) {
				var caracteristicaField =parseInt(caracteristicasEnForm[i].id.split('_')[1]);
				if(caracteristicaField>=newCaracteristicaField){
					newCaracteristicaField = caracteristicaField+1;
				}
			}
		}else{
			newCaracteristicaField=1;
		}

		console.log(newCaracteristicaField);

		$.ajax({
			url: '#path#productos/getNewCaracteristica/'+newCaracteristicaField,
			type: 'post',
			dataType: 'json',
			data: {},
		})
		.done(function(data) {
			console.log("success");
			console.log(data);
			$('#grupo_de_caracteristicas').append(data.campos);
		})
		.fail(function(response) {
			console.log("error");
			console.log(response);
		})
		.always(function() {
			console.log("complete");
		})
	}
/*--FIN Caracteristicas adicionales*/


/*-- Estados especiales */
	$(document).ready(function() {
		$('input[type=radio][name=estadoEspecial]').change(function() {
		    if (this.value == 'preventa') {
		    	$('#label_estado_especial').addClass('slb_preventa');
		    	$('#label_estado_especial').removeClass('slb_aPedido');
		    	$('#label_estado_especial').removeClass('slb_liquidacion');
		    	$('#label_estado_especial').removeClass('slb_otro');

		    		$('#estado_especial_descripcion').val('Preventa');
		    		$('#label_estado_especial').html('Preventa');

		    }else if (this.value == 'aPedido') {
		    	$('#label_estado_especial').addClass('slb_aPedido');
		    	$('#label_estado_especial').removeClass('slb_preventa');
		    	$('#label_estado_especial').removeClass('slb_liquidacion');
		    	$('#label_estado_especial').removeClass('slb_otro');


		    	$('#estado_especial_descripcion').val('A pedido');
		    	$('#label_estado_especial').html('A pedido');

		    }else if(this.value == 'liquidacion'){
		    	$('#label_estado_especial').addClass('slb_liquidacion');
		    	$('#label_estado_especial').removeClass('slb_preventa');
		    	$('#label_estado_especial').removeClass('slb_aPedido');
		    	$('#label_estado_especial').removeClass('slb_otro');

		    		$('#estado_especial_descripcion').val('Liquidación');
		    		$('#label_estado_especial').html('Liquidación');

		    }else if(this.value == 'e_otro'){
		    	$('#label_estado_especial').addClass('slb_otro');
		    	$('#label_estado_especial').removeClass('slb_preventa');
		    	$('#label_estado_especial').removeClass('slb_aPedido');
		    	$('#label_estado_especial').removeClass('slb_liquidacion');

		    		$('#estado_especial_descripcion').val('Refurbished');
		    		$('#label_estado_especial').html('Refurbished');

		    }
		});

		$('#estado_especial_descripcion').on('input', function() {
			$('#label_estado_especial').html($('#estado_especial_descripcion').val());
		});


		$('#etiqueta_estado_especial').change(function(event) {
			if ($(this).val() == 'si') {
				$('#dash_set_estados_especiales').show();
			}else if($(this).val() == 'no'){
				$('#dash_set_estados_especiales').hide();
			}
		});
	});
/*--FIN Estados especiales */

/*-- campos de proveedores*/
	function delProveedorFields(id){
		$('#'+id).remove();
	}

	function agregarProveedorFields(){
		var proveedoresEnForm = $('#proveedores_asociados_producto').find('[data-proveedores="proveedor_datos"]');
		var newProveedorField = 0;

		if(parseInt(proveedoresEnForm.length) >0){	
			for (var i = 0; i < proveedoresEnForm.length; i++) {
				var proveedorField =parseInt(proveedoresEnForm[i].id.split('_')[2]);
				if(proveedorField>=newProveedorField){
					newProveedorField = proveedorField+1;
				}
			}
		}else{
			newProveedorField=1;
		}

		console.log(newProveedorField);

		$.ajax({
			url: '#path#proveedor/GetNewProveedorField/'+newProveedorField,
			type: 'post',
			dataType: 'json',
			data: {},
		})
		.done(function(data) {
			console.log("success");
			console.log(data);
			$('#proveedores_asociados_producto').append(data.campos);
		})
		.fail(function(response) {
			console.log("error");
			console.log(response);
		})
		.always(function() {
			console.log("complete");
		})
	}
/*--FIN campos de proveedores*/

/*-- Calulos de pesificaciones de valores de dolar precio dolar*/

	$(document).ready(function() {	
		$('#producto_precio_dolar_publico').on('input', function() {
			pesificarCampo($('#producto_precio_dolar_publico').val(),$('#dolar_cotizacion').html(),'dash_dolar_publico');
		});

		$('#producto_precio_dolar_costo').on('input', function() {
			pesificarCampo($('#producto_precio_dolar_costo').val(),$('#dolar_cotizacion').html(),'dash_dolar_costo');
		});

	});

	function dolarizarPrecioPesos(){
		var dolar = $('#dolar_cotizacion').html();
		dolar = dolar.replace(/<\/?[^>]+(>|$)/g, "");
		dolar = parseFloat(dolar);

		var precioPublico = $('#producto_precio').val();
		var precioCosto = $('#producto_precio_costo').val();

		precioPublico = parseFloat(precioPublico);
		precioCosto = parseFloat(precioCosto);

		var precioPublicoDolarizado = precioPublico/dolar;
		var precioCostoDolarizado = precioCosto/dolar;

		$('#producto_precio_dolar_publico').val(precioPublicoDolarizado.toFixed(2));
		$('#producto_precio_dolar_costo').val(precioCostoDolarizado.toFixed(2));

		pesificarCampo($('#producto_precio_dolar_publico').val(),$('#dolar_cotizacion').html(),'dash_dolar_publico');
		pesificarCampo($('#producto_precio_dolar_costo').val(),$('#dolar_cotizacion').html(),'dash_dolar_costo');
	}

	function pesificarCampo(dolarizarValor, dolarValor, outputIdElement){
		var resutlado;
		var dolarCal = dolarizarValor;

		if(dolarCal == ''){
			dolarCal = 0;
		}else{
			dolarCal = parseFloat(dolarCal);
		}

		var dolar = dolarValor;
		dolar = dolar.replace(/<\/?[^>]+(>|$)/g, "");
		dolar = parseFloat(dolar);
		resultado = dolar * dolarCal;
		$('#'+outputIdElement).html(resultado.toFixed(2));
	}
/*-- FIN Calulos de pesificaciones de valores de dolar precio dolar*/

/* -----------------AGREGAR PRODUCTO */

function agregarProducto(){
	var datosFormulario = new FormData(); 

 	datosFormulario.append('pdf_producto', $('#pdf_producto').prop('files')[0]);

	var imagenesAcargar = Object.keys(archivoDeImagenes);
	if (imagenesAcargar.length >= 0) {
		for (var i = 0; i < imagenesAcargar.length; i++) {
			if (archivoDeImagenes[imagenesAcargar[i]].status == true) {
				datosFormulario.append('archivosAsubir_'+i, archivoDeImagenes[imagenesAcargar[i]]);
			}
		}
	}

	var camposFormulario = $('#add_producto_articulo').serializeObject();

	$.each(camposFormulario, function(i, item) {
	    // console.log(camposFormulario[i]);
	    datosFormulario.append(i, camposFormulario[i]);
	});
	    // console.log(datosFormulario);


	// datosFormulario.append('data', decodeURIComponent($('#add_producto_articulo').serializeObject()));
	// var formdata = new FormData(datosForm);


	$.ajax({
        url: '#path#productos/alta', // point to server-side PHP script 
        dataType: 'json',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        async: false,
        data: datosFormulario,                         
        type: 'post',
    	beforeSend: function(result){
		    $('#loading').show();
    	}
     })
	.done(function(response) {
		console.log("success");
		console.log(response);
		if(response.state === true){
			notificacion(response.notification, 'exito', 0.5,99);
		}else{
			notificacion(response.notification, 'error', 0.5,99);
		}
	})
	.fail(function(response) {
		console.log("error");
		console.log(response);
	})
	.always(function(response) {
		$('#loading').hide();
		console.log("complete");
		console.log(response.responseText);
	});
	
}


/*FIN AGREGAR PRODUCTO */

/* -----------------AGREGAR CONJUNTOS DE PRODUCTO */
function agregarProductoConjunto(){
	var datosFormulario = new FormData(); 

	var imagenesAcargar = Object.keys(archivoDeImagenes);
	if (imagenesAcargar.length >= 0) {
		for (var i = 0; i < imagenesAcargar.length; i++) {
			if (archivoDeImagenes[imagenesAcargar[i]].status == true) {
				datosFormulario.append('archivosAsubir_'+i, archivoDeImagenes[imagenesAcargar[i]]);
			}
		}
	}

	var camposFormulario = $('#add_producto_conjunto').serializeObject();

	$.each(camposFormulario, function(i, item) {
	    // console.log(camposFormulario[i]);
	    datosFormulario.append(i, camposFormulario[i]);
	});
	    // console.log(datosFormulario);


	// datosFormulario.append('data', decodeURIComponent($('#add_producto_articulo').serializeObject()));
	// var formdata = new FormData(datosForm);


	$.ajax({
        url: '#path#productos/alta', // point to server-side PHP script 
        dataType: 'json',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: datosFormulario,                         
        type: 'post',
    	beforeSend: function(result){
		    $('#loading').show();
    	}
     })
	.done(function(response) {
		console.log("success");
		console.log(response);
		if(response.state === true){
			notificacion(response.notification, 'exito', 0.5,99);
		}else{
			notificacion(response.notification, 'error', 0.5,99);
		}
	})
	.fail(function(response) {
		console.log("error");
		console.log(response);
	})
	.always(function(response) {
		$('#loading').hide();
		console.log("complete");
		console.log(response.responseText);
	});
	
}
/*FIN AGREGAR PRODUCTO */

/* -----------------EDITAR PRODUCTO */
	function editarProducto(){
		var datosFormulario = new FormData(); 

	 	datosFormulario.append('pdf_producto', $('#pdf_producto').prop('files')[0]);

		var imagenesAcargar = Object.keys(archivoDeImagenes);
		if (imagenesAcargar.length >= 0) {
			for (var i = 0; i < imagenesAcargar.length; i++) {
				if (archivoDeImagenes[imagenesAcargar[i]].status == true) {
					datosFormulario.append('archivosAsubir_'+i, archivoDeImagenes[imagenesAcargar[i]]);
				}
			}
		}

		// console.log(imagenesToAcction);

		var camposFormulario = $('#edit_producto_articulo').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    // console.log(camposFormulario[i]);
		    datosFormulario.append(i, camposFormulario[i]);
		});

			datosFormulario.append('imgAction', imagenesToAcction);
		    // console.log(datosFormulario);

		// datosFormulario.append('data', decodeURIComponent($('#add_producto_articulo').serializeObject()));
		// var formdata = new FormData(datosForm);

		$.ajax({
	        url: '#path#productos/modificar', // point to server-side PHP script 
	        dataType: 'json',  // what to expect back from the PHP script, if anything
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: datosFormulario,                         
	        type: 'post',
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
	     })
		.done(function(response) {
			console.log("success");
			console.log(response);
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
			}else{
				notificacion(response.notification, 'error', 0.5,99);
			}
		})
		.fail(function(response) {
			console.log("error");
			console.log(response);
		})
		.always(function(response) {
			$('#loading').hide();
			console.log("complete");
			console.log(response.responseText);
		});
		
	}
/*FIN EDITAR PRODUCTO */


/*EDITAR PRODUCTO */

	const imagenesToAcction = [];
	/*--FIN Calcular ganancias*/
	function img_to_del(idImg) {
		if($('#img_edit_id_'+idImg).hasClass('img_active_to_del')){
			$('#img_edit_id_'+idImg).removeClass('img_active_to_del');
			imagenesToAcction[idImg]= '';

			var allImg =$('.img_edit_cont').find('.img_active_to_principal');

			if (allImg.length == 0){
				$('#prin_img_'+idImg).show();
			}


		}else{
			imagenesToAcction[idImg]= 'del';
			$('#img_edit_id_'+idImg).addClass('img_active_to_del');
			$('#prin_img_'+idImg).hide();
		}
		
	}

	function img_to_principal(idImg) {
		if($('#img_edit_id_'+idImg).hasClass('img_active_to_principal')){
			$('#img_edit_id_'+idImg).removeClass('img_active_to_principal');
			imagenesToAcction[idImg]= '';

			$('#del_img_'+idImg).show();

			var allImg =$('.img_edit_cont').find('.prin_img');

			for (var i = 0; i < allImg.length; i++) {
				if('prin_img_'+idImg != allImg[i].id){

					// console.log(allImg[i].id);
					var bloqueImg = allImg[i].id;
					var idBloqueImg = bloqueImg.split("_");
					// console.log(idBloqueImg[2]);
					if(!$('#img_edit_id_'+idBloqueImg[2]).hasClass('img_active_to_del')){
						console.log($('#img_edit_id_'+idBloqueImg[2])+' no tiene');
						$('#'+allImg[i].id).show();
					}
				}

			}
		}else{
			imagenesToAcction[idImg]= 'principal';
			$('#img_edit_id_'+idImg).addClass('img_active_to_principal');
			$('#del_img_'+idImg).hide();

			var allImg =$('.img_edit_cont').find('.prin_img');

			for (var i = 0; i < allImg.length; i++) {
				if('prin_img_'+idImg != allImg[i].id){

					$('#'+allImg[i].id).hide();
				}
				// console.log(allImg[i].id);
			}
		}
		
	}

	/*-- Grupo datos*/

/*FIN EDITAR PRODUCTO */

/*boton desplegable options tabla*/
function desplegable_options(id){

  var desplegable_options = $('#desplegable_'+id+'>.desplegable_options');
  var ui_desplegable_container = $('#desplegable_container');
  if (desplegable_options.hasClass('desplegado')) {
    desplegable_options.removeClass('desplegado');
    desplegable_options.hide();
    ui_desplegable_container.hide();
  }else{
    desplegable_options.addClass('desplegado');
    desplegable_options.show();
    ui_desplegable_container.show();
    ui_desplegable_container.click(function(event) {
      if (desplegable_options.hasClass('desplegado')) {
        desplegable_options.removeClass('desplegado');
        desplegable_options.hide();
        ui_desplegable_container.hide();
      }
    });

  }
}
/*FIN boton desplegable options tabla*/

/*Agregar CONJUNTO PRODUCTO */
$(document).ready(function() {
	$('#form_search_conjunto').submit(function(event) {
		event.preventDefault();
		var todos = $('#form_search_conjunto').serialize();
		refreshConjuntoList();

		var buscar = $('#inputBuscar').val();
		var rows = $('#limit_rows').val();
		var page = 1;

		window.history.replaceState(null, null, "?search="+buscar+"&rows="+rows+"&page="+page);
	});
	
	$('.dash_conjunto_pagination_button').click(function(event) {
		alert('actualizanding');
		var pagina = $(this).attr('data-value');
		$('#page_conjunto').val(pagina);
		refreshConjuntoList();
	});
});

function c_pag_button (page) {
		$('#page_conjunto').val(page);

		var buscar = $('#inputBuscar').val();
		var rows = $('#limit_rows').val();
		window.history.replaceState(null, null, "?search="+buscar+"&rows="+rows+"&page="+page);

		refreshConjuntoList();
}

function addproducto_conjunto(codigo){
	$.ajax({
		url: '#path#productos/AddProductoConjunto',
		type: 'POST',
		dataType: 'json',
		data: {code: codigo},
	})
	.done(function(response) {
		console.log("success");
		console.log(response);
		if(response.status == true){
			refreshConjuntoTemp();
			refreshConjuntoList();
		}
	})
	.fail(function(response) {
		console.log("error");
	})
	.always(function(response) {
		console.log("complete");
	});
	
}

function removeproducto_conjunto(codigo){
	$.ajax({
		url: '#path#productos/RemoveProductoConjunto',
		type: 'POST',
		dataType: 'json',
		data: {code: codigo},
	})
	.done(function(response) {
		console.log("success");
		console.log(response);
		if(response.status == true){
			refreshConjuntoTemp();
			refreshConjuntoList();
		}
	})
	.fail(function(response) {
		console.log("error");
	})
	.always(function(response) {
		console.log("complete");
	});
	
}

function refreshConjuntoList(){
	var buscar = $('#inputBuscar').val();
	var rows = $('#limit_rows').val();
	var page = $('#page_conjunto').val();

	var path = '#path#';

	/*Filters vars*/

	var box = $('#f_box').val();
	if (box == null){console.log('null papu');}else{console.log(box);}

	var precio = $('#f_precio').val();
	if (precio == null){console.log('null papu');}else{console.log(precio);}

	var categoria = $('#categoria').val();
	if (categoria == null){console.log('null papu');}else{console.log(categoria);}

	var subcategoria = $('#form_subcategoria').val();
	if (subcategoria == null){console.log('null papu');}else{console.log(subcategoria);}

	var fabricante = $('#fabricante').val();
	if (fabricante == null){console.log('null papu');}else{console.log(fabricante);}

	var stock = $('#f_stock').val();
	if (stock == null){console.log('null papu');}else{console.log(stock);}

	var alfabetico = $('#f_alfa').val();
	if (alfabetico == null){console.log('null papu');}else{console.log(alfabetico);}

	var engrupo = $('#grupo_select_f').val();
	if (engrupo == null){console.log('null papu');}else{console.log(engrupo);}

	var porCodigo = $('#f_codigo').val();
	if (porCodigo == null){console.log('null papu');}else{console.log(porCodigo);}

	if(buscar == ''){buscar = 'null';}

	$.ajax({
		url: '#path#productos/GenerarListaConjunto/'+buscar+'/'+rows+'/'+page,
		type: 'POST',
		dataType: 'json',
		data: {path: path, box:box, precio:precio, categoria:categoria, subcategoria:subcategoria, fabricante:fabricante, stock:stock, alfabetico:alfabetico, engrupo:engrupo, porCodigo:porCodigo},
	})
	.done(function(response) {
		console.log("success");
		$('#dash_conjunto').html(response.lista);
		$('#dash_conjunto_paginacion_cont').html(response.paginacionBottom);
		$('#dash_conjunto_list_total_items').html(response.totalProductos);
		
	})
	.fail(function(response) {
		console.log("error");
		console.log(response.responseText);
		
	})
	.always(function(response) {
		console.log("complete");
	});
	
}

function refreshConjuntoTemp(){
	$.ajax({
		url: '#path#productos/ListaTempConjunto',
		dataType: 'html',
	})
	.done(function(response) {
		// console.log("success");
		$('#dash_conjunto_temp').html(response);
	})
	.fail(function(response) {
		// console.log("error");
	})
	.always(function(response) {
		// console.log("complete");
	});
	
}

/*FIN Agregar CONJUNTO PRODUCTO */























</script>