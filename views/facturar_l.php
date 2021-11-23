<html>
<head>
	<title>SIC | Facturados</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
?>
<!--Termina script dispositivos-->
</head>
<main>
<body>
<div class="container">
  <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m9 l9">Facturados: </h3>
      <h5 class="hide-on-large-only col s12 m9 l9">Facturados: </h5>   
  </div>
  <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">Pago Pendiente</a></li>
          <li class="tab col s6"><a class="black-text" href="#test-swipe-2">Liquidada</a></li>
        </ul>
      </div><br><br><br><br>

      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
      <div  id="test-swipe-1" class="col s12">
        <div class="row">
          <p><div>
            <table class="bordered  highlight responsive-table">
              <thead>
                <tr>
                    <th>#Orden</th>
                    <th>Cliente</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Comunidad</th>
                    <th>Técnico</th>
                    <th>Registró</th>
                    <th>Estatus</th>
                    <th>Liquidada</th>
                    <th>Atender</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus = 'Facturado' AND  liquidada != 1 ORDER BY fecha");
              //Obtiene la cantidad de filas que hay en la sql_orden
              $filas = mysqli_num_rows($sql_orden);
              //Si no existe ninguna fila que sea igual a $sql_ordenBusqueda, entonces mostramos el siguiente mensaje
              if ($filas <= 0) {
                echo '<script>M.toast({html:"No se encontraron ordenes de servico.", classes: "rounded"})</script>';
              } else {
                while($resultados = mysqli_fetch_array($sql_orden)) {
                  $id_cliente = $resultados['id_cliente'];
                  $id_user=$resultados['registro'];
                  $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente"));
                  $id_comunidad = $cliente['lugar'];
                  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                  $Descripción = $resultados['trabajo'];
                  $Tecnicos = $resultados['tecnicos_s'];
                  if ($Tecnicos == '') {
                    $Tecnicos = 'SIN';
                  }
                  $liqi = ($resultados['liquidada']==1)? '<span class="new badge green" data-badge-caption=""><b>Pagada</b></span>': '<span class="new badge red" data-badge-caption=""><b>Pendiente</b></span>';
                  echo '
                      <tr>
                        <td><b>'.$resultados['id'].'</b></td>
                        <td>'.$cliente['nombre'].'</td>
                        <td>'.$Descripción.'</td>
                        <td>'.$resultados['fecha_f'].'</td>
                        <td>'.$comunidad['nombre'].'</td>
                        <td>'.$Tecnicos.'</td>
                        <td>'.$users['firstname'].'</td>
                        <td><b>'.$resultados['estatus'].'</b></td>
                        <td>'.$liqi.'</td>
                        <td><br><form action="atender_factura.php" method="post"><input type="hidden" name="id_orden" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                      </tr>';
                }
              }
              ?>
              </tbody>
            </table>
          </div></p>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12">
        <div class="row">
          <p><div>
            <table class="bordered  highlight responsive-table">
              <thead>
                <tr>
                    <th>#Orden</th>
                    <th>Cliente</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Comunidad</th>
                    <th>Técnico</th>
                    <th>Registró</th>
                    <th>Estatus</th>
                    <th>Liquidada</th>
                    <th>Atender</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus = 'Facturado' AND  liquidada = 1 ORDER BY fecha");
              //Obtiene la cantidad de filas que hay en la sql_orden
              $filas = mysqli_num_rows($sql_orden);
              //Si no existe ninguna fila que sea igual a $sql_ordenBusqueda, entonces mostramos el siguiente mensaje
              if ($filas <= 0) {
                echo '<script>M.toast({html:"No se encontraron ordenes de servico.", classes: "rounded"})</script>';
              } else {
                while($resultados = mysqli_fetch_array($sql_orden)) {
                  $id_cliente = $resultados['id_cliente'];
                  $id_user=$resultados['registro'];
                  $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente"));
                  $id_comunidad = $cliente['lugar'];
                  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                  $Descripción = $resultados['trabajo'];
                  $Tecnicos = $resultados['tecnicos_s'];
                  if ($Tecnicos == '') {
                    $Tecnicos = 'SIN';
                  }
                  $liqi = ($resultados['liquidada']==1)? '<span class="new badge green" data-badge-caption=""><b>Pagada</b></span>': '<span class="new badge red" data-badge-caption=""><b>Pendiente</b></span>';
                  echo '
                      <tr>
                        <td><b>'.$resultados['id'].'</b></td>
                        <td>'.$cliente['nombre'].'</td>
                        <td>'.$Descripción.'</td>
                        <td>'.$resultados['fecha_f'].'</td>
                        <td>'.$comunidad['nombre'].'</td>
                        <td>'.$Tecnicos.'</td>
                        <td>'.$users['firstname'].'</td>
                        <td><b>'.$resultados['estatus'].'</b></td>
                        <td>'.$liqi.'</td>
                        <td><br><form action="atender_factura.php" method="post"><input type="hidden" name="id_orden" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                      </tr>';
                }
              }
              ?>
              </tbody>
            </table>
          </div></p>
        </div>
      </div>
  </div>
<br><br>
</div>
</body>
</main>
</html>