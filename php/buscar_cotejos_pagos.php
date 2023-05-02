<?php
session_start();
include('../php/conexion.php');
$fromDateCot = $conn->real_escape_string($_POST['fechaInicio']);
$toDateCot = $conn->real_escape_string($_POST['fechaFinal']);
$banco = $conn->real_escape_string($_POST['banco']);
$accion = $conn->real_escape_string($_POST['accion']);
$idPago =$conn->real_escape_string($_POST['idPago']);



if ($accion == 'cotejar') {
  $hoy = date('Y-m-d'); 
  $usuario =$_SESSION['user_name'];
  $insert = "INSERT INTO cotejo_pagos(id_pago, id_user, fecha_cotejamiento) VALUES ($idPago,'$usuario','$hoy')";
  $insertar = mysqli_query($conn, $insert);
  echo '<script>M.toast({html:"Se cotejo el pago", classes: "rounded"})</script>';
}

$sentenciaFechas="SELECT r.id_pago, r.descripcion, p.fecha, r.banco FROM referencias r
            JOIN pagos p ON p.id_pago = r.id_pago
            WHERE r.banco = '$banco' AND p.fecha BETWEEN '$fromDateCot' AND '$toDateCot' AND NOT EXISTS(SELECT id_pago FROM cotejo_pagos c WHERE c.id_pago = r.id_pago);";


  $pagos = mysqli_query($conn, $sentenciaFechas);

?>

<div>

  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>Id Pago</th>
        <th>Referencia</th>
        <th>Fecha</th>
        <th>Banco</th>
        <th class="center">Cotejar</th>
      </tr>
    </thead>
    <tbody>

    
<?php

$rows = $pagos->fetch_all(MYSQLI_ASSOC);
$cotejar = 'cotejar';
 
if($rows != null){

  foreach ($rows as $r) {
    echo '<tr>
      
    <td>'.$r["id_pago"].'</td>
    <td>'.$r["descripcion"].'</td>
    <td>'.$r["fecha"].'</td>
    <td>'.$r["banco"].'</td>
    <td class="center"><button id='.$r['id_pago'].' class="btn waves-light waves-effect center pink" onclick="buscar_pagos_cotejo('."'cotejar'".','.$r['id_pago'].');"><i class="material-icons prefix right">check</i> Cotejar</button></td>

    </tr>';
  }


}else{
  echo "<center><b><h5>No hay pagos registrados en ".$banco." en este rango de fechas</h5></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>