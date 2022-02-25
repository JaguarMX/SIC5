<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];
$FechaTs = date('Y-m-d');
$HoraTs = date('H:i:s'); 

$ID = $conn->real_escape_string($_POST['valorId']);

$incidencia= mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM incidencias WHERE id = '$ID'"));

$IDComunidad = $incidencia['comunidad'];
$Descripcion = $incidencia['descripcion'];
$Referencia = 'N/A';
$Comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = '$IDComunidad'"));
$Nombres = 'SIC-'.$Comunidad['nombre'];
$Descripcion= "'Mantenimiento: ".$Descripcion."'";

if (isset($Nombres)) {
	$sql_cliente = mysqli_query($conn, "SELECT * FROM especiales WHERE nombre='$Nombres' AND telefono='4339256286' AND lugar='$IDComunidad'");
	if(mysqli_num_rows($sql_cliente)>0){
	 	echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	$cliente =  mysqli_fetch_array($sql_cliente);            
        $IdCliente = $cliente['id_cliente'];
		$sql_mto = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro) VALUES ($IdCliente, ".$Descripcion.", '$FechaTs', '$HoraTs', '$id_user')";
		if(mysqli_query($conn, $sql_mto)){
			$sql_incidencia = "UPDATE incidencias SET observacion = 'Mantenimiento', estatus = 1, fecha_ts = '$FechaTs', hora_ts = '$HoraTs' WHERE id = $ID";
			if(mysqli_query($conn, $sql_incidencia)){
				echo '<script >M.toast({html:"La incidencia se actualizó satisfactoriamente.", classes: "rounded"})</script>';	
			}else{
				echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
			}
			?>
			<script>
				var a = document.createElement("a");
				a.href = "../views/mantenimiento.php";
				a.click();
				</script>
			<?php
		}else{
			echo '<script>M.toast(html:"Ha ocurrido un error.", classes: "rounded")</script>';	
		}
	}else{
		$sql = "INSERT INTO especiales (nombre, telefono, lugar, referencia, usuario, mantenimiento) 
				VALUES('$Nombres', '4339256286', '$IDComunidad',  '$Referencia','$id_user', '1')";
		if(mysqli_query($conn, $sql)){
			echo '<script >M.toast({html:"Se registro el cliente especial satisfactoriamente.", classes: "rounded"})</script>';
			$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_cliente) AS id FROM especiales"));            
        	$IdCliente = $ultimo['id'];
			$sql_mto = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro) VALUES ($IdCliente, ".$Descripcion.", '$FechaTs', '$HoraTs', '$id_user')";
			if(mysqli_query($conn, $sql_mto)){
				$sql_incidencia = "UPDATE incidencias SET observacion = 'Se paso a Mantenimiento', estatus = 1, fecha_ts = '$FechaTs', hora_ts = '$HoraTs' WHERE id = $ID";
				if(mysqli_query($conn, $sql_incidencia)){
					echo '<script >M.toast({html:"La incidencia se actualizó satisfactoriamente.", classes: "rounded"})</script>';	
				}else{
					echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
				}
				?>
				<script>
					var a = document.createElement("a");
					a.href = "../views/mantenimiento.php";
					a.click();
				</script>
				<?php
			}else{
				echo '<script>M.toast(html:"Ha ocurrido un error.", classes: "rounded")</script>';	
			}
		}else{
			echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
		}
	}
}
mysqli_close($conn);
?>
