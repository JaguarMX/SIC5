<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');

//VARIABLES A UTILIZAR DESDE EL ARCHIVO pagos_sicflix.php
$Tipo_Cambio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Total = $conn->real_escape_string($_POST['valorTotal']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$ReferenciaB = $conn->real_escape_string($_POST['valorRef']);
$Respuesta = $conn->real_escape_string($_POST['valorRespuesta']);

$entra = 'No';
if ($Respuesta == 'Ver') {
    $sql_DEUDAS = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0 AND id_cliente = '$IdCliente'");
    $sql_Abono = mysqli_query($conn, "SELECT * FROM pagos WHERE tipo = 'Abono' AND fecha = '$Fecha_hoy' AND id_cliente = '$IdCliente'");
    if (mysqli_num_rows($sql_DEUDAS)>0 AND mysqli_num_rows($sql_Abono) == 0) {
      ?>
      <script>
        $(document).ready(function(){
          $('#mostrarmodal').modal();
          $('#mostrarmodal').modal('open'); 
        });
      </script>
      <!-- Modal Structure -->
      <div id="mostrarmodal" class="modal">
        <div class="modal-content">
          <h4 class="red-text center">! Advertencia !</h4>
          <p>
          <h6 class="blue-text"><b>FAVOR DE PAGAR TIENE DEUDA(s):</b></h6><br>
          <table>
            <thead>
              <th>Descripción</th>
              <th>Fecha</th>
              <th>Cantidad</th>
              <th>Registró</th>
            </thead>
            <tbody>
          <?php
          $total=0;
          while ($deuda = mysqli_fetch_array($sql_DEUDAS)) {
            $id_userd = $deuda['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_userd"));
             ?>
             <tr>
               <td><?php echo $deuda['descripcion']; ?></td>
               <td><?php echo $deuda['fecha_deuda']; ?></td>
               <td>$<?php echo $deuda['cantidad']; ?></td>
               <td><?php echo $user['firstname']; ?></td>
             </tr>
             <?php 
             $total += $deuda['cantidad'];          
          }
          ?>
              <tr>
                <td></td><td><b>TOTAL:</b></td>
                <td><b>$<?php echo $total; ?></b></td><td></td>
              </tr>
            </tbody>
          </table><br><br>
          <h6 class="red-text"><b>Para resolver cualquier duda favor de marcar a oficinal al 433 935 6286 y 433 935 6288.</b></h6>
          </p>
        </div>
        <div class="modal-footer row">
          <?php 
          $rol = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
          if ($rol['area'] == 'Administrador') {
          ?>
          <form method="post" action="../views/pagos_sicflix.php">
            <input id="resp" name="resp" type="hidden" value="Si">
            <input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente;?>">
            <button class="btn waves-effect red accent-4 waves-light" type="submit" name="action"><b>Registrar</b></button>
          </form>
         <?php  } ?>
          <form method="post" action="../views/credito.php">
            <input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente;?>">
            <button class="btn waves-effect green accent-4 waves-light" type="submit" name="action"><b>Liquidar</b></button>
          </form>
          <form action="../views/clientes.php">
            <button class="btn waves-effect waves-light" type="submit" name="action"><b>Cancelar</b></button>
          </form><br>
        </div>
      </div>
      <?php
        echo '<script>M.toast({html:"Este cliente tiene deudas.", classes: "rounded"})</script>';
    }else {
      $entra = "Si";
    }
}else{
  $entra = $Respuesta;
}

if ($entra == "Si") {   
    $Mes = $conn->real_escape_string($_POST['valorMes']);
    $Año = $conn->real_escape_string($_POST['valorAño']);
    $ver = $Mes.' '.$Año;
    $sql_ver = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion like '%$ver%' AND tipo = 'SICFLIX'");
    if(mysqli_num_rows($sql_ver)>0){
        echo '<script>M.toast({html:"Ya se encuentra un pago del mismo mes y mismo año.", classes: "rounded"})</script>';
        ?>
        <script>
            $(document).ready(function(){
                $('#mostrarmodal').modal();
                $('#mostrarmodal').modal('open'); 
            });
        </script>
        <!-- Modal Structure -->
        <div id="mostrarmodal" class="modal">
        <div class="modal-content">
            <h4 class="red-text center">! Advertencia !</h4>
            <p>
            <h6 class="red-text center"><b>NO SE REGISTRO EL PAGO YA QUE YA SE REGISTRO UN PAGO DEL MISMO MES:</b></h6>
            <table class="bordered highlight responsive-table  " id="mostrar_pagos">
                <thead>
                    <tr>
                        <th>Id_Cliente</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Regitró</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $aux = mysqli_num_rows($sql_ver);
                    if($aux>0){
                        $pago = mysqli_fetch_array ($sql_ver);
                        $id = $pago['id_user'];
                        $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from users WHERE user_id = '$id'"));
                        ?>
                        <tr>
                            <td><b><?php echo $IdCliente;?></b></td>
                            <td><?php echo $pago['fecha'];?></td>
                            <td><?php echo $pago['descripcion'];?></td>
                            <td><?php echo $user['firstname'];?></td>
                        </tr>
                        <?php
                    }else{
                        echo "<center><b><h5>Este cliente aún no ha registrado reportes</h5></b></center>";
                    }
                    ?>        
                </tbody>
            </table><br>
            <h6 class="blue-text"><b>Pago No Registrado:</b></h6>
            <table class="bordered highlight responsive-table ">
                <thead>
                <tr>
                    <th>Id_Cliente</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Regitró</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo $IdCliente; ?></td>
                    <td><?php echo $Fecha_hoy; ?></td>
                    <td><?php echo $Descripcion; ?></td>
                    <td><?php  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from users WHERE user_id = '$id_user'")); echo $user['firstname']; ?></td>
                </tr>
                </tbody>
            </table>
            </p>
        </div>
        <div class="modal-footer">
            <form method="post" action="../views/clientes.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente ?>"><button class="btn waves-effect red accent-2 waves-light" type="submit" name="action">
            <b>Aceptar</b>
            </button></form>
        </div>
        </div>
        <?php
    }else{
        #ARREGLO EL CUAL DEFINE EL MES SIGUIENTE PARA LA FECHA DE CORTE DEL CLIENTE
        $array =  array('ENERO' => '02','FEBRERO' => '03', 'MARZO' => '04','ABRIL' => '05', 'MAYO' => '06', 'JUNIO' => '07', 'JULIO' => '08', 'AGOSTO' => '09', 'SEPTIEMBRE' => '10', 'OCTUBRE' => '11', 'NOVIEMBRE' => '12',  'DICIEMBRE' => '01');      
        $N_Mes = $array[$Mes];
        #-- AQUI TOMAMOS EL MES EN TURNO PARA COMPARAR
        $mes_sig = ['ENERO' ,'FEBRERO' , 'MARZO' ,'ABRIL' , 'MAYO' , 'JUNIO' , 'JULIO' , 'AGOSTO' , 'SEPTIEMBRE' , 'OCTUBRE' , 'NOVIEMBRE' ,  'DICIEMBRE' ][$N_Mes-1];
        #COMO ES DICIEMBRE ADELANTAMOS UN AÑO PORQUE LA FECHA DE CORTE YA ES EL SIGUINETE AÑO PAGO TODO DICIEMBRE
        if ($Mes == 'DICIEMBRE') {  $Año ++; }
        #FECHA DE CORTE SEGUN EL MES Y AÑO SELECCIONADO
        $FechaCorteSicflix = date($Año.'-'.$N_Mes.'-05');

        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $IdCliente"));

        #SI EL PAGO ES NORMAL REGISTRAR     
        echo $Tipo;       
        #--- CREAMOS EL SQL PARA LA INSERCION ---
        $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', $Total, '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Cambio', 0)";

        //fecha promesa falta con la variable $Hasta o no
        if ($Tipo_Cambio == "Credito") {
            $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, hasta, tipo, descripcion, usuario) VALUES ($IdCliente, $Total, '$Fecha_hoy', NULL, '$Tipo', '$Descripcion', $id_user)";
            
            mysqli_query($conn,$mysql);
            $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
            $id_deuda = $ultimo['id'];
            $sql = "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', $Total, '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Cambio', $id_deuda, 0)";
        }
        #--- SE INSERTA EL PAGO -----------
        if(mysqli_query($conn, $sql)){
            echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
            // Si el pago es de banco guardar la referencia....
        }else{
            echo '<script>M.toast({html:"Error al insertar a pagos.", classes: "rounded"})</script>'; 
        }
        if (($Tipo_Cambio == 'Banco' OR $Tipo_Cambio == 'SAN') AND $ReferenciaB != '') {
            $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
            $id_pago = $ultimoPago['id'];
            mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
        }
        #ACTUALIZAMOS LA FECHA DE CORTE   /////////////////       IMPORTANTE         ///////////////
        mysqli_query($conn, "UPDATE clientes SET fecha_corte_sicflix='$FechaCorteSicflix' WHERE id_cliente='$IdCliente'");
        
        
        //<<<<<<<<<<<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>>>>>>>>>>>//
        //CONDICIONES PARA LA ACTIVACIÓN Y DESACTIVACIÓN AUTOMÁTICA

        //Aquí se declara una variable para tomar la informacion de la tabla reporte_sicflix
        $sql = "SELECT * FROM reporte_sicflix";
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
                        $Solucion = 'Fecha vencida';
                        $sql3 = "INSERT INTO `reporte_sicflix` (cliente, descripcion, estatus, paquete, precio_paquete, fecha_registro, registro, nombre_usuario_sicflix, contraseña_sicflix, solucion) VALUES ($IdCliente, '$Descripcion',$Estaus, '$Paquete', $PrecioPaquete, '$Fecha_hoy', $id_user, '$Nombre_Usuario', '$Pass', '$Solucion')";
                        if(mysqli_query($conn, $sql3)){
                            echo '<script>M.toast({html:"Se generó un reporte de desactivación SICFLIX.", classes: "rounded"})</script>';
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
                            echo '<script>M.toast({html:"Se generó un reporte de Activación SICFLIX.", classes: "rounded"})</script>';
                        }else{
                            echo  '<script>M.toast({html:"Ha ocurrido un error con el insert del reporte de activación.", classes: "rounded"})</script>';	
                        }
                    }
                }//FIN DE LA CONDICION DE ACTIVACIÓN AUTOMÁTICA
            }//FIN WHILE
        }//FIN DE LAS CONDICIONES DE ACTIVACIÓN Y DESACTIVACIÓN AUTOMÁTICA 


        #SOLO ACTIVAMOS SI LA FECHA DE CORTE ES MAYOR A HOY   /////////////////       IMPORTANTE         ///////////////
        if ($FechaCorteSicflix >= $Fecha_hoy) {
            ?>
            <script>
                id_cliente = <?php //echo $IdCliente; ?>;
                var a = document.createElement("a");
                a.target = "_blank";
                a.href = "../php/activar_pago.php?id="+id_cliente;
                a.click();
            </script>
            <?php
        }//FIN IF SI ACTIVA
        else{
            echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
        }
    }//FIN ELSE PAGO NO REPETIDO
    ?>
    <div id="modalBorrar"></div>
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
                    <th>Imprimir</th>
                    <th>Borrar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_pagos = "SELECT * FROM pagos WHERE tipo = 'SICFLIX' AND id_cliente = ".$IdCliente." ORDER BY id_pago DESC";
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
                            <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                            <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-4 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                        </tr>
                        <?php
                        $aux--;
                    }//fin while
                }else{
                    echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
                }
                ?>        
            </tbody>
        </table>
    </div>
<?php
}//FIN IF ENTRA
mysqli_close($conn);
?>

<!-- <script> -->
    <!-- var a = document.createElement("a");	 -->
    <!-- a.href = "../views/clientes.php"; -->
    <!-- a.click(); -->
<!-- </script> -->