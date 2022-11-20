<html>
<head>
	<title>SIC | Reportes Sicflix</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');
$sicflix = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE servicio IN ('Telefonia', 'Internet y Telefonia') AND tel_cortado = 0 AND corte_tel < '$Fecha_hoy'"));
//$tel = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE servicio IN ('Telefonia', 'Internet y Telefonia') AND tel_cortado = 0 AND corte_tel < '$Fecha_hoy'"));
?>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Sicflix</h3>
    <h5 class="hide-on-large-only">Sicflix</h5>
    <a class="waves-effect waves-light btn pink right" href="cortes_telefono.php">Cortes Telefono: <b class="black-text"><?php echo $tel['count(*)'];?></b><i class="material-icons left">phone_locked</i></a>
  </div>
  <div class="row"><br>
    <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">DAR DE ALTA / ACTIVAR</a></li>
          <li class="tab col s6"><a class="black-text" href="#test-swipe-2">DAR DE BAJA / DESACTIVAR</a></li>
        </ul>
      </div><br><br><br><br>
      <?php
        $filasBaja = '';
        $filasAlta = '';
        //Aquí se declara una variable para tomar la informacion de la tabla reporte_sicflix
        $sql = "SELECT * FROM reporte_sicflix";
        $consulta = mysqli_query($conn, $sql);
        //Obtiene la cantidad de filas que hay en la consulta
        $filas = mysqli_num_rows($consulta);
        //Si no existe ninguna fila que sea igual a $consulta, entonces mostramos el siguiente mensaje
        if ($filas == 0) {
          echo '<script>M.toast({html:"No se encontraron clientes para dar de alta.", classes: "rounded"})</script>';
        }else {          
          //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
          while($resultados = mysqli_fetch_array($consulta)) {
            $id_cliente = $resultados['cliente'];
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
            // Sí es igual que se ponga azul, sí es menor que se ponga rojo y se es mayor que se ponga verde
            if ($cliente['fecha_corte_sicflix'] == $Fecha_hoy) {
              $color = 'indigo';
            }else if ($cliente['fecha_corte_sicflix'] < $Fecha_hoy) {
              $color = 'red darken-2';
            }else{
              $color = 'green';
            }
            if ($cliente['fecha_corte_sicflix'] <= $Fecha_hoy) {
              $filasAlta .= '
                <tr>
                  <td><span class="new badge '.$color.'" data-badge-caption=""></span></td>
                  <td>'.$resultados['id'].'</td>
                  <td>'.$cliente['nombre'].'</td>
                  <td>'.$resultados['descripcion'].'</td>
                  <td>'.$resultados['paquete'].'</td>
                  <td>'.$resultados['fecha_registro'].'</td>
                  <td>'.$resultados['registro'].'</td>
                  <td><br><form action="cotejo_tel.php" method="post"><input type="hidden" name="id_reporte_sicflix" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                </tr>';
            }else{
              $filasBaja .= '
                <tr>
                  <td><span class="new badge '.$color.'" data-badge-caption=""></span></td>
                  <td>'.$resultados['id'].'</td>
                  <td>'.$cliente['nombre'].'</td>
                  <td>'.$resultados['descripcion'].'</td>
                  <td>'.$resultados['paquete'].'</td>>
                  <td>'.$resultados['fecha_registro'].'</td>
                  <td>'.$resultados['registro'].'</td>
                  <td><br><form action="cotejo_tel.php" method="post"><input type="hidden" name="id_reporte_sicflix" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                </tr>';
            }          
          }//Fin while $resultados
        } //Fin else $filas
      ?>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
      <div  id="test-swipe-1" class="col s12">
        <div class="row">
          <p><div>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>Estatus</th>
                  <th># No.Rep</th>
                  <th>Cliente</th>
                  <th>Descripción</th>
                  <th>Paquete</th>
                  <th>Fecha Registro</th>
                  <th>Registro</th>            
                  <th>Atender</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $filasAlta; ?>
              </tbody>
            </table>
          </div></p>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12">
        <div class="row">
          <p><div>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>Estatus</th>
                  <th>#</th>
                  <th>Cliente</th>
                  <th>Tipo</th>
                  <th>Cotejar</th>            
                  <th>Atender</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $filasBaja; ?>
              </tbody>
            </table>
          </div></p>
        </div>
      </div>
  </div>
  </div>
</div><br>
</body>
</main>
</html>