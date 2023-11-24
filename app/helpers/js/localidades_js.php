<script>
$(document).ready(function() {
	$('.localidad_selec').change(function(event) {
		loadLocalidadSelect($(this).val());
	});
});

function inicializarSelectProvincias(){
	$('.localidad_selec').change(function(event) {
		loadLocalidadSelect($(this).val());
	});
}

function loadLocalidadSelect(id_provincia) {
	$.ajax({
		url: '#path#localidades/GetLocalidadesByProvinciaSelect',
		type: 'POST',
		dataType: 'html',
		data: {id_provincia: id_provincia},
		async: false,
	})
	.done(function(response) {
		console.log("success");
		$('#select_Localidades').html(response)
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
}

function loadLocalidadSelectPreSeleccionada(id_provincia, id_localidad) {
	$.ajax({
		url: '#path#localidades/GetLocalidadesByProvinciaSelect',
		type: 'POST',
		dataType: 'html',
		data: {id_provincia: id_provincia, id_localidad: id_localidad},
		async: false,
	})
	.done(function(response) {
		console.log("success");
		$('#select_Localidades').html(response)
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
}

function loadProvinciaSelectPreSeleccionada(id_provincia) {
	$.ajax({
		url: '#path#localidades/GetProvinciaSelect',
		type: 'POST',
		dataType: 'html',
		data: {id_provincia: id_provincia},
		async: false,
	})
	.done(function(response) {
		console.log("success");
		$('#select_Provincias').html(response)
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
}

function getCpByLocalidad(id_localidad){
	var codigoPostal = '';
	$.ajax({
		url: '#path#localidades/Getcpbylocalidad',
		type: 'POST',
		dataType: 'html',
		data: {id_localidad: id_localidad},
		async: false,
	})
	.done(function(response) {
		codigoPostal = response;
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});

	return codigoPostal;
}
</script>