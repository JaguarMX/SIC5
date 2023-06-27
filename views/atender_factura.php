<!DOCTYPE html>
<html>
<head>
	<title>SIC | Atender Orden</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_POST['id_orden']) == false) {
  ?>
  <script>
    M.toast({html: "Regresando a listado de ordenes.", classes: "rounded"});
    setTimeout("location.href='ordenes_servicio.php'", 1000);
  </script>
  <?php
}else{
  $id_orden = $_POST['id_orden'];
  $tecnico = $_SESSION['user_name'];
?>
<script>
  function imprimir(id_pago){
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/imprimir.php?IdPago="+id_pago;
            a.click();
      };
  function update_orden() {
      var textoLiquidar = $("input#liquidar").val();
      var textoIdOrden = $("input#id_orden").val();
      var textoIdCliente = $("input#id_cliente").val();

      if (textoLiquidar == 0) {
        var textoLiquidarS = $("input#liquidar_s").val();
        var textoRef = $("input#referencia_f").val();

        if(document.getElementById('banco').checked==true){
          textoTipoE = 'Banco';
        }else if (document.getElementById('san').checked == true) {
          textoTipoE = 'SAN';
        }else{
          textoTipoE = 'Efectivo';
        }
        if (textoLiquidarS == 0) {
          M.toast({html: 'La cantidad debe ser mayor a 0.', classes: 'rounded'});
        }else if ((document.getElementById('banco').checked == true || document.getElementById('san').checked == true) && textoRef == "") {
          M.toast({html: 'Los pagos en Banco y SAN deben de llevar una referencia.', classes: 'rounded'});
        }else{
          $.post("../php/update_orden_f.php", {
              valorIdOrden: textoIdOrden,
              valorLiquidarS: textoLiquidarS,
              valorIdCliente: textoIdCliente,
              valorRef: textoRef,
              valorTipoE: textoTipoE
          }, function(mensaje) {
              $("#resultado_update_orden").html(mensaje);
          });
        }
      }else{
        $.post("../php/update_orden_f.php", {
            valorIdOrden: textoIdOrden,
            valorIdCliente: textoIdCliente,
            valorLiquidarS: 0
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });
      }
  };
</script>
<body>
	<div class="container">
		<div class="row">
	      <h3 class="hide-on-med-and-down">Atender Factura:</h3>
	      <h5 class="hide-on-large-only">Atender Factura:</h5>
    	</div>
      <div id="resultado_update_orden"></div>
    <?php   
      $orden =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_orden"));
      $id_cliente = $orden['id_cliente'];
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
      $id_counidad = $datos['lugar'];
      $Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_counidad"));
    ?>
      <div class="row">
   		<ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Folio: </b><?php echo $id_orden;?></span>
              <p><b>Nombre: </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>                  
                <b>Comunidad: </b><?php echo $Comunidad['nombre'];?><br>
                <b>Referencia: </b><?php echo $datos['referencia'];?><br>                
                <b>Solicitud: </b><?php echo $orden['solicitud'];?><br>             
                <b>Trabajo: </b><?php echo $orden['trabajo'];?><br>                
                <b>Material: </b><?php echo $orden['material'];?><br> 
                <b>Solucion: </b><?php echo $orden['solucion'];?><br> 
                <b>Realizo: </b> ***Tecnico(s): <?php echo $orden['tecnicos_s'];?>***  -  ***Fecha: <?php echo $orden['fecha_s'];?>***<br> 
                <b>Cotizacion: </b> $<?php echo $orden['precio'];?> - <b>Documento: </b><a href = "../files/cotizaciones/<?php echo $orden['cotizacion_n'];?>" target = "blank"><?php echo $orden['cotizacion_n'];?></a> <br>
                  <?php
                  $totalE = 0;
                  $Extras = mysqli_query($conn, "SELECT * FROM orden_extras WHERE id_orden = $id_orden");
                  echo '<b class = "col s2">Extra(s): </b>';
                  if (mysqli_num_rows($Extras) > 0) {
                    echo '<table class = "col s6">
                        <thead>
                          <tr>
                          <th>Descripcion</th>
                          <th>Cantida</th>
                          </tr>
                        </thead>
                        <tbody>';
                    while ($extra = mysqli_fetch_array($Extras)) {
                      $totalE += $extra['cantidad'];
                      echo '<tr>
                          <td>'.$extra['descripcion'].'</td>
                          <td> $'.$extra['cantidad'].'</td>
                          </tr>';
                    }
                    echo '  </tbody>
                        </table><br><br><br><br><br><br>'; 
                  } ?><br>
                <b>TOTAL: $<?php echo $orden['precio']+$totalE;?></b><br> 
                <hr>
              </p>
              <br>
            </li>
      </ul>
      </div>
    	<form class="col s12">
        <div class="row">
          <?php if ($orden['liquidada'] == 0) { ?>

            
            <div class="">
              <h4>Pagos realizados a este Servicio</h4>
              <?php
              



              ?>

              <table class="bordered highlight responsive-table">
                <thead>
                  <tr>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                  </tr>
                </thead>
                <tbody>

                    <?php
                    $pagosOrden =  mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM pagos WHERE descripcion = $id_orden"), MYSQLI_ASSOC);
                    if ($pagosOrden != null) {
                      foreach ($pagosOrden as $pago) {
                        echo '
                          <tr>
                            <td>'.$pago["cantidad"].'</td>
                            <td>'.$pago["fecha"].'</td>
                            <td>'.$pago["tipo_cambio"].'</td>
                          </tr>
                        ';
                        }
                    }else{
                      echo '<tr>
                              <td>Sin pagos para este Servicio</td>
                            </tr>';
                    }
                    
                    ?>

                </tbody>

              </table>
            </div>
            <br><br>

            <div class="input-field col s6 m3 l3">
              <i class="material-icons prefix">local_atm</i>
              <input id="liquidar_s" type="number" class="validate" data-length="6" required value="<?php $edad = 20; echo ($pagosOrden != null) ? $orden['precio'] - $pago["cantidad"] : $orden['precio']?>">
              <label for="liquidar_s">Liquidar:</label>
            </div>
            <div class="col s6 m2 l2">
              <p>
                <br>
                <input type="checkbox" id="banco"/>
                <label for="banco">Banco</label>
              </p>
            </div> <div class="col s6 m2 l2">
              <p>
                <br>
                <input type="checkbox" id="san"/>
                <label for="san">SAN</label>
              </p>
            </div>
            <div class="input-field col s6 m3 l3">
              <i class="material-icons prefix">local_atm</i>
              <input id="referencia_f" type="text" class="validate" data-length="10" required>
              <label for="referencia_f">Referencia:</label>
            </div>
          
            <input id="id_orden" value="<?php echo htmlentities($id_orden);?>" type="hidden">
            <input id="id_cliente" value="<?php echo htmlentities($id_cliente);?>" type="hidden">
            <input id="liquidar" value="<?php echo htmlentities($orden['liquidada']);?>" type="hidden"><br>
            <a onclick="update_orden();" class="waves-effect waves-light btn pink"><i class="material-icons right">check</i>FACTURADO</a> <br><br>
          <?php } else { ?>
            <!------------------------------  TABLA DE PAGOS  ---------------------------------------->
              <h4>Historial </h4>
              <div id="mostrar_pagos">
                <table class="bordered highlight responsive-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Cantidad</th>
                      <th>Tipo</th>
                      <th>Descripción</th>
                      <th>Usuario</th>
                      <th>Fecha</th>
                      <th>Cambio</th>
                      <th>Imprimir</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$id_cliente." AND tipo != 'Dispositivo' ORDER BY id_pago DESC";
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
                          <td><?php echo $pagos['fecha'].' '.$pagos['hora'];?></td>
                          <td><?php echo $pagos['tipo_cambio'] ?></td>
                          <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                        </tr>
                        <?php
                        $aux--;
                      }//Fin while
                    }else{
                      echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
                    }
                    ?> 
                  </tbody>
                </table> <br><br>   
              </div>
          <?php } ?>
          <a href = "../php/ticket_orden.php?Id=<?php echo $id_orden;?>" target = "blank" class="waves-effect waves-light btn pink right"><i class="material-icons right">print</i>TIKET</a><br>
      </div>  
    </form>   	
    </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>