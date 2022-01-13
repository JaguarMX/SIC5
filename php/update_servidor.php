<?php 
session_start();
include('../php/conexion.php');
include('../php/admin.php');
date_default_timezone_set('America/Mexico_City');
$IdServidor = $conn->real_escape_string($_POST['valorIdServior']);
$Ip = $conn->real_escape_string($_POST['valorIp']);
$IPLocal = $conn->real_escape_string($_POST['valorIPLocal']);
$User = $conn->real_escape_string($_POST['valorUser']);
$Pass = $conn->real_escape_string($_POST['valorPass']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Port = $conn->real_escape_string($_POST['valorPort']);
$PortWEB = $conn->real_escape_string($_POST['valorPortWEB']);

$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']!="Administrador"){
  echo "<script >M.toast({html: 'Solo un administrador puede editar un servidor.', classes: 'rounded'});</script>";
}else{
	$sql= "UPDATE servidores SET ip = '$Ip', ip_local = '$IPLocal', user = '$User', pass = '$Pass', nombre = '$Nombre', port = '$Port', port_web = '$PortWEB' WHERE id_servidor = '$IdServidor'";
	if (mysqli_query($conn, $sql)) {
		echo '<script>M.toast({html:"El servidor se actualizó correctamente.", classes: "rounded"})</script>';
		?>
		<script>
		  var a = document.createElement("a");
			a.href = "../views/servidores.php";
			a.click();   
		</script>
		<?php
	}else{
		echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';
	}
}