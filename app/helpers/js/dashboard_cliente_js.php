<script>
	$(document).ready(function() {
		preventSumbitClienteForm();
	});
/* -----------------AGREGAR CLIENTE */
	function agregarCliente(){

		var datosFormulario = new FormData(); 
		var camposFormulario = $('#add_cliente').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    datosFormulario.append(i, camposFormulario[i]);
		});
		    // console.log(datosFormulario);

		$.ajax({
	        url: '#path#cliente/alta', // point to server-side PHP script 
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
				$('#add_cliente').trigger("reset");
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
		});
	}

	function preventSumbitClienteForm(){
	    $('#add_cliente').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		agregarCliente();
	       }
	    });
	}

	function nombre_razonSocial() {
		$('#razonsocial_mismoNombre').change(function(event) {
			if($('#cliente_nombre').val() == '' || $('#cliente_apellido').val() == ''){
				notificacion('<b>Nombre</b> y <b>Apellido</b> no puede estar en blanco', 'error', 0.5,99);
				$('#razonsocial_mismoNombre').prop('checked', false);
			}else{
				var razonSocial = $('#cliente_nombre').val()+' '+$('#cliente_apellido').val();
				$('#cliente_razon_social').val(razonSocial);
			}
		});
	}
/*FIN AGREGAR CLIENTE */

/* -----------------AGREGAR DOMICILIO */
	function addDomicilio(token){
		$.ajax({
			url: '#path#cliente/agregarDireccion',
	        type: 'post',
			dataType: 'html',
	        data: {token: token},                         
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitClienteDireccionForm();
			prepararListaPrecarga();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	function preventSumbitClienteDireccionForm(){
	    $('#add_direccion').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		agregarDommicilio();
	       }
	    });
	}

	function agregarDommicilio() {
		var datosFormulario = new FormData(); 
		var camposFormulario = $('#add_direccion').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    datosFormulario.append(i, camposFormulario[i]);
		});

		$.ajax({
	        url: '#path#cliente/altaDomicilio', // point to server-side PHP script 
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
				updateDomicilioList();
				modalClose();
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
/*FIN AGREGAR DOMICILIO */


/* -----------------EDITAR DOMICILIO */
	function editDomicilio(id,token){
		$.ajax({
			url: '#path#cliente/editarDireccion',
	        type: 'post',
			dataType: 'html',
	        data: {token:token, id:id},                         
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitClienteEditDireccionForm();
			prepararListaPrecarga();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	function preventSumbitClienteEditDireccionForm(){
	    $('#edit_direccion').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		editarDommicilio();
	       }
	    });
	}

	function editarDommicilio() {
		var datosFormulario = new FormData(); 
		var camposFormulario = $('#edit_direccion').serializeObject();

		$.each(camposFormulario, function(i, item) {
		    datosFormulario.append(i, camposFormulario[i]);
		});

		$.ajax({
	        url: '#path#cliente/editarDomicilio', // point to server-side PHP script 
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
				updateDomicilioList();
				modalClose();
				// $('#subcategoria_nombre').val('');
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
/*FIN EDITAR DOMICILIO */


/* -----------------ACTUALIZAR DOMICILIO */
	function updateDomicilioList() {
		var token = $('#tokenIdentificator').val();

		$.ajax({
			url: '#path#cliente/getDomiciliosbyToken/'+token+'/'+true,
	        type: 'post',
			dataType: 'html',                   
			async: false,
		})
		.done(function(contenido) {
			$('#cliente_direcciones_cont').html(contenido)
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}
/*FIN ACTUALIZAR DOMICILIO */


/* -----------------ELIMINAR DOMICILIO */
	function eliminarDomicilio(id,token){
		$.ajax({
			url: '#path#cliente/delDireccion',
	        type: 'post',
			dataType: 'html',
	        data: {token:token, id:id},                         
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitClienteEditDireccionForm();
			prepararListaPrecarga();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}


	function deleteDomicilio(id,token) {
		$.ajax({
	        url: '#path#cliente/eliminarDomicilio', // point to server-side PHP script 
	        dataType: 'json',  // what to expect back from the PHP script, if anything
	        type: 'post',
			dataType: 'json',
			async: false,
	        data: {token:token, id:id},                         
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
	     })
		.done(function(response) {
			console.log("success");
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				updateDomicilioList();
				modalClose();
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
/*FIN ELIMINAR DOMICILIO */

/* -----------------PREDETERMINADO DOMICILIO */
	function predeterminadoDomicilio(id,token){
		$.ajax({
			url: '#path#cliente/predeterminadaDireccion',
	        type: 'post',
			dataType: 'html',
	        data: {token:token, id:id},                         
			async: false,
		})
		.done(function(contenido) {
			modalActive(contenido);
			preventSumbitClienteEditDireccionForm();
			prepararListaPrecarga();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}


	function defaultDomicilio(id,token) {
		$.ajax({
	        url: '#path#cliente/predeterminadaDomicilio', // point to server-side PHP script 
	        dataType: 'json',  // what to expect back from the PHP script, if anything
	        type: 'post',
			dataType: 'json',
			async: false,
	        data: {token:token, id:id},                         
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
	     })
		.done(function(response) {
			console.log("success");
			if(response.state === true){
				notificacion(response.notification, 'exito', 0.5,99);
				updateDomicilioList();
				modalClose();
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

/*FIN PREDETERMINADO DOMICILIO */

</script>