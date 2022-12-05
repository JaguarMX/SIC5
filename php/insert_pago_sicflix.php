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
$Descuento = $conn->real_escape_string($_POST['valorDescuento']);
//$Hasta = $conn->real_escape_string($_POST['valorHasta']);
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
    $sql_ver = mysqli_query($conn, "SELECT * FROM pagos_sicflix WHERE id_cliente = $IdCliente AND descripcion like '%$ver%' AND tipo = 'Mensualidad'");
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
            <h6 class="red-text center"><b>NO SE REISTRO EL PAGO YA QUE YA SE REGISTRO UN PAGO DEL MISMO MES:</b></h6>
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
            <form method="post" action="../views/pagos_sicflix.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente ?>"><button class="btn waves-effect red accent-2 waves-light" type="submit" name="action">
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
        $FechaCorte = date($Año.'-'.$N_Mes.'-05');
    }
}
#SI EL PAGO ES NORMAL REGISTRAR     
echo $Tipo;       
#--- CREAMOS EL SQL PARA LA INSERCION ---
$sql = "INSERT INTO pagos_sicflix (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', $Total, '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Cambio', 0)";

//fecha promesa falta con la variable $Hasta o no
if ($Tipo_Cambio == "Credito") {
    $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, hasta, tipo, descripcion, usuario) VALUES ($IdCliente, $Total, '$Fecha_hoy', NULL, '$Tipo', '$Descripcion', $id_user)";
    
    mysqli_query($conn,$mysql);
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
    $id_deuda = $ultimo['id'];
    $sql = "INSERT INTO pagos_sicflix (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', $Total, '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Cambio', $id_deuda, 0)";
}
#--- SE INSERTA EL PAGO -----------
if(mysqli_query($conn, $sql)){
    echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
    // Si el pago es de banco guardar la referencia....
}
if (($Tipo_Cambio == 'Banco' OR $Tipo_Cambio == 'SAN') AND $ReferenciaB != '') {
    $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos_sicflix WHERE id_cliente = $IdCliente"));            
    $id_pago = $ultimoPago['id'];
    mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
}

#ARREGLO EL CUAL DEFINE EL MES SIGUIENTE PARA LA FECHA DE CORTE DEL CLIENTE
$array =  array('ENERO' => '02','FEBRERO' => '03', 'MARZO' => '04','ABRIL' => '05', 'MAYO' => '06', 'JUNIO' => '07', 'JULIO' => '08', 'AGOSTO' => '09', 'SEPTIEMBRE' => '10', 'OCTUBRE' => '11', 'NOVIEMBRE' => '12',  'DICIEMBRE' => '01');      
$N_Mes = $array[$Mes];
#-- AQUI TOMAMOS EL MES EN TURNO PARA COMPARAR
$mes_sig = ['ENERO' ,'FEBRERO' , 'MARZO' ,'ABRIL' , 'MAYO' , 'JUNIO' , 'JULIO' , 'AGOSTO' , 'SEPTIEMBRE' , 'OCTUBRE' , 'NOVIEMBRE' ,  'DICIEMBRE' ][$N_Mes-1];
#COMO ES DICIEMBRE ADELANTAMOS UN AÑO PORQUE LA FECHA DE CORTE YA ES EL SIGUINETE AÑO PAGO TODO DICIEMBRE
if ($Mes == 'DICIEMBRE') {  $Año ++; }
#FECHA DE CORTE SEGUN EL MES Y AÑO SELECCIONADO
$FechaCorteSicflix = date($Año.'-'.$N_Mes.'-05'); 

#ACTUALIZAMOS LA FECHA DE CORTE   /////////////////       IMPORTANTE         ///////////////
mysqli_query($conn, "UPDATE clientes SET fecha_corte_sicflix='$FechaCorteSicflix' WHERE id_cliente='$IdCliente'");


#SOLO ACTIVAMOS SI LA FECHA DE CORTE ES MAYOR A HOY   /////////////////       IMPORTANTE         ///////////////
//if ($FechaCorte >= $Fecha_hoy) {
    ?>
    <!--  
    <script>
        id_cliente = <?php //echo $IdCliente; ?>;
        var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/activar_pago.php?id="+id_cliente;
        a.click();
    </script>
    -->
    <?php
//}//FIN IF SI ACTIVA
//else{
    //echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
//}
?>
<script>
    var a = document.createElement("a");	
    a.href = "../views/clientes.php";
    a.click();
</script>