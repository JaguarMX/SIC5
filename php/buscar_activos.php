<?php
include('../php/conexion.php');

$Id_Comunidad = $conn->real_escape_string($_POST['comunidad']);

$sql = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad' AND servicio != 'Telefonia'");
if ($Id_Comunidad == 'Todos') {
  $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE servicio != 'Telefonia'");
} 
//SE VERIFICA SI ENCUENTRA CLIENTES PARA CONTINUAR Y MOSTRAR
if (mysqli_num_rows($sql) == 0) {
  echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
} else {
  $corte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM int_cortes ORDER BY id DESC LIMIT 1"));
  $Ultimo_Corte = $corte['fecha'];
  
  if ($Id_Comunidad == 'Todos') {
    //BUSCAMOS TODOS LOS CLIENTES
    //BUSCAMOS LOS QUE TIENEN FECHA DE CORTE ADELANTADA
    $sql_activos = mysqli_query($conn, "SELECT * FROM clientes WHERE  fecha_corte >= '$Ultimo_Corte' AND servicio != 'Telefonia'");
    //BUSCAMOS LOS QUE TIENEN FECHA DE CORTE ATRASTADA
    $sql_inactivos = mysqli_query($conn, "SELECT * FROM clientes WHERE  fecha_corte < '$Ultimo_Corte' AND servicio != 'Telefonia'");
  } else{
    //BUSCAMOS SOLO LOS DE LA COMUNIDAD ELEGIDA
    //BUSCAMOS LOS QUE TIENEN FECHA DE CORTE ADELANTADA
    $sql_activos = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad' AND fecha_corte >= '$Ultimo_Corte' AND servicio != 'Telefonia'");
    //BUSCAMOS LOS QUE TIENEN FECHA DE CORTE ATRASTADA
    $sql_inactivos = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad' AND fecha_corte < '$Ultimo_Corte' AND servicio != 'Telefonia'");
  }
  $Total_Clientes = mysqli_num_rows($sql);
  $Total_Activos = mysqli_num_rows($sql_activos);
  $Total_Inactivos = mysqli_num_rows($sql_inactivos);
  echo "ULTIMO CORTRE DE INTERNET: ".$Ultimo_Corte;
?>
<div class="row">
  <div class="col s12 m6 l6">   
    <h4 class="hide-on-med-and-down indigo-text"><b>ACTIVOS: (<?php echo $Total_Activos.'/'.$Total_Clientes; ?> = <?php echo sprintf('%.2f', (($Total_Activos*100)/$Total_Clientes)); ?>% )</b></h4>  
    <h5 class="hide-on-large-only indigo-text"><b>ACTIVOS: (<?php echo $Total_Activos.'/'.$Total_Clientes; ?> = <?php echo sprintf('%.2f', (($Total_Activos*100)/$Total_Clientes)); ?>% )</b></h5>
    <table class="border highlight">
      <thead>
        <tr>
          <th>No.</th>
          <th>ID</th>
          <th>Cliente</th>
          <th>Comunidad</th>
          <th>IP</th>
          <th>Fecha Corte</th>
        </tr>
      </thead>
      <tbody>
        <?php    
        if ($Total_Activos == 0) {
          echo "<h5><b>No se encontraron clientes activos</b></h5>";
        } else {
          $aux = 0;
          while ($cliente = mysqli_fetch_array($sql_activos)) {
            $id_comunidad = $cliente['lugar'];
            $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
            $aux ++;   
            ?>
            <tr>
              <td><?php echo $aux; ?></span></td>
              <td><b><?php echo $cliente['id_cliente']; ?></b><span class="new badge green" data-badge-caption=""></span></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $comunidad['nombre']; ?></td>
              <td><?php echo $cliente['ip']; ?></td>
              <td><?php echo $cliente['fecha_corte']; ?></td>
            </tr>
          <?php
          }
        }
        ?>
      </tbody>
    </table>  
  </div>
  <!-- MITAD DE LA PANTALLA -->
  <div class="col s12 m6 l6">
    <h4 class="hide-on-med-and-down indigo-text"><b>INACTIVOS: (<?php echo $Total_Inactivos.'/'.$Total_Clientes; ?> = <?php echo sprintf('%.2f', (($Total_Inactivos*100)/$Total_Clientes)); ?>%)</b></h4>  
    <h5 class="hide-on-large-only indigo-text"><b>INACTIVOS: (<?php echo $Total_Inactivos.'/'.$Total_Clientes; ?> = <?php echo sprintf('%.2f', (($Total_Inactivos*100)/$Total_Clientes)); ?>%)</b></h5>
    <table class="border highlight">
      <thead>
        <tr>
        <th>No.</th>
        <th>ID</th>
        <th>Cliente</th>
        <th>Comunidad</th>
        <th>IP</th>
        <th>Fecha Corte</th>
      </tr>
    </thead>
    <tbody>
      <?php    
      if ($Total_Inactivos == 0) {
        echo "<h5><b>No se encontraron clientes inactivos</b></h5>";
      } else {
        $aux = 0;
        while ($cliente = mysqli_fetch_array($sql_inactivos)) {
          $id_comunidad = $cliente['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
          $aux ++;   
          ?>
          <tr>
            <td><?php echo $aux; ?></span></td>
            <td><b><?php echo $cliente['id_cliente']; ?></b><span class="new badge red" data-badge-caption=""></span></td>
            <td><?php echo $cliente['nombre']; ?></td>
            <td><?php echo $comunidad['nombre']; ?></td>
            <td><?php echo $cliente['ip']; ?></td>
            <td><?php echo $cliente['fecha_corte']; ?></td>
          </tr>
        <?php
        }
      }
      ?>
    </tbody>
  </table>
  </div>  
</div>
<?php } ?>