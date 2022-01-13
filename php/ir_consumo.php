<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

$id_cliente = $conn->real_escape_string($_POST['valorCliente']);

//DATOS DEL CLIENTE
$datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
//SACAMOS LA INFO DE LA COMUNIDAD
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
//SACAMOS LA INFO DEL SERVIDOR
$id_servidor = $comunidad['servidor'];
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $id_servidor"));

//////// INFORMACION DEL SERVIDOR
$ServerList = $serv['ip_local'] ; //ip_de_tu_API
$Port = $serv['port_web']; //puerto_API

$URL =$ServerList.':'.$Port.'/graphs/';

echo '<script>M.toast({html:"Esto puede tardar algunos segundos.", classes: "rounded"})</script>';
?>
<script>
	var a = document.createElement("a");
		a.target = "_blank";
		a.href = "http://'.$URL.'";
		a.click();
</script>

