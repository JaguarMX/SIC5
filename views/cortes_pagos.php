<!DOCTYPE html>
<html lang="en">
<head>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
  include('../php/conexion.php');
?>
<title>SIC | Cortes Pagos</title>
</head>
<script>
  //FUNCION QUE RIDERECCIONA A UNA NUEVA PESTAÑA DONDE SE CREARA EL TICKET DE CONFIRMACION
  function ticket_confirmar(){
    var id = $("input#id_corte_confirmar").val(); 
    if (id <= 0) {
        M.toast({html:"Ingese un Id de corte.", classes: "rounded"});
    }else{
      var a = document.createElement("a");
          a.target = "_blank";
          //REDIRECCIONA Y ENVIAMOS UN LETRA Y EL ID DEL CORTE
          a.href = "../php/ticket_confirmar.php?id="+id;
          a.click();
    }
  }
  //FUNCION QUE ENVIA LOS DATOS PARA VALIDAR DESPUES DE LLENADO DEL MODAL
  function recargar_corte() {
    var textoClave = $("input#clave").val(); 
    var textoRecibio = $("select#recibio").val(); 
    var textoCantidad = $("input#cantidadD").val(); 
    var textoDescripcion = $("input#descripcionD").val();
    var textoCantidadSAN = $("input#cantidadSAN").val(); 
    var textoDescripcionSAN = $("input#descripcionSAN").val();

    entra = "Si";
    if (textoCantidad != 0 || textoDescripcion != "") {
      if (textoCantidad <= 0) {
        entra = "No";
        texto = "Ingrese una cantidad correcta";
      }
      if (textoDescripcion == "") {
        entra = "No";
        texto = "Ingrese una descripcion correcta";
      }
    } 
    if (textoCantidadSAN != 0 || textoDescripcionSAN != "") {
      if (textoCantidadSAN <= 0) {
        entra = "No";
        texto = "Ingrese una cantidad correcta";
      }
      if (textoDescripcionSAN == "") {
        entra = "No";
        texto = "Ingrese una descripcion correcta";
      }
    } 
    if (textoClave == "") {
        M.toast({html:"El campo clave no puede ir vacío.", classes: "rounded"});
    }else if (textoRecibio == 0) {
        M.toast({html:'Seleccione a la persona que le recibió el corte.', classes: "rounded"});
    }else if (entra == "No") {
        M.toast({html:texto, classes: "rounded"});
    }else{
      $.post("../php/crear_corte.php", {
          valorClave: textoClave,
          valorRecibio: textoRecibio,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
          valorCantidadSAN: textoCantidadSAN,
          valorDescripcionSAN: textoDescripcionSAN,
        }, function(mensaje) {
           $("#resultado_corte").html(mensaje);
      });
    }   
  } 
  //FUNCION QUE ENVIA LA INFORMACION PARA CONFIRMAR EL CORTE Y CHECAR SI EL COBRADOR ENTREGO TODO O QUEDO A DEBER EFECTIVO
  function confirmar(){
    var textoIdCorteConfirmar = $("input#id_corte_confirmar").val(); 
    var textoCantidadCorteConfirmar = $("input#cantidadCon").val(); 

    if (textoIdCorteConfirmar <= 0) {
        M.toast({html:"Ingese un Id de corte.", classes: "rounded"});
    }else{
        $.post("../php/confirmar_corte.php", {
              valorIdCorteConfirmar: textoIdCorteConfirmar,
              valorCantidadCorteConfirmar: textoCantidadCorteConfirmar,
            }, function(mensaje) {
                $("#resultado_confirmar").html(mensaje);
        });
    }
  }
