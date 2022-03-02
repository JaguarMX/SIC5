<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$FechaHoy = date('Y-m-d');
$fechanew = strtotime('-2 month', strtotime($FechaHoy));
$DOSMESES = date('Y-m-05', $fechanew);

$Clientes = mysqli_query($conn,"SELECT * FROM clientes WHERE contrato  = 1 AND fecha_corte <= '$DOSMESES'");
if (mysqli_num_rows($Clientes) > 0) {
	while ($cliente = mysqli_fetch_array($Clientes)){
		$id_cliente = $cliente['id_cliente'];
		$Descripcion = 'POR CONTRATO:  Retraso de pago por mas de dos mensualidades, notificar al cliente y/o recoger equipos.';
		if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_cliente = '$id_cliente' AND descripcion = '$Descripcion'"))>0) {
			echo 'YA SE ENCUENTRA UN REPORTE PARA ESTE CLIENTE';
		}else{
			if(mysqli_query($conn, "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($id_cliente, '$Descripcion', '$FechaHoy')")){
				echo 'El reporte se dio de alta con exito';
			}else{
				echo 'Ocurrio un error, no se registro el reporte';
			}
		}
	}
}