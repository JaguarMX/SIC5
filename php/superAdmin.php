<?php 
include('conexion.php');
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']=="Administrador" AND (in_array($area['user_id'], array(10, 49, 25, 28, 101)))){
}else{
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
  	echo '<script>admin();</script>';
	mysqli_close($conn);
	exit;
}
?>