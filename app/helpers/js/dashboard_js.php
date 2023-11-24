<script type="text/javascript">
$(document).ready(function() {

	var modoOscuroLink = $('#modo_oscuro_style').attr('href');
	var modoClaroLink = $('#modo_claro_style').attr('href');

	$('#dash_modo_oscuro').attr('rel', modoOscuroLink);
	$('#dash_modo_claro').attr('rel', modoClaroLink);


	$('#dash_modo_oscuro').click(function(event) {
		$('#modo_oscuro_style').attr('href',modoOscuroLink);
		$('#modo_claro_style').attr('href','');
	});

	$('#dash_modo_claro').click(function(event) {
		$('#modo_claro_style').attr('href',modoClaroLink);
		$('#modo_oscuro_style').attr('href','');
	});


	$(window).scroll(function(){
	    var aTop = $('.dash_top_bar').height();
	    if($(this).scrollTop()>=aTop){
	    	if (!$('.dash_logo').hasClass('dash_logo_pas')) {
	    		$('.dash_logo').addClass('dash_logo_pas');
	    	}
	    }else{
	    	if ($('.dash_logo').hasClass('dash_logo_pas')) {
	    		$('.dash_logo').removeClass('dash_logo_pas');
	    	}
	    }
	});

});

function dashCondensed() {
	$('#dash_body').addClass('dash_condensed_mode');
	$('#dash_menu').addClass('dash_menu_condensed');
}
</script>