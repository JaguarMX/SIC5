<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);

?>

<h3 class="hide-on-med-and-down">Reporte de deducibles</h3>
<h5 class="hide-on-large-only">Reporte de deducibles</h5>

<table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th>Id Corte</th>
	        <th>Cantidad</th>
	        <th>Descripcion</th>
	        <th>Fecha</th>
	        <th>Entregado por</th>
			<th>Detalle de corte</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$resultado_deducibles = mysqli_query($conn, "SELECT * FROM deducibles WHERE fecha>='$ValorDe' AND fecha<='$ValorA' ORDER BY id_corte DESC");
	$aux = mysqli_num_rows($resultado_deducibles);
	if($aux>0){
	$total = 0;
	$totalClientes= 0;
	$totalbanco = 0;
	$totalcredito = 0;
	$totaldeducible = 0;
	while($deducibles = mysqli_fetch_array($resultado_deducibles)){
		$id_corte =$deducibles['id_corte'];
		$idUsuario = $deducibles['usuario'];
		$deducibleCantidad =$deducibles['cantidad'];
		$deducibleDescripcion =$deducibles['descripcion'];
		$deducibleFecha =$deducibles['fecha'];
		$idDeducible =$deducibles['id'];
		$total += $deducibles['cantidad'];
		$sql =  mysqli_query($conn,  "SELECT * FROM users WHERE user_id = $idUsuario");
		$usuarioDeducible = mysqli_fetch_array($sql);
	  ?>
	  <tr>
	    <td><b><?php echo $id_corte;?></b></td>
	    <td>$<?php echo $deducibleCantidad;?></td>
	    <td><?php echo $deducibleDescripcion;?></td>
	    <td><?php echo $deducibleFecha;?></td>
	    <td><?php echo $usuarioDeducible['firstname'];?></td>
	    <td><form method="post" action="../views/detalle_corte.php"><input id="id_corte" name="id_corte" type="hidden" value="<?php echo $deducibles['id_corte']; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
	  </tr>
	  <?php
	  
	  $aux--;
	}
	?>
	  <tr>
	  	<td><h5>TOTAL:</h5></td>
	  	<td><h5>$<?php echo number_format($total, 2, '.', ','); ?></h5></td>
	  
	  </tr>
	<?php
	}else{
	  echo "<center><b><h5>Este usuario aún no ha registrado cortes</h5></b></center>";
	}
	?>
	<?php 
	mysqli_close($conn);
	?> 
		
	</tbody>
</table><br><br>