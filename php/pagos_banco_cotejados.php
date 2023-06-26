<?php
session_start();
include('../php/conexion.php');
$fromDateCot = $conn->real_escape_string($_POST['fechaInicio']);
$toDateCot = $conn->real_escape_string($_POST['fechaFinal']);
$banco = $conn->real_escape_string($_POST['banco']);


$sentencia = "SELECT c.id_pago, r.descripcion AS referencia, r.banco, c.id_user, p.fecha, c.fecha_cotejamiento
              FROM cotejo_pagos c
              JOIN pagos p ON p.id_pago = c.id_pago
              JOIN referencias r ON r.id_pago = c.id_pago
              WHERE r.banco = '$banco' AND p.fecha BETWEEN '$fromDateCot' AND '$toDateCot'";


  $cotejados = mysqli_query($conn, $sentencia);
  //$result = $pagos->fetch_all(MYSQLI_ASSOC);


?>

<div>

  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>Id Pago</th>
        <th>Referencia</th>
        <th>Banco</th>
        <th>Usuario</th>
        <th>Fecha Pago</th>
        <th>Fecha Cotejo</th>
      </tr>
    </thead>
    <tbody>

    
<?php

$cot = $cotejados->fetch_all(MYSQLI_ASSOC);
 
if($cot != null){

  foreach ($cot as $c) {
    echo '<tr>
      
    <td>'.$c["id_pago"].'</td>
    <td>'.$c["referencia"].'</td>
    <td>'.$c["banco"].'</td>
    <td>'.$c["id_user"].'</td>
    <td>'.$c["fecha"].'</td>
    <td>'.$c["fecha_cotejamiento"].'</td>

    </tr>';
  }


}else{
  echo "<center><b><h5>No se han cotejado pagos de banco ".$banco." en este rango de fechas.</h5></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>