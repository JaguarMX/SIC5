<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

$FechaTD = date('Y-m-d');
$HoraTD = date('H:i:s'); 

$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Prioridad = $conn->real_escape_string($_POST['valorPrioridad']);
$FechaTO = $conn->real_escape_string($_POST['valorFechaTO']);
$HoraTO = $conn->real_escape_string($_POST['valorHoraTO']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM incidencias WHERE comunidad = '$Comunidad' AND descripcion = '$Descripcion' AND fecha_to = '$FechaTO' AND fecha_td = '$FechaTD'"))>0){
	echo '<script >M.toast({html:"Ya se encuentra una incidencia con los mismos datos registrados.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO incidencias (comunidad, descripcion, prioridad, fecha_to, hora_to, fecha_td, hora_td) VALUES('$Comunidad', '$Descripcion', '$Prioridad', '$FechaTO', '$HoraTO', '$FechaTD', '$HoraTD')";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"La incidencia se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
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
}
mysqli_close($conn);
?>
