<!DOCTYPE html>
<html>
	<head>
		<title>SIC | Cajas</title>
		<?php
		include ('fredyNav.php');
		include ('../php/superAdmin.php');
		?>
	</head>
	<?php
	if (in_array($_SESSION['user_id'], array(10, 49, 101)) == false) {
	  ?>
	  <script>    
	    function regresacortes() {
	      M.toast({html: "NO TIENES ACCESO!...", classes: "rounded"});
	      setTimeout("location.href='home.php'", 1000);
	    };
	    regresacortes();
	  </script>
	<?php }  ?>
	<body>
		<div class="container">
			<div class="row">
				<h3 class="hide-on-med-and-down">Saldo en Cajas:</h3>
	  			<h5 class="hide-on-large-only">Saldo en Cajas:</h5>
			</div>
			<table class="bordered highlight responsive-table" width="100%">
				<thead>
					<tr>
					  <th colspan="2"></th>
					  <th colspan="2">En Caja</th>
					  <th colspan="2"></th>
					</tr>
					<tr>
					  <th>Nombre</th>
					  <th>Apellidos</th>
					  <th>Corte</th>
					  <th>Pendiente</th>
					  <th>Banco</th>
					  <th>Credito</th>
					</tr>
				</thead>
				<tbody>
				<?php 
		          $sql_tmp = mysqli_query($conn,"SELECT * FROM users WHERE estatus = 1");
		          $columnas = mysqli_num_rows($sql_tmp);
		          if($columnas == 0){
		            echo '<h5 class="center">No se encontraron Usuarios</h5>';
		          }else{
		            $AllEfectivo = 0;  $AllBanco = 0;  $AllPendiente = 0;  $AllCredito = 0;

	             	while($tmp = mysqli_fetch_array($sql_tmp)){
	                	$id_user = $tmp['user_id'];
						$efectivo = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'"));					
						$banco = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
						$credito = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'"));
						// SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
						$deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $id_user"));
						$abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_user AND tipo = 'Abono Corte'"));
						//COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
						if ($deuda['suma'] == "") {
							$deuda['suma'] = 0;
						}elseif ($abono['suma'] == "") {
							$abono['suma'] = 0;
						}
						//SE RESTAN DEUDAS DE ABONOS
						$Saldo = $deuda['suma']-$abono['suma'];
						$Efectivo = $efectivo['suma']; 
						$Banco = $banco['suma']; 
						$Credito = $credito['suma']; 
						if ($Efectivo =='') {	$Efectivo= 0;	}
						if ($Banco =='') {	$Banco= 0;		}
						if ($Credito =='') {	$Credito= 0;	}					
	                	?>
						<tr>
							<td><?php echo $tmp['firstname']; ?></td>
							<td><?php echo $tmp['lastname']; ?></td>
							<td>$<?php echo sprintf('%.2f', $Efectivo); ?></td>
							<td>$<?php echo sprintf('%.2f', $Saldo); ?></td>
							<td>$<?php echo sprintf('%.2f', $Banco); ?></td>	
							<td>$<?php echo sprintf('%.2f', $Credito); ?></td>	
						</tr>
						<?php
						$AllEfectivo += $Efectivo;
						$AllBanco += $Banco;
						$AllPendiente += $Saldo;
						$AllCredito += $Credito;
	              	}//FIN WHILE
	              }//FIN ELSE ?>
					<tr>
						<td></td>
						<td><h5>TOTAL:</h5></td>
						<td><h5>$<?php echo sprintf('%.2f', $AllEfectivo); ?></h5></td>
						<td><h5>$<?php echo sprintf('%.2f', $AllPendiente); ?></h5></td>
						<td><h5>$<?php echo sprintf('%.2f', $AllBanco); ?></h5></td>
						<td><h5>$<?php echo sprintf('%.2f', $AllCredito) ?></h5></td>
					</tr>
			    </tbody>				
			</table>
		</div>
	</body>
</html>