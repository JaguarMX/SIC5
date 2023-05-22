<?php
session_start();
include ('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
?>
<table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th>Id Corte</th>
			<th>Usuarios</th>
	        <th>Efectivo</th>
	        <th>HSBC</th>
	        <th>BBVA</th>
	        <th>BANORTE</th>
	        <th>Total Banco</th>
	        <th>Credito</th>
	        <th>Deducible(s)</th>
	        <th>Recibio</th>
	        <th>Fecha y Hora</th>
	        <th>Movimientos</th>
	        <th>Detalles</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$resultado_cortes = mysqli_query($conn, "SELECT * FROM cortes WHERE fecha>='$ValorDe' AND fecha<='$ValorA' ORDER BY usuario DESC");
		$aux = mysqli_num_rows($resultado_cortes);
		if($aux>0){
		$TotalBBVA = 0; $TotalBanorte = 0;  $TotalHSBC = 0;
		$total = 0;  	$totalClientes= 0; 	$totalbanco = 0; 	$totalcredito = 0;	$totaldeducible = 0; 
		while($cortes = mysqli_fetch_array($resultado_cortes)){
			$id_corte =$cortes['id_corte'];
			$pagos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM detalles WHERE id_corte = $id_corte"));
			$sql_pagos = mysqli_query($conn,"SELECT * FROM detalles WHERE id_corte = $id_corte");
			$BBVA = 0; $Banorte = 0;  $HSBC = 0;
			if (mysqli_num_rows($sql_pagos)>0) {
				while ($pago = mysqli_fetch_array($sql_pagos)) {
					$id_pago = $pago['id_pago'];
					$info_pago = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM pagos WHERE id_pago = $id_pago"));
					if ($info_pago['tipo_cambio'] == 'Banco') {
						$DestinoB = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM referencias WHERE id_pago = $id_pago"));
						if ($DestinoB['banco'] == 'BANORTE') {
							$Banorte += $info_pago['cantidad'];
						}else if ($DestinoB['banco'] == 'BBVA') {
							$BBVA += $info_pago['cantidad'];
						}else if ($DestinoB['banco'] == 'HSBC'){
							$HSBC += $info_pago['cantidad'];
						}
					}
				}
			}
			#TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
		    $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$id_corte'");  
		    if (mysqli_num_rows($sql_Deducible) > 0) {
		        $Deducible = mysqli_fetch_array($sql_Deducible);
		        $Deducir = $Deducible['cantidad'];
		    }else{
		        $Deducir = 0;
		    }
			$id_usuario = $cortes['usuario'];
			$usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = $id_usuario"));
			?>
			<tr>
			    <td><b><?php echo $id_corte;?></b></td>
			    <td><?php echo $usuario['firstname'] ?></td>
			    <td>$<?php echo $cortes['cantidad'];?></td>
			    <td>$<?php echo $HSBC; ?></td>
			    <td>$<?php echo $BBVA; ?></td>
			    <td>$<?php echo $Banorte; ?></td>
			    <td>$<?php echo $cortes['banco']; ?></td>
			    <td>$<?php echo $cortes['credito']; ?></td>
			    <td>$<?php echo ($Deducir == 0)? 0:$Deducir.'<br>'.$Deducible['descripcion'];?></td>
			    <td><?php echo $cortes['recibio'];?></td>
			    <td><?php echo $cortes['fecha'].' '.$cortes['hora'];?></td>
			    <td><?php echo $pagos['count(*)'];?></td>
			    <td><form method="post" action="../views/detalle_corte.php"><input id="id_corte" name="id_corte" type="hidden" value="<?php echo $cortes['id_corte']; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
			</tr>
			<?php
			$total += $cortes['cantidad'];
			$totalbanco += $cortes['banco'];
			$totalcredito += $cortes['credito'];
			$totaldeducible += $Deducir;
			$totalClientes += $pagos['count(*)'];
			$aux--;
			$TotalBBVA += $BBVA; $TotalBanorte += $Banorte;  $TotalHSBC += $HSBC;
		}
		?>
			<tr>
			  	<td></td>
			  	<td><h5>EFECTIVO<br>$<?php echo $total; ?></h5></td>
			  	<td><h5>HSBC<br>$<?php echo $TotalHSBC; ?></h5></td>
			  	<td><h5>BBVA<br>$<?php echo $TotalBBVA; ?></h5></td>
			  	<td><h5>BANORTE<br>$<?php echo $TotalBanorte; ?></h5></td>
			  	<td><h5>BANCO<br>$<?php echo $totalbanco; ?></h5></td>
			  	<td><h5>CREDITO<br>$<?php echo $totalcredito; ?></h5></td>
			  	<td colspan="2"><h5>DEDUSIBLES<br>$<?php echo $totaldeducible; ?></h5></td>
			  	<td><h5>TOTAL:</h5></td>
			  	<td><h5>MOVIMIETOS<br><?php echo $totalClientes;?></h5></td>
			  	<td></td>
			</tr>
		<?php
		}else{
		  echo "<center><b><h5>No se encontraron cortes para estas fechas</h5></b></center>";
		}
		mysqli_close($conn);
		?> 	
	</tbody>
</table><br><br>