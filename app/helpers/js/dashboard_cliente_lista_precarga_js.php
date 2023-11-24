<script>
$(document).ready(function() {
	$('.lista-precarga-input').on("input", function(){
        $(".boxtop").html($(this).val());

        var id_ul = $(this).attr('data-precarga');
        var cantidadDeCaracteres = $(this).val();
        var cp = $(this).val();

 	    if (cantidadDeCaracteres.length >= 3) {
 	    	getListaLocalidades(cp, id_ul);
 	    }

 	    if ($('#lista_precarga_'+id_ul).find('li').length > 10) {
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'scroll');
 	    	var altoLi = $('#lista_precarga_1').find('li').css("height");
 	    	altoLi.replace('px', '');

 	    	var altoLista = parseFloat(altoLi)*10;
 	    	 $('#lista_precarga_'+id_ul).css('height', altoLista+'px');
 	    }else{
 	    	$('#lista_precarga_'+id_ul).css('height', 'initial');
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'initial');
 	    }

 	    inputListaPrecargaFocusOut(id_ul);
 	    inputListaPrecargaOnfocus(id_ul);
    });
});

function prepararListaPrecarga() {
	$('.lista-precarga-input').on("input", function(){
        $(".boxtop").html($(this).val());

        var id_ul = $(this).attr('data-precarga');
        var cantidadDeCaracteres = $(this).val();
        var cp = $(this).val();

 	    if (cantidadDeCaracteres.length >= 3) {
 	    	getListaLocalidades(cp, id_ul);
 	    }

 	    if ($('#lista_precarga_'+id_ul).find('li').length > 10) {
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'scroll');
 	    	var altoLi = $('#lista_precarga_1').find('li').css("height");
 	    	altoLi.replace('px', '');

 	    	var altoLista = parseFloat(altoLi)*10;
 	    	 $('#lista_precarga_'+id_ul).css('height', altoLista+'px');
 	    }else{
 	    	$('#lista_precarga_'+id_ul).css('height', 'initial');
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'initial');
 	    }

 	    clickEnitemLista(id_ul);
 	    inputListaPrecargaFocusOut(id_ul);
 	    inputListaPrecargaOnfocus(id_ul);

    });

    inicializarSelectsProvincia_Localidad();
}

function getListaLocalidades(cp, id_ul) {
	$.ajax({
		url: '#path#localidades/GetListaLocalidadesByCP',
		type: 'POST',
		dataType: 'html',
		data: {cp: cp},
		async: false,
    	beforeSend: function(result){
		    $('#search_icon_'+id_ul).show();
    	}
	})
	.done(function(response) {
		console.log("success");
		$('#lista_precarga_'+id_ul).html(response)
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
		$('#search_icon_'+id_ul).hide();
	});
	
}

function inputListaPrecargaFocusOut(id_ul) {
	$('#lista-precarga-input_'+id_ul).focusout(function(event) {
		$(".boxtop").html('LOST FOCUS');
		if($('.dash_lista_precarga').is(':hover')){

		}else{
			$('#lista_precarga_'+id_ul).css('height', '0px');
		}
	});
}

function listaPrecargaOcultar(id_ul){
	$('#lista_precarga_'+id_ul).css('height', '0px');
}

function inputListaPrecargaOnfocus(id_ul) {
	$('#lista-precarga-input_'+id_ul).focus(function(event) {
		$(".boxtop").html('FOCUS');

        var id_ul = $(this).attr('data-precarga');
        var cantidadDeCaracteres = $(this).val();
        var cp = $(this).val();

 	    if (cantidadDeCaracteres.length >= 3) {
 	    	getListaLocalidades(cp, id_ul);
 	    }

 	    if ($('#lista_precarga_'+id_ul).find('li').length > 10) {
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'scroll');
 	    	var altoLi = $('#lista_precarga_1').find('li').css("height");
 	    	altoLi.replace('px', '');

 	    	var altoLista = parseFloat(altoLi)*10;
 	    	 $('#lista_precarga_'+id_ul).css('height', altoLista+'px');
 	    }else{
 	    	$('#lista_precarga_'+id_ul).css('height', 'initial');
 	    	$('#lista_precarga_'+id_ul).css('overflow-y', 'initial');
 	    }

 	     clickEnitemLista();
	});
}


function clickEnitemLista(id_ul){
    $('.dash_lista_precarga>li').click(function(event) {
    	var id_localidad = $(this).attr('data-iddeprovlocalidad');
    	var id_provincia = $(this).attr('data-id_provincia');
    	var codigoPostal = $(this).attr('data-cp');

    	$('#lista-precarga-input_1').val(codigoPostal);

    	recargarSelectProvincia(id_provincia);
    	recargarSelectLocalidad(id_provincia,id_localidad);
    	inicializarSelectsProvincia_Localidad();
    	listaPrecargaOcultar(id_ul);
    });
}

function recargarSelectProvincia(id_provincia){
	loadProvinciaSelectPreSeleccionada(id_provincia);
}


function recargarSelectLocalidad(id_provincia, id_localidad){
	loadLocalidadSelectPreSeleccionada(id_provincia,id_localidad);
}

function inicializarSelectsProvincia_Localidad(){
	$('.localidad_selec').change(function(event) {
		$('#lista-precarga-input_1').val('');
		loadLocalidadSelect($(this).val());

		$('.select_localidad').change(function(event) {
			var codigoPostal = getCpByLocalidad($(this).val());
			$('#lista-precarga-input_1').val(codigoPostal);
		});
	});
	 	
}
</script>