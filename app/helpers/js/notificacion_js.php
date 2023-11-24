<script>
/*notification*/
  function notificacion(msg, clase, delay, id){
  	if (delay == null){
  		delay = 1
  	}
    if ($.isArray(msg)) {
      var notificationElement = new Array();

      for (var i = 0; i < msg.length; i++) {
        // alert(msg[i]);
        notificationElement[i] = '<div id="notificacion_'+i+'" class="notificacion '+clase+' notifx"><span id="cerrar_noti_'+i+'" class="cerrar-noti"><i class="far fa-times-circle"></i></span><p>'+ msg[i]+'</p></div>';
        notificacion_set(i,delay,notificationElement[i]);
      }

    }else{    
      var notificationElement = '<div id="notificacion_'+id+'" class="notificacion '+clase+' notifx"><span id="cerrar_noti_'+id+'" class="cerrar-noti"><i class="far fa-times-circle"></i></span><p>'+msg+'</p></div>';
      setTimeout(function() {
        notificacion_set(id,delay,notificationElement);
      }, delay);
    } 
  }

  function notificacion_set(id, delay, notificationElement){
      $('.notificacion-area').append(notificationElement);
      $('#cerrar_noti_'+id).click(function() {
        $('#notificacion_'+id).hide(400);
      });

      setTimeout(function() {
        $('#notificacion_'+id).hide(400, function() {
          $('#notificacion_'+id).remove();
        });
      }, delay+5000);
  }
  /*end notification*/
</script>