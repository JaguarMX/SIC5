<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$IP = $conn->real_escape_string($_POST['valorIP']);
$IPLocal = $conn->real_escape_string($_POST['valorIPLocal']);
$User = $conn->real_escape_string($_POST['valorUser']);
$Pass = $conn->real_escape_string($_POST['valorPass']);
$Port = $conn->real_escape_string($_POST['valorPort']);
$PortWEB = $conn->real_escape_string($_POST['valorPortWEB']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);

$sql_servidor = "SELECT * FROM servidores WHERE ip='$IP' OR ip_local = '$IPLocal'";
if(mysqli_num_rows(mysqli_query($conn, $sql_servidor))>0){
    echo '<script>M.toast({html :"Ya se encuentra un servidor con la misma dirección.", classes: "rounded"})</script>';
}else{
    //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
    $sql = "INSERT INTO servidores (ip, ip_local, nombre, user, pass, port, port_web) VALUES('$IP', '$IPLocal', '$Nombre', '$User', '$Pass', '$Port', '$PortWEB')";
    if(mysqli_query($conn, $sql)){
    	echo '<script>M.toast({html :"El servidor se registró satisfactoriamente.", classes: "rounded"})</script>';
    }else{
    	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
    }
}
?>
<script>
    var a = document.createElement("a");
        a.href = "servidores.php";
        a.click();
</script>