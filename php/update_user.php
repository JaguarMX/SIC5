<?php 
session_start();
include('../php/conexion.php');
$user_id = $conn->real_escape_string($_POST['valorId']);
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Apellidos = $conn->real_escape_string($_POST['valorApellidos']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
$Email = $conn->real_escape_string($_POST['valorEmail']);

$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area'] == "Administrador" OR $user_id == $id){
	$sql = "UPDATE users SET firstname='$Nombres', lastname='$Apellidos', user_name='$Usuario', user_email = '$Email' WHERE user_id='$user_id'";
	if(mysqli_query($conn, $sql)){
		echo '<script>M.toast({html:"El perfil se actualizó correctamente.", classes: "rounded"})</script>';
		?>
		<script>
		  var a = document.createElement("a");
			a.href = "../views/perfil_user.php";
			a.click();   
		</script>
		<?php
	}else{
		echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}else{
  echo "<script >M.toast({html: 'Sólo un administrador o el mismo usuario puede editar un perfil.', classes: 'rounded'});</script>";
}
mysqli_close($conn);
?>