<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];
$FechaHoy = date('Y-m-d');
$Hora = date('H:i:s');

$Clientes = mysqli_query($conn,"SELECT * FROM clientes WHERE contrato  = 1 AND fecha_corte < '$FechaHoy'");
if (mysqli_num_rows($Clientes) > 0) {
	while ($cliente = mysqli_fetch_array($Clientes)){
		$id_cliente = $cliente['id_cliente'];
		$id_paquete = $cliente['paquete'];
        $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete = $id_paquete"));
        $Cantidad = $paquete['mensualidad'];
		$fecha_corte = $cliente['fecha_corte'];
		$fechaCorte = strtotime($fecha_corte);
		$fecha_hoy = strtotime($FechaHoy);
		$mes_corte = date('m', $fechaCorte);
		$año_corte = date('Y', $fechaCorte);
		$mes_hoy = date('m', $fecha_hoy);
		if ($mes_hoy == $mes_corte) {
			#echo "mes en curso".$cliente['nombre'] ."<br>";
		}else{
			$diff = ($mes_hoy-$mes_corte);			
			if ($diff == 1) {
				$mes_registrar = ['ENERO' ,'FEBRERO' , 'MARZO' ,'ABRIL' , 'MAYO' , 'JUNIO' , 'JULIO' , 'AGOSTO' , 'SEPTIEMBRE' , 'OCTUBRE' , 'NOVIEMBRE' ,  'DICIEMBRE' ][$mes_corte-1];
				$Descripcion = "$mes_registrar $año_corte (SISTEMA)";
				$ver = "$mes_registrar $año_corte";
				$sql_ver = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id_cliente AND descripcion LIKE '%$ver%' AND tipo = 'Mensualidad'");
				if(mysqli_num_rows($sql_ver)>0){
				    echo "Ya se encuentra un pago del mismo mes y mismo año.";
				}else{
					$mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($id_cliente, '$Cantidad', '$FechaHoy', 'Mensualidad', '$Descripcion', $id_user)";
			        
			        mysqli_query($conn,$mysql);
			        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $id_cliente"));            
			        $id_deuda = $ultimo['id'];

			        $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($id_cliente, '$Descripcion', '$Cantidad', '$FechaHoy', '$Hora', 'Mensualidad', $id_user, 0, 0, 'Credito', $id_deuda, 0)";
			        mysqli_query($conn,$sql);

					echo "REGISTRAR $diff (INICIA: $Descripcion) MES CORTE: $mes_corte  MES HOY: $mes_hoy  $id_cliente<br>";
				}
			}else{

				for ($i=0; $i < $diff; $i++) { 
					$mes_registrar = ['ENERO' ,'FEBRERO' , 'MARZO' ,'ABRIL' , 'MAYO' , 'JUNIO' , 'JULIO' , 'AGOSTO' , 'SEPTIEMBRE' , 'OCTUBRE' , 'NOVIEMBRE' ,  'DICIEMBRE' ][$mes_corte-1+$i];
					$Descripcion = "$mes_registrar $año_corte (SISTEMA)";
					$ver = "$mes_registrar $año_corte";
					$sql_ver = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id_cliente AND descripcion LIKE '%$ver%' AND tipo = 'Mensualidad'");
					if(mysqli_num_rows($sql_ver)>0){
					    echo "Ya se encuentra un pago del mismo mes y mismo año.";
					}else{
						$mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($id_cliente, '$Cantidad', '$FechaHoy', 'Mensualidad', '$Descripcion', $id_user)";
				        
				        mysqli_query($conn,$mysql);
				        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $id_cliente"));            
				        $id_deuda = $ultimo['id'];

				        $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($id_cliente, '$Descripcion', '$Cantidad', '$FechaHoy', '$Hora', 'Mensualidad', $id_user, 0, 0, 'Credito', $id_deuda, 0)";
			        	mysqli_query($conn,$sql);

						echo "CICLO $diff (INICIA: $Descripcion) MES CORTE: $mes_corte  MES HOY: $mes_hoy  $id_cliente<br>";
					}
					if ($mes_registrar == 'DICIEMBRE') {
						$año_corte = date("Y",strtotime($fecha_corte."+ 1 year"));
					}
				}
			}
		}
	}
}