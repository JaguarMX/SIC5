<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);

if ($Usuario != "" AND $Tipo == "") {
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$Usuario'"));
    $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND liquidada = 0"));
    $sql_deuda = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND liquidada = 0 ORDER BY id_deuda DESC");
    $head = $usuario['firstname'].' '.$usuario['lastname'].':  .  TOTAL = $'.$total['precio'];
}elseif ($Tipo != "" AND $Usuario == "") {
    $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND tipo_cambio='$Tipo' AND liquidada = 0"));
    $head = $Tipo.':  .  TOTAL = $'.$total['precio'];
    $sql_deuda = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND tipo_cambio = '$Tipo' AND liquidada = 0 ORDER BY id_deuda DESC");
}else{
    $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS deudas FROM pagos WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND tipo_cambio='$Tipo' AND liquidada = 0"));
    $head = $Tipo.':  .  TOTAL = $'.$total['precio'];
    $sql_deuda = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND tipo_cambio = '$Tipo' AND liquidada = 0 ORDER BY id_deuda DESC");
   
}


?>

<div>

<h4 class="blue-text"><?php echo $head;?></h4><br>
  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#Cliente</th>
        <th>Cliente</th>
        <th>Cantidad</th>
        <th>Abono</th>
        <th>Resta</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <?php
        if ($Usuario != "" AND $Tipo == "") {
        ?>
        <th>Cambio</th>
        <?php
        }elseif ($Tipo == 'Banco'  OR $Tipo == 'SAN') {
        ?>
        <th>Referencia</th>        
        <th>Registró</th>
        <?php
        }else{
        ?>
        <th>Registró</th>
        <?php
        }
        ?>
      </tr>
    </thead>
    <tbody>
<?php
$aux = mysqli_num_rows($sql_deuda);
if($aux>0){
    $sql = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0 AND usuario = $Usuario");
	$filas =  mysqli_num_rows($sql);
	if ($filas <= 0) {
	    echo "<center><b><h3>No se encontraron deudas</h3></b></center>";
	}else{
	    date_default_timezone_set('America/Mexico_City');
		$Fecha_Hoy = date('Y-m-d');
		$cont=0;
		$total = 0;
		while ( $resultados = mysqli_fetch_array($sql)) {
			$id_cliente = $resultados['id_cliente'];
			$deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $id_cliente AND liquidada = 1"));
			$abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Abono'"));
			if ($deuda['suma'] == "") {
			    $deuda['suma'] = 0;
			}
			if ($abono['suma'] == "") {
				$abono['suma'] = 0;
			}
			$poner = mysqli_fetch_array(mysqli_query($conn, "SELECT min(id_deuda) AS id FROM deudas WHERE id_cliente = $id_cliente AND liquidada = 0"));
			$tiene = $abono['suma']-$deuda['suma'];
			$cosnulta = mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente");
			if (mysqli_num_rows($cosnulta)<=0) {
				$cosnulta = mysqli_query($conn,"SELECT * FROM especiales WHERE id_cliente=$id_cliente");
			} 
			$cliente = mysqli_fetch_array($cosnulta);
			$id_comunidad = $cliente['lugar'];
			$Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));	
			$id_usuario = $resultados['usuario'];
			$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));
			$Mas_mes = strtotime('+1 month', strtotime($resultados['fecha_deuda']));
			$Mas_Mes = date('Y-m-d', $Mas_mes);
			$color = "green";
			$estatus = "";
			if ($Fecha_Hoy >= $Mas_Mes) {
				$color = "red accent-4";
				$estatus = "Cobrar";
			}
			$cont++;
			$cantidad = $resultados['cantidad'];
			if ($cantidad =='') {
				$cantidad= 0;
			}
			if ($poner['id'] == $resultados['id_deuda']) {
				$tiene = $tiene;
			}else{
				$tiene = 0;
			}
			$resta = $cantidad-$tiene;
            ?>
            <tr>
                <td><b><?php echo $id_cliente;?></b></td>
                <td><?php echo $cliente['nombre'];?></td>
                <td>$<?php echo $resultados['cantidad'];?></td>
                <td>$<?php echo $tiene; ?></td>
                <td>$<?php echo $resta; ?></td>
                <td><?php echo $resultados['tipo'];?></td>
                <td><?php echo $resultados['descripcion'];?></td>
                <td><?php echo $resultados['fecha_deuda'];?></td>
                <?php
                if ($Usuario != "" AND $Tipo == "") {
                    ?>
                    <td><?php echo $resultados['tipo'];?><br><?php if ($resultados['tipo'] == 'Banco' OR $resultados['tipo'] == 'SAN') { echo $refe; } ?></td>
                    <?php
                }elseif ($Tipo == 'Banco' OR $Tipo == 'SAN') {
                    ?>
                    <td><?php echo $refe;?></td>
                    <td><?php echo $usuario['firstname'];?></td>
                    <?php
                }else{
                    ?>
                    <td><?php echo $usuario['firstname'];?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
            $aux--;
        }
    }
}else{
  echo "<center><b><h5>No hay deudas registradas en esta fecha</h5></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>