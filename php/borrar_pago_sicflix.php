<?php
session_start();
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('../php/superAdmin.php');
$IdPago = $conn->real_escape_string($_POST['valorIdPago']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Motivo = $conn->real_escape_string($_POST['valorMotivo']);

$fecha_corte = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM clientes WHERE id_cliente='.$IdCliente));

$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']!="Administrador"){
    echo "<script >M.toast({html: 'Sólo un administrador puede borrar pagos.', classes: 'rounded'});</script>";
}else{
    $Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago=$IdPago"));
    if ($Pago['tipo_cambio'] == 'Credito') {
        $Id_deuda = $Pago['id_deuda'];
        if (mysqli_query($conn, "DELETE FROM deudas WHERE id_deuda = '$Id_deuda'")) {
            echo '<script >M.toast({html:"Deuda Borrada.", classes: "rounded"})</script>';   
        }
    }
    $INFO_Pgo =  mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM pagos WHERE id_pago = $IdPago"));
    $Descripcion = $INFO_Pgo['tipo'].' :'.$INFO_Pgo['descripcion'];
    $Cantidad = $INFO_Pgo['cantidad'];
    $Cliente = $INFO_Pgo['id_cliente'];
    $Realizo = $INFO_Pgo['id_user'];
    $Tipo_Cambio = $INFO_Pgo['tipo_cambio'];
    $Fecha_registro =$INFO_Pgo['fecha'].' '.$INFO_Pgo['hora'];
    $HOY = date('Y-m-d');
  
    if(mysqli_query($conn, "INSERT INTO pagos_borrados(cliente, cantidad, descripcion, realizo, tipo_cambio, fecha_hora_registro, motivo, borro, fecha_borrado) VALUES ($Cliente,'$Cantidad', '$Descripcion', $Realizo, '$Tipo_Cambio', '$Fecha_registro', '$Motivo', $id, '$HOY')")){

        if(mysqli_query($conn, "DELETE FROM pagos WHERE id_pago = '$IdPago'")){
            echo '<script >M.toast({html:"Pago Borrado.", classes: "rounded"})</script>'; 
            if ($Tipo == 'SICFLIX') {
                $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'SICFLIX' ORDER BY id_pago DESC LIMIT 1"));
                //SI HAY PAGOS ANTERIORES ENTONCES TOMA LA FECHA DE CORTE SICFLIX DEL ULTIMO PAGO Y LA REEMPLAZA
                if(count($ultimoPago) != 0){
                    $porciones = explode(" ", $ultimoPago['descripcion']);
                    $Mes = $porciones[0];
                    $Año = $porciones[1];
                    #ARREGLO EL CUAL DEFINE EL MES SIGUIENTE PARA LA FECHA DE CORTE DEL CLIENTE
                    $array =  array('ENERO' => '02','FEBRERO' => '03', 'MARZO' => '04','ABRIL' => '05', 'MAYO' => '06', 'JUNIO' => '07', 'JULIO' => '08', 'AGOSTO' => '09', 'SEPTIEMBRE' => '10', 'OCTUBRE' => '11', 'NOVIEMBRE' => '12',  'DICIEMBRE' => '01');      
                    $N_Mes = $array[$Mes];
                    #COMO ES DICIEMBRE ADELANTAMOS UN AÑO PORQUE LA FECHA DE CORTE YA ES EL SIGUINETE AÑO PAGO TODO DICIEMBRE
                    if ($Mes == 'DICIEMBRE') {  $Año ++; }
                    #FECHA DE CORTE SEGUN EL MES Y AÑO SELECCIONADO
                    $FechaCorte = date($Año.'-'.$N_Mes.'-05'); 
                    mysqli_query($conn, "UPDATE clientes SET fecha_corte_sicflix='$FechaCorte' WHERE id_cliente='$IdCliente'");
                }else{
                    //SI !NO¡ HAY PAGOS ANTERIORES ENTONCES REEMPLAZA EL CAMPO CON UN DATO VACIO
                    $FechaCorte = date('2000-01-01');
                    mysqli_query($conn, "UPDATE clientes SET fecha_corte_sicflix='$FechaCorte' WHERE id_cliente='$IdCliente'");
                }
            }
        }else{
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});</script>";
        } 
    }else{
        echo "<script >M.toast({html: 'Ha ocurrido un error al insertar.', classes: 'rounded'});</script>";
    } 
}
?>
<div id="mostrar_pagos">
    <table class="bordered highlight">
    <thead>
      <tr>
        <th>#</th>
        <th>Cantidad</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Imprimir</th>
        <th>Borrar</th>
      </tr>
    </thead>
    <tbody>
<?php
if($Tipo == 'Telefono'){
  $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = '$IdCliente' AND tipo IN ('Min-extra', 'Mes-Tel')  ORDER BY id_pago DESC";
}else{
  $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = '$IdCliente' AND tipo = '$Tipo' ORDER BY id_pago DESC";
}
$resultado_pagos = mysqli_query($conn, $sql_pagos);
$aux = mysqli_num_rows($resultado_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($resultado_pagos)){
  $id_user = $pagos['id_user'];
  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
  ?>
  <tr>
    <td><b><?php echo $aux;?></b></td>
    <td>$<?php echo $pagos['cantidad'];?></td>
    <td><?php echo $pagos['tipo'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $user['user_name'];?></td>
    <td><?php echo $pagos['fecha'];?></td>
    <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
    <td><a onclick="verificar_eliminar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-4 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>