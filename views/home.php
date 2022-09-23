<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
?>
</style>
<title>SIC | Inicio SIC5.0</title>
</head>
<main>
<script>
	$(document).ready(function(){
	    $('#bienvenida').modal();
	    $('#bienvenida').modal('open'); 
	 });
</script>
<body>
	<div class="row">
		 <img class="materialboxed" width="100%" src="../img/banner2SIC.jpg">
	</div>
 	<div class="row container">
 		<h4 class="center-align">Servicios Integrales de Computación</h4>
 	</div>
 	<!--Modal cortes BIENVENIDA-->
	<div id="bienvenida" class="modal">
	  <div class="modal-content">
			<?php
	    	$Usuario = $_SESSION['user_name'];
	    	$MSJ = 'SIC te invita a hacer un buen uso del sistema, que tengas un dia muy productivo. :)';
	  	?>
	    <h3 class="red-text center">¡ Bienvenid@ !</h3><br>
	    <h4 class="blue-text center"><b><?php echo $Usuario ?></b></h4><br>
	    <h5><b>"<?php echo $MSJ ?>"</b></h5>	     
	  </div>
	  <div class="modal-footer">
	      <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar<i class="material-icons right">close</i></a>
	  </div>
	</div>
 	<!--Modal cortes BIENVENIDA-->
</body>
</main>
</html>
