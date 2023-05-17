<?php 
include('../php/conexion.php');
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Paquete = $conn->real_escape_string($_POST['valorPaquete']);
$IP = $conn->real_escape_string($_POST['valorIP']);
$FechaCorte = $conn->real_escape_string($_POST['valorFechaCorte']);
$FechaSus = $conn->real_escape_string($_POST['valorFechaSus']);
$FechaCT = $conn->real_escape_string($_POST['valorFechaCT']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);
$Servicio = $conn->real_escape_string($_POST['valorServicio']);

$Extencion = $conn->real_escape_string($_POST['valorExtencion']);
$PT = $conn->real_escape_string($_POST['valorPT']);
if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE ip='$IP' AND id_cliente != '$IdCliente'"))>0){
	echo '<script>M.toast({html :"Esta IP ya se encuentra asignada a un cliente.", classes: "rounded"})</script>';
}else{
	//Variable vacía (para evitar los E_NOTICE)
	if(filter_var($IP, FILTER_VALIDATE_IP)){
		if ($Servicio == "Telefonia") {
			$sql = "UPDATE clientes SET nombre = '$Nombres', telefono = '$Telefono', lugar = '$Comunidad', direccion = '$Direccion', referencia = '$Referencia', paquete = '$Paquete', ip = '$IP', fecha_corte = '$FechaCorte', coordenadas = '$Coordenada', servicio = '$Servicio', fecha_instalacion = '$FechaSus', corte_tel = '$FechaCT', tel_servicio = '$Extencion', paquete_t = '$PT' WHERE id_cliente = $IdCliente";
		}else{
			$TipoInt = $conn->real_escape_string($_POST['valorTipo']);
			$Contratro = 0;
			$Prepago = 1;
			if ($TipoInt == 1) {
				$Contratro = 1;
				$Prepago = 0;
			}else if($TipoInt == 2){
				$Contratro = 2;
				$Prepago = 0;
			}
			$sql = "UPDATE clientes SET nombre = '$Nombres', telefono = '$Telefono', lugar = '$Comunidad', direccion = '$Direccion', referencia = '$Referencia', paquete = '$Paquete', ip = '$IP', fecha_corte = '$FechaCorte', coordenadas = '$Coordenada',servicio = '$Servicio', contrato = '$Contratro', Prepago = '$Prepago', fecha_instalacion = '$FechaSus', corte_tel = '$FechaCT', paquete_t = '$PT'  WHERE id_cliente = $IdCliente ";
		}
		if(mysqli_query($conn, $sql)){
			echo '<script>M.toast({html :"Se ha actualizado la informacion correctamente.", classes: "rounded"})</script>';
			echo '<script>recargar2()</script>';
		}else{
			echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
		}
	}else{
		echo '<script>M.toast({html :"Por favor ingrese una IP valida.", classes: "rounded"})</script>';	
	}
}
mysqli_close($conn);
?>