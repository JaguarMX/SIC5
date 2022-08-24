<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);

$sql_pagos = mysqli_query($conn, "SELECT * FROM pagos_borrados ORDER BY id DESC");
?>
<div class="row">
    <h4 class="blue-text">Total de Pagos Borrados:</h4><br>
    <table class="bordered highlight responsive-table">
      <thead>
        <tr>
          <th>N°</th>
          <th>Cliente</th>
          <th>Cantidad</th>
          <th>Tipo Cambio</th>
          <th>Descripción</th>
          <th>Fecha Hora Registro</th>
          <th>Registro</th>
          <th>Motivo Borrado</th>
          <th>Borro</th>
          <th>Fecha Borrado</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $aux = mysqli_num_rows($sql_pagos);
      if($aux>0){
        while($pagos = mysqli_fetch_array($sql_pagos)){
          $id_cliente = $pagos['cliente'];
          if ($id_cliente == 0) {
            $Nombre = 'N/A';
          }else{
            $cliente= mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente"));
            $Nombre = $cliente['nombre'];
          }
          $registro = $pagos['realizo'];
          $usuario_r = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$registro'"));
          $borro = $pagos['borro'];
          $usuario_b = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$borro'"));
          ?>
          <tr>
            <td><b><?php echo $id_cliente;?></b></td>
            <td><?php echo $Nombre;?></td>
            <td>$<?php echo $pagos['cantidad'];?></td>
            <td><?php echo $pagos['tipo_cambio'];?></td>
            <td><?php echo $pagos['descripcion'];?></td>
            <td><?php echo $pagos['fecha_hora_registro'];?></td>
            <td><?php echo $usuario_r['firstname'];?></td>
            <td><?php echo $pagos['motivo'];?></td>
            <td><?php echo $usuario_b['firstname'];?></td>
            <td><?php echo $pagos['fecha_borrado'];?></td>
          </tr>
          <?php
        }
      }else{
        echo "<center><b><h5>No hay pagos registrados en esta fecha</h5></b></center>";
      } 
      mysqli_close($conn);
      ?>        
      </tbody>
    </table>
</div><br>