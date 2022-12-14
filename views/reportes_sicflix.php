<!-- SCRIPT PARA ACTIVAR EL MODAL -->
<script>
  function verificar_reporte(id) {

    $.post("../php/modal_desactivar_sicflix.php", {
      valorIDReporte: id,
    },function(mensaje) {
      $("#Continuar").html(mensaje);
    });   
  };
</script>
<!-- FIN SCRIPT MODAL-->
<html>
<head>
	<title>SIC | Reportes SICFLIX</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');
$id_user = $_SESSION['user_id'];
?>
</head>
<main>
<body>
  <div class="container" id="Continuar">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Atender reportes SICFLIX</h3>
      <h5 class="hide-on-large-only">Atender reportes SICFLIX</h5>
      <!-- <a class="waves-effect waves-light btn pink right" href="cortes_telefono.php">Cortes Telefono: <b class="black-text"><?php echo $tel['count(*)'];?></b><i class="material-icons left">phone_locked</i></a> -->
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
          //ORDENAMOS DEL MAS RECIENTE PRIMERO AL MAS ANTIGUO AL FINAL
          $sql = "SELECT * FROM reporte_sicflix ORDER BY id DESC";
          $consulta = mysqli_query($conn, $sql);
          //Obtiene la cantidad de filas que hay en la consulta
          $filas = mysqli_num_rows($consulta);
          //Si no existe ninguna fila que sea igual a $consulta, entonces mostramos el siguiente mensaje
          if ($filas == 0) {
            echo '<script>M.toast({html:"No se encontraron clientes para dar de alta.", classes: "rounded"})</script>';
          }else{

            //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            while($resultados = mysqli_fetch_array($consulta)) {
              $id_cliente = $resultados['cliente'];
              $id_reporte = $resultados['id'];
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
              $reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id=$id_reporte"));
              // SELECCIONAMOS EL ULTIMO REGISTRO PARA COMPROBAR CULA FUE LA ÚLTIMA OPRACIÓN Y HACER  Ó NO UN NUEVO REPORTE
              $ultimo_resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reporte_sicflix WHERE cliente = $id_cliente ORDER BY id DESC LIMIT 1"));
              
              // SE EJECUTA LA CONDICIÓN AL COMPROBAR LA FECHA DE CORTE SICFLIX PARA ACTIVAR UN NUEVO REPORTE DE DESACTIVACIÓN -->
              if($cliente['fecha_corte_sicflix'] < $Fecha_hoy AND $cliente['fecha_corte_sicflix'] != 0000-00-00 AND $cliente['fecha_corte_sicflix'] != date('2000-01-01')){
                // CONDICIÓN PARA EVITAR CICLAMINETOS
                if($resultados['estatus'] != 0 AND $ultimo_resultado['descripcion'] != 'Desactivar Sicflix' AND $ultimo_resultado['estatus'] != 0){
                  $IdCliente = $resultados['cliente'];
                  $Pass = $resultados['contraseña_sicflix'];
                  $Nombre_Usuario = $resultados['nombre_usuario_sicflix'];
                  $Descripcion = 'Desactivar Sicflix';
                  $Estaus = 0;
                  $Paquete = $resultados['paquete'];
                  $PrecioPaquete = $resultados['precio_paquete'];
                  $sql3 = "INSERT INTO `reporte_sicflix` (cliente, descripcion, estatus, paquete, precio_paquete, fecha_registro, registro, nombre_usuario_sicflix, contraseña_sicflix) VALUES ($IdCliente, '$Descripcion',$Estaus, '$Paquete', $PrecioPaquete, '$Fecha_hoy', $id_user, '$Nombre_Usuario', '$Pass')";
                  if(mysqli_query($conn, $sql3)){
                    ?>
                    <script>
                      var a = document.createElement("a");
                        a.href = "../views/reportes_sicflix.php";
                        a.click();
                    </script>
                    <?php
                  }else{
                    echo  '<script>M.toast({html:"Ha ocurrido un error con el insert del reporte de desactivación.", classes: "rounded"})</script>';	
                  }
                }
              }
              //BASICAMENTE SI LA FECHA DE CORTE SICFLIX ES MAYOR ES PORQUE YA PAGÓ, VAMOS A PONER LA CONDICION DE QUE SI LA FECHA DE CORTE ES MAYOR A LA FECHA DE HOY
              //ENTONCES VERIFICA SI EL SERVICIO ESTA ACTIVO, SI NO ENTONCES GENERAR UN REPORTE DE ACTIVACION
              if($cliente['fecha_corte_sicflix'] > $Fecha_hoy AND $cliente['sicflix'] < 1){
                // CONDICIÓN PARA EVITAR CICLAMINETOS
                if($resultados['estatus'] != 0 AND $ultimo_resultado['descripcion'] != 'Activar Sicflix' AND $ultimo_resultado['estatus'] != 0){
                  $IdCliente = $resultados['cliente'];
                  $Pass = $resultados['contraseña_sicflix'];
                  $Nombre_Usuario = $resultados['nombre_usuario_sicflix'];
                  $Descripcion = 'Activar Sicflix';
                  $Estaus = 0;
                  $Paquete = $resultados['paquete'];
                  $PrecioPaquete = $resultados['precio_paquete'];
                  $sql4 = "INSERT INTO `reporte_sicflix` (cliente, descripcion, estatus, paquete, precio_paquete, fecha_registro, registro, nombre_usuario_sicflix, contraseña_sicflix) VALUES ($IdCliente, '$Descripcion',$Estaus, '$Paquete', $PrecioPaquete, '$Fecha_hoy', $id_user, $Nombre_Usuario, '$Pass')";
                  if(mysqli_query($conn, $sql4)){
                    ?>
                    <script>
                      var a = document.createElement("a");
                        a.href = "../views/reportes_sicflix.php";
                        a.click();
                    </script>
                    <?php
                  }else{
                    echo  '<script>M.toast({html:"Ha ocurrido un error con el insert del reporte de desactivación.", classes: "rounded"})</script>';	
                  }
                }
              }//FIN   
            

              //EL COLOR DEPENDE DEL ESTATUS DEL REPORTE
              $color_reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id=$id_reporte"));
              // Sí es mayor o igual a 1 que se ponga verde, sí no que se ponga rojo
              if ($color_reporte['estatus'] > 0) {
                $color = 'green';
              }else{
                $color = 'red';
              }

              //VARIABLES OCULTAS PARA MANDAR AL MODAL
              ?>
              <form name="formMensualidad">
                <input id="nombres" name="nombres" type="hidden" value="<?php echo $Nombre ?>">
                <input id="telefono" name="telefono" type="hidden" value="<?php echo $Telefono ?>">
                <input id="direccion" name="direccion" type="hidden" value="<?php echo $Direccion ?>">
              </form>
              <?php

              if ($reporte['descripcion'] == 'Activar Sicflix') {
                $filasAlta .= '
                <tr>
                  <td><span class="new badge '.$color.'" data-badge-caption=""></span></td>
                  <td>'.$resultados['id'].'</td>
                  <td>'.$cliente['nombre'].'</td>
                  <td>'.$resultados['descripcion'].'</td>
                  <td>'.$resultados['paquete']."($".$resultados['precio_paquete'].")".'</td>
                  <td>'.$resultados['fecha_registro'].'</td>
                  <td>'.$resultados['registro'].'</td>
                  <td><br><form action="activar_sicflix.php" method="post"><input type="hidden" name="id_reporte_sicflix" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                </tr>';
              }else{
                $filasBaja .= '
                <tr>
                  <td><span class="new badge '.$color.'" data-badge-caption=""></span></td>
                  <td>'.$resultados['id'].'</td>
                  <td>'.$cliente['nombre'].'</td>
                  <td>'.$resultados['descripcion'].'</td>
                  <td>'.$resultados['paquete']."($".$resultados['precio_paquete'].")".'</td>
                  <td>'.$resultados['fecha_registro'].'</td>
                  <td>'.$resultados['registro'].'</td>
                  <td><a onclick="verificar_reporte('.$resultados['id'].')" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">send</i></a></td>
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
                  <?php echo $filasBaja; ?>
                </tbody>
              </table>
            </div></p>
          </div>
        </div>
    </div>
    </div>
  </div><br><!-- FIN DEL CONTAINER -->
</body>
</main>
</html>