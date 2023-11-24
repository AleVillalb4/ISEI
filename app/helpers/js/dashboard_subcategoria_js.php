<script>
	$(document).ready(function() {
		preventSumbitSubCategoriaForm();
		filtroCategoriaSearch();
		preventSubmitSearchSubCategoria();
		preventSumbitEditSubCategoriaForm();
	});


/* ----------------- AGREGAR subCATEGORIA */
	function agregarSubCategoria(){

		var datosFormulario = new FormData(); 
		var camposFormulario = $('#add_subcategoria').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    datosFormulario.append(i, camposFormulario[i]);
		});

		$.ajax({
	        url: '#path#subcategoria/alta', // point to server-side PHP script 
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
			// console.log(response);
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				$('#subcategoria_nombre').val('');
				reloadListadoSubCategoria();
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
			// console.log(response.responseText);
		});
	}

	function addNuevaSubCategoria(){
		var categoria = '';
		categoria = $('#id_categoria_select').val();

		$.ajax({
			url: '#path#subcategoria/agregar/mdl',
			type: 'post',
			dataType: 'html',
			data: {categoria: categoria},
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitSubCategoriaForm();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	function preventSumbitSubCategoriaForm(){
	    $('#add_subcategoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		agregarSubCategoria();
	       }
	    });
	}
/*FIN AGREGAR subCATEGORIA */

/* -----------------EDITAR subCATEGORIA */
	function editarSubCategoria(id) {
		$.ajax({
			url: '#path#subcategoria/editar',
			type: 'POST',
			dataType: 'html',
			data: {id: id},
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitEditSubCategoriaForm();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		
	}

	function preventSumbitEditSubCategoriaForm(){
	    $('#edit_subcategoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		modificarSubCategoria();
	       }
	    });
	}

	function modificarSubCategoria() {

		var datosFormulario = new FormData(); 
		var camposFormulario = $('#edit_subcategoria').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    datosFormulario.append(i, camposFormulario[i]);
		});

		$.ajax({
	        url: '#path#subcategoria/modificar', // point to server-side PHP script 
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
			// console.log(response);
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				reloadListadoSubCategoria();
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
			// console.log(response.responseText);
		});
	}
/*FIN EDITAR subCATEGORIA */

/* -----------------LISTADO subCATEGORIA */
	function reloadListadoSubCategoria(){
		var search = '';
		var categoria = '';
		categoria = $('#id_categoria_select').val();

		if($('#inputBuscarSubCategoria').val() !== ''){
			search = $('#inputBuscarSubCategoria').val();
		}

		$.ajax({
			url: '#path#subcategoria/listadoJS',
			type: 'POST',
			dataType: 'html',
			data: {search: search, categoria:categoria},
			async: false,
		})
		.done(function(contenido) {
			// console.log(contenido);
			$('#listado_subcategoria').html(contenido);
		})
		.fail(function() {
			console.log("fail");
			$('#listado_subcategoria').html('Ajax Error!');
		})
		.always(function() {
			console.log("complete");
		});
	}

	function preventSubmitSearchSubCategoria(){
	    $('#inputBuscarSubCategoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		reloadListadoSubCategoria();
	       }
	    });
	}

	function filtroCategoriaSearch(){
		$('#id_categoria_select').change(function(event) {
			reloadListadoSubCategoria();
		});
	}
/*FIN LISTADO subCATEGORIA */


/* -----------------ELIMINAR subCATEGORIA */
	function delSubCategoria(id) {
		$.ajax({
			url: '#path#subcategoria/eliminarSINO',
			type: 'post',
			dataType: 'html',
			data: {id_Subcategoria: id},
			async: false,
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
		})
		.done(function(contenido) {
			modalActive(contenido);
		})
		.fail(function(contenido) {
			console.log("error");
		})
		.always(function(contenido) {
			$('#loading').hide();
			console.log("complete");
		});
		
	}

	function eliminarSubCategoria(id) {
		$.ajax({
			url: '#path#subcategoria/eliminar',
			type: 'post',
			dataType: 'json',
			data: {id_Subcategoria: id},
			async: false,
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
		})
		.done(function(response) {
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				reloadListadoSubCategoria();
				modalClose();
			}else{
				notificacion(response.notification, 'error', 0.5,99);
			}
		})
		.fail(function(response) {
			console.log("error");
		})
		.always(function(response) {
			$('#loading').hide();
			console.log("complete");
			console.log(response.responseText);
		});
		
	}
/*FIN ELIMINAR subCATEGORIA */
</script>