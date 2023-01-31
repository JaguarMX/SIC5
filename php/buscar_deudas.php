<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);

if ($Usuario != 0) {
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$Usuario'"));
    $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND liquidada = 0"));
    $sql_deuda = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND usuario='$Usuario' AND liquidada = 0 ORDER BY id_deuda DESC");
    $head = $usuario['firstname'].' '.$usuario['lastname'].':  .  TOTAL = $'.$total['precio'];
}elseif ($Usuario == 0) {
    $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND liquidada = 0"));
    $head = 'TOTAL = $'.$total['precio'];
    $sql_deuda = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND liquidada = 0 ORDER BY id_deuda DESC");
}
?>
<div>
  <h4 class="blue-text"><?php echo $head;?></h4><br>
  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Estatus</th>
        <th>Id. Cliente</th>
        <th>Nombre Cliente</th>
        <th>Comunidad</th>
        <th>Descripción</th>    
        <th>Cantidad</th>
        <th>Abono</th>
        <th>Resta</th>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Ver</th>
      </tr>
    </thead>
    <tbody>
                        <?php
                        $filas =  mysqli_num_rows($sql_deuda);
                        if ($filas <= 0) {
                            echo "<center><b><h3>No se encontraron deudas</h3></b></center>";
                        }else{
                            date_default_timezone_set('America/Mexico_City');
                            $Fecha_Hoy = date('Y-m-d');
                            $cont=0;
                            $total = 0;
                            while ( $resultados = mysqli_fetch_array($sql_deuda)) {
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
                                    <td><?php echo $cont; ?></td>
                                    <td><span class="new badge <?php echo$color; ?>" data-badge-caption=""><?php echo $estatus; ?></span></td>
                                    <td><?php echo $id_cliente; ?></td>
                                    <td><?php echo $cliente['nombre']; ?></td>
                                    <td><?php echo $Comunidad['nombre']; ?></td>        
                                    <td><?php echo $resultados['descripcion']; ?></td>          
                                    <td>$<?php echo $cantidad; ?></td>                      
                                    <td>$<?php echo $tiene; ?></td>
                                    <td>$<?php echo $resta; ?></td>
                                    <td><?php echo $resultados['fecha_deuda']; ?></td>
                                    <td><?php echo $usuario['firstname']; ?></td>
                                <td><form method="post" action="../views/credito.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                                </tr>
                                <?php
                                $total += $resta;
                            }//FIN WHILE
                            ?>
                            <tr>
                                <td colspan="7">
                                <td><b>TOTAL:</b></td><td><b> $<?php echo $total; ?></b></td>
                                <td colspan="4"></td>
                            </tr>
                            <?php
                        }//FIN ELSE
                        ?>
        </tbody>
    </tbody>
  </table>
</div>