<script>
	$(document).ready(function() {
		preventSumbitCategoriaForm();
		preventSubmitSearchCategoria();
		preventSumbitEditCategoriaForm();
	});

/* -----------------AGREGAR CATEGORIA */

	function agregarCategoria(){

		var datosFormulario = new FormData(); 
		var camposFormulario = $('#add_categoria').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    // console.log(camposFormulario[i]);
		    datosFormulario.append(i, camposFormulario[i]);
		});
		    // console.log(datosFormulario);

		$.ajax({
	        url: '#path#categoria/alta', // point to server-side PHP script 
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
				$('#categoria_nombre').val('');
				reloadListadoCategoria();
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

	function addNuevaCategoria(){
		$.ajax({
			url: '#path#categoria/agregar/mdl',
			dataType: 'html',
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitCategoriaForm();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	function reloadListadoCategoria(){
		var search = '';

		if($('#inputBuscarCategoria').val() !== ''){
			search = $('#inputBuscarCategoria').val();
		}

		$.ajax({
			url: '#path#categoria/listadoJS',
			type: 'POST',
			dataType: 'html',
			data: {search: search},
			async: false,
		})
		.done(function(contenido) {
			console.log(contenido);
			$('#listado_categoria').html(contenido);
		})
		.fail(function() {
			console.log("fail");
			$('#listado_categoria').html('Ajax Error!');
		})
		.always(function() {
			console.log("complete");
		});
	}

	function preventSumbitCategoriaForm(){
	    $('#add_categoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		agregarCategoria();
	       }
	    });
	}

	function preventSubmitSearchCategoria(){
	    $('#inputBuscarCategoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		reloadListadoCategoria();
	       }
	    });
	}
/*FIN AGREGAR CATEGORIA */

/* -----------------EDITAR CATEGORIA */
	function editarCategoria(id) {
		$.ajax({
			url: '#path#categoria/editar',
			type: 'POST',
			dataType: 'html',
			data: {id: id},
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitEditCategoriaForm();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		
	}

	function modificarCategoria() {

		var datosFormulario = new FormData(); 
		var camposFormulario = $('#edit_categoria').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    // console.log(camposFormulario[i]);
		    datosFormulario.append(i, camposFormulario[i]);
		});
		    // console.log(datosFormulario);

		$.ajax({
	        url: '#path#categoria/modificar', // point to server-side PHP script 
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
				reloadListadoCategoria();
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

	function preventSumbitEditCategoriaForm(){
	    $('#edit_categoria').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		modificarCategoria();
	       }
	    });
	}
/*FIN EDITAR CATEGORIA */

/* -----------------ELIMINAR subCATEGORIA */
	function delCategoria(id) {
		$.ajax({
			url: '#path#categoria/eliminarSINO',
			type: 'post',
			dataType: 'html',
			data: {id_categoria: id},
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

	function eliminarCategoria(id) {
		$.ajax({
			url: '#path#categoria/eliminar',
			type: 'post',
			dataType: 'json',
			data: {id_categoria: id},
			async: false,
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
		})
		.done(function(response) {
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				reloadListadoCategoria();
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