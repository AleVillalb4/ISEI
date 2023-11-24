<script>
	$(document).ready(function() {
		preventSumbitClienteFormBuscar();
		offsetLista();
	});

	function buscarCliente(){

		var buscar = $('#inputBuscarCliente').val();
		var rows = $('#limit_rows').val();
		var pagina = $('#page').val();

		$.ajax({
	        url: '#path#cliente/GetClientes', // point to server-side PHP script 
	        type: 'post',
	        dataType: 'html',  // what to expect back from the PHP script, if anything
	        data: {buscar: buscar, rows: rows, ajax: true, page: pagina},                         
	        async: false,
	    	beforeSend: function(result){
			    $('#loading').show();
	    	}
	     })
		.done(function(response) {
			console.log("success");
			console.log(response);
			$('#lista_completa').html(response);
			preventSumbitClienteFormBuscar();
			offsetLista();
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

	function preventSumbitClienteFormBuscar(){
	    $('#listado_clientes').bind('keypress', function(e){
	    	//previene que al apretar enter en un input se active el submit y actualice la pagina
	       if(e.keyCode == 13) { 
	       		e.preventDefault(); 
	       		buscarCliente();
	       		$('#inputBuscarCliente').select();
				setLink();
	       }
	    });


	   	$('#listado_clientes').submit(function(event) {
	   		event.preventDefault(); 
	   		buscarCliente();
	   		$('#inputBuscarCliente').select();
			setLink();
	   	});

	   	$('#inputBuscarCliente').focus(function(event) {
	   		$('#inputBuscarCliente').select();
	   	});
	}

	function offsetLista(){
		$('.offsetValue').click(function(event) {
			var pagina = $(this).attr('data-offset');
			$('#page').val(pagina);
			setLink();
			buscarCliente();

		});
	}

	function setLink(){
		var buscar = $('#inputBuscarCliente').val();
		var rows = $('#limit_rows').val();
		var pagina = $('#page').val();
   		window.history.replaceState(null, null, "?buscar="+buscar+"&rows="+rows+"&page="+pagina);
	}

</script>