</script>
<main>
<body>
  <div class="container">
    <?php
    $id_user = $_SESSION['user_id'];// INFORMACION DEL USUARIO LOGEADO
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
    ?>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_corte"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="resultado_corte"></div> 
    <h3 class="hide-on-med-and-down">Pagos realizados por: <?php echo $usuario['user_name'];?></h3>
    <h5 class="hide-on-large-only">Pagos realizados por: <?php echo $usuario['user_name'];?></h5>
    <?php
      // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
      $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $id_user"));
      $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_user AND tipo = 'Abono Corte'"));
      //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
      if ($deuda['suma'] == "") {
        $deuda['suma'] = 0;
      }elseif ($abono['suma'] == "") {
        $abono['suma'] = 0;
      }
      //SE RESTAN DEUDAS DE ABONOS
      $Saldo = $deuda['suma']-$abono['suma'];
      // SI SE ENCUENTRA SALDO PENIDNTE SE MUESTRA
      if ($Saldo > 0) {
        ?>
        <h4 class="hide-on-med-and-down right red-text">Saldo Pendiente de: $<?php echo $Saldo;?> <form method="post" action="../views/saldo_cobrador.php"><input name="id" type="hidden" value="<?php echo $id_user; ?>"><button type="submit" class="btn btn-tiny waves-effect waves-light pink"><i class="material-icons  right">send</i>VER</button></form></h4>
        <h5 class="hide-on-large-only right red-text">Saldo Pendiente de: $<?php echo $Saldo;?> <form method="post" action="../views/saldo_cobrador.php"><input name="id" type="hidden" value="<?php echo $id_user; ?>"><button type="submit" class="btn btn-tiny waves-effect waves-light pink"><i class="material-icons  right">send</i>VER</button></form></h5>
        <br><br>
      <?php }//FIN IF ?>
    <!-----------------------------------------------------------------------------
    #              -------------  PAGOS DE INTERNET ------------------
    #------------------------------------------------------------------------------>
    <div class="row">
        <ul class="collection">
          <?php 
          $sql_pagos_int = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo != 'Dispositivo'  AND tipo != 'Orden Servicio' AND tipo != 'Punto Venta'");
          ?>
          <li class="collection-item grey"><h6><b> >>> INTERNET: <span class="new badge green" data-badge-caption="pago(s)"><?php echo mysqli_num_rows($sql_pagos_int); ?></span></b></h6></li>
        </ul>
        <ul class="collapsible">
          <?php 
          $sql_pagos_efectivo_int = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo'  AND tipo != 'Orden Servicio' AND tipo != 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN EFECTIVO DE INTERNET SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_efectivo_int) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">local_atm</i>EFECTIVO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_efectivo_int)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      if ($pagos['tipo'] == 'Abono Corte') {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
                      }else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      }else{
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo ($pagos['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL EFECTIVO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF EFECTIVO

          $sql_pagos_banco_int = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo'  AND tipo != 'Orden Servicio' AND tipo != 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN BANCO DE INTERNET SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_banco_int) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">credit_card</i>A BANCO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_banco_int)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      if ($pagos['tipo'] == 'Abono Corte') {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
                      }else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      }else{
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo ($pagos['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL BANCO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF BANCO

          $sql_pagos_credito_int = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo'  AND tipo != 'Orden Servicio' AND tipo != 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN CREDITO DE INTERNET SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_credito_int) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">featured_play_list</i>A CREDITO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_credito_int)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      if ($pagos['tipo'] == 'Abono Corte') {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
                      }else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      }else{
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo ($pagos['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL CREDITO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF CREDITO
          ?>
        </ul>
    </div>
    <!-----------------------------------------------------------------------------
    #           ------------  PAGOS DEL PUNTO DE VENTA ------------------
    #----------------------------------------------------------------------------->
    <div class="row">
        <ul class="collection">
          <?php 
          $sql_pagos_pv = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo = 'Punto Venta'");
          ?>
          <li class="collection-item grey"><h6><b> >>> PUNTO VENTA: <span class="new badge green" data-badge-caption="pago(s)"><?php echo mysqli_num_rows($sql_pagos_pv) ; ?></span></b></h6></li>
        </ul>
        <ul class="collapsible">
          <?php 
          $sql_pagos_efectivo_pv = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN EECTIVO DEL PUNTO DE VENTA SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_efectivo_pv) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">local_atm</i>EFECTIVO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pago = mysqli_fetch_array($sql_pagos_efectivo_pv)) {
                      $aux ++;
                      $id_cliente = $pago['id_cliente'];
                      if ($id_cliente == 0) {
                        $cliente['nombre'] = 'Venta Publico';
                      }else{
                        $id_cliente = $pago['id_cliente']-100000;
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM  `punto-venta_clientes` WHERE id=$id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td>
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pago['descripcion']; ?></td>
                        <td><?php echo $pago['tipo']; ?></td>
                        <td><?php echo $pago['fecha'].' '.$pago['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pago['cantidad']); ?></td>
                      </tr>
                      <?php 
                      $Total += $pago['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL EFECTIVO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF EFECTIVO

          $sql_pagos_banco_pv = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN BANCO DEL PUNTO DE VENTA SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_banco_pv) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"> <i class="material-icons">credit_card</i> A BANCO </div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N°</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pago = mysqli_fetch_array($sql_pagos_banco_pv)) {
                      $aux ++;
                      $id_cliente = $pago['id_cliente'];
                      if ($id_cliente == 0) {
                        $cliente['nombre'] = 'Venta Publico';
                      }else{
                        $id_cliente = $pago['id_cliente']-100000;
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM  `punto-venta_clientes` WHERE id=$id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td>
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pago['descripcion']; ?></td>
                        <td><?php echo $pago['tipo']; ?></td>
                        <td><?php echo $pago['fecha'].' '.$pago['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pago['cantidad']); ?></td>
                      </tr>
                      <?php 
                      $Total += $pago['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL A BANCO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>
              </div>
            </li> 
          <?php 
          }// FIN IF BANCO

          $sql_pagos_credito_pv = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo = 'Punto Venta'");
          //VERIFICAMOS SI HAY PAGOS EN Credito DEL PUNTO DE VENTA SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_credito_pv) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">featured_play_list</i> A CREDITO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N°</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pago = mysqli_fetch_array($sql_pagos_credito_pv)) {
                      $aux ++;
                      $id_cliente = $pago['id_cliente'];
                      if ($id_cliente == 0) {
                        $cliente['nombre'] = 'Venta Publico';
                      }else{
                        $id_cliente = $pago['id_cliente']-100000;
                        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM  `punto-venta_clientes` WHERE id=$id_cliente"));
                      }
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td>
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pago['descripcion']; ?></td>
                        <td><?php echo $pago['tipo']; ?></td>
                        <td><?php echo $pago['fecha'].' '.$pago['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pago['cantidad']); ?></td>
                      </tr>
                      <?php 
                      $Total += $pago['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL A CREDITO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>
              </div>
            </li> 
          <?php 
          }// FIN IF CREDITO
          ?>         
        </ul>
    </div>
    <!-----------------------------------------------------------------------------
    #           ------------  PAGOS DEL SERVICIO TECNICO ------------------
    #----------------------------------------------------------------------------->
    <div class="row">
        <ul class="collection">
          <?php 
          $sql_pagos_ST = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo = 'Dispositivo'");
          ?>
          <li class="collection-item grey"><h6><b> >>> SERVICIO TECNICO: <span class="new badge green" data-badge-caption="pago(s)"><?php echo mysqli_num_rows($sql_pagos_ST); ?></span></b></h6></li>
        </ul>
        <ul class="collapsible">
          <?php 
          $sql_pagos_efectivo_ST = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
          //VERIFICAMOS SI HAY PAGOS EN EFECTIVO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_efectivo_ST) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">local_atm</i>EFECTIVO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_efectivo_ST)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL EFECTIVO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF EFECTIVO

          $sql_pagos_banco_ST = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'");
          //VERIFICAMOS SI HAY PAGOS EN BANCO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_banco_ST) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">credit_card</i>A BANCO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_banco_ST)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL BANCO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF BANCO

          $sql_pagos_credito_ST = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo = 'Dispositivo'");
          //VERIFICAMOS SI HAY PAGOS EN CREDITO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_credito_ST) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">featured_play_list</i>A CREDITO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_credito_ST)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL CREDITO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF CREDITO
          ?>
        </ul>
    </div>
    <!-----------------------------------------------------------------------------
    #        ------------  PAGOS DE LAS ORDENES DE SERVICO -----------------
    #----------------------------------------------------------------------------->
    <div class="row">
        <ul class="collection">
          <?php 
          $sql_pagos_OS = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo = 'Orden Servicio'");
          ?>
          <li class="collection-item grey"><h6><b> >>> ORDEN SERVICIOS: <span class="new badge green" data-badge-caption="pago(s)"><?php echo mysqli_num_rows($sql_pagos_OS); ?></span></b></h6></li>
        </ul>
        <ul class="collapsible">
          <?php 
          $sql_pagos_efectivo_OS = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'");
          //VERIFICAMOS SI HAY PAGOS EN EFECTIVO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_efectivo_OS) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">local_atm</i>EFECTIVO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_efectivo_OS)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL EFECTIVO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF EFECTIVO

          $sql_pagos_banco_OS = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Orden Servicio'");
          //VERIFICAMOS SI HAY PAGOS EN BANCO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_banco_OS) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">credit_card</i>A BANCO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_banco_OS)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL BANCO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF BANCO

          $sql_pagos_credito_OS = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo = 'Orden Servicio'");
          //VERIFICAMOS SI HAY PAGOS EN CREDITO DE LAS ORDENES DE SERVICO SI HAY MOSTRAMOS EL DESPLEGABLE
          if (mysqli_num_rows($sql_pagos_credito_OS) > 0) { 
            ?>
            <li>
              <div class="collapsible-header"><i class="material-icons">featured_play_list</i>A CREDITO</div>
              <div class="collapsible-body">
                <table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>N° Cliente</th>
                      <th>Cliente</th>
                      <th>Descripción</th>
                      <th>Tipo</th>
                      <th>Fecha y Hora</th>
                      <th>Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $aux = 0;
                    $Total = 0;
                    while ($pagos = mysqli_fetch_array($sql_pagos_credito_OS)) {
                      $aux ++;
                      $id_cliente = $pagos['id_cliente'];
                      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
                      ?>
                      <tr>
                        <td><?php echo $aux; ?></td> 
                        <td><?php echo $id_cliente; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $pagos['descripcion']; ?></td>
                        <td><?php echo $pagos['tipo']; ?></td>
                        <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                        <td>$<?php echo sprintf('%.2f', $pagos['cantidad']); ?></td>
                      </tr>
                      <?php
                      $Total += $pagos['cantidad'];
                    }
                  ?>
                  </tbody>
                </table>
                <h6 class="right"><b>TOTAL CREDITO . $<?php echo sprintf('%.2f', $Total); ?> </b></h6><br>                 
              </div>
            </li> 
          <?php 
          }// FIN IF CREDITO
          ?>
        </ul>
    </div>    
    
    <div class="row">
      <a class="waves-effect waves-light btn-large pink right modal-trigger" href="#corte">Realizar CORTE<i class="material-icons right">content_cut</i></a>
    </div>

    <!-- VISTA DE CONFIRMAR PAGO  -->
    <div id="resultado_confirmar"></div>
    <div class="row"><br>
      <h4 class="hide-on-med-and-down">Confirmar Corte:</h4>
      <h5 class="hide-on-large-only">Confirmar Corte:</h5>
      <form class="col s12"><br>     
          <div class="row col s10 m3 l3">
            <div class="input-field">
                <i class="material-icons prefix">filter_9_plus</i>
                <input id="id_corte_confirmar" type="number" class="validate" data-length="6"  required>
                <label for="id_corte_confirmar">Corte (ej: 1243):</label>
            </div>
          </div>
          <div class="row col s10 m3 l3">
            <div class="input-field">
                <i class="material-icons prefix">payment</i>
                <input id="cantidadCon" type="number" class="validate" data-length="6" value="0" required>
                <label for="cantidadCon">Cantidad (Efectivo):</label>
            </div>
          </div>
          <div class="row"><br>
            <a onclick="ticket_confirmar();" class="waves-effect waves-light btn indigo right "><i class="material-icons right">print</i>TIKET</a>
            <a onclick="confirmar();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Confirmar</a>
          </div>
      </form>
    </div>
  </div><br><br><!-- FIN DE CONTAINER  -->
  <?php mysqli_close($conn);?>
</body>
</main>
</html>