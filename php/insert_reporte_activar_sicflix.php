<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

//ASÍ OBTENEMOS LAS VARIABLES DEL ARCHIVO modal_reporte_sicflix.php
$Nombre = $conn->real_escape_string($_POST ['valorNombre']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Coordenadas = $conn->real_escape_string($_POST['valorCoordenada']);
//Aquí obtenemos el valor de "Activar Sicflix" con la variable $Descripcion
$Descripcion = $conn->real_escape_string($_POST['valorReporteTexto']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Fecha = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

//Variable $Estatus que nos indica que el usuario ya se activó
$Estaus = 0;
  
$sql2= "UPDATE clientes SET nombre = '$Nombre', telefono = '$Telefono', direccion = '$Direccion', referencia='$Referencia', coordenadas = '$Coordenadas' WHERE id_cliente=$IdCliente ";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
}
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO `reporte_sicflix` (cliente, descripcion, estatus,  fecha_registro, registro) VALUES ($IdCliente, '$Descripcion',$Estaus, '$Fecha', $id_user)";
if(mysqli_query($conn, $sql)){
	?>
  <script>    
    var a = document.createElement("a");
      a.href = "../views/reportes_sicflix.php";
      a.click();
  </script>
  <?php
}else{
	echo  '<script>M.toast({html:"Ha ocurrido un error con el insert.", classes: "rounded"})</script>';	
}

mysqli_close($conn);
?>  