<?php 
include('../php/conexion.php');
include('is_logged.php');

$ID = $conn->real_escape_string($_POST['valorID']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Prioridad = $conn->real_escape_string($_POST['valorPrioridad']);
$FechaTO = $conn->real_escape_string($_POST['valorFechaTO']);
$HoraTO = $conn->real_escape_string($_POST['valorHoraTO']);


$sql = "UPDATE incidencias SET comunidad = '$Comunidad', descripcion = '$Descripcion', prioridad = '$Prioridad', fecha_to = '$FechaTO', hora_to = '$HoraTO' WHERE id = $ID";
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
