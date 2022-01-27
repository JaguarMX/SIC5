<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

$FechaTs = date('Y-m-d');
$HoraTs = date('H:i:s'); 

$ID = $conn->real_escape_string($_POST['valorId']);
$Observacion = $conn->real_escape_string($_POST['valorObservacion']);

$sql = "UPDATE incidencias SET observacion = '$Observacion', estatus = 1, fecha_ts = '$FechaTs', hora_ts = '$HoraTs' WHERE id = $ID";
if(mysqli_query($conn, $sql)){
	echo '<script >M.toast({html:"La incidencia se actualizó satisfactoriamente.", classes: "rounded"})</script>';	
	?>
	<script>
		var a = document.createElement("a");
		a.href = "../views/incidencias.php";
		a.click();   
	</script>
	<?php
}else{
	echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
mysqli_close($conn);
?>
