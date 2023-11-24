<script>
	var dataCard = '';

$(document).ready(function() {
	window.onkeypress = function(event) {
			var indice = 0;
	      if (event.keyCode == 13) {

	        $('#inputCard').html(dataCard);
	      }else{
	         // console.log("key pressed: "+ event.keyCode);
	          // console.log(String.fromCharCode(event.which))
	          dataCard += String.fromCharCode(event.which);
	          detectInput(dataCard);
	          // dataCard = '';
	      }
	    };
	 
});

function detectInput(data){
	if (data.indexOf('-f-') > -1)
	{
	  alert("hello found inside your_string");
	  customReplace(data);

	}
}

function showDataCard(){
	$('#inputCard').append(dataCard);
}

function customReplace(){
	const obj = /-b-(.*)-f-/.exec(dataCard);

	var ressultado = obj[1];

	// var datas = obj[1].split('=');
	// console.log(datas); 

	var datos = obj[1].split(/\r?\n/);
	console.log(datos); 
	
	return ressultado
}

</script>