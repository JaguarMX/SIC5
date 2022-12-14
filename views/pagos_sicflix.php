<html>
<head>
  <title>SIC | Pago Sicflix</title>
</head>
    <?php 
    include('fredyNav.php');
    include('../php/conexion.php');
    date_default_timezone_set('America/Mexico_City');
    $Fecha_Hoy = date('Y-m-d');
    $fecha_vacia = strtotime('0000-00-00');
    $no_cliente = 0;
    #AQUÍ CHECAMOS SI LAS VARIABLES ESTAN DEFINIDAS
    if (isset($_POST['no_cliente']) == false) {
        if (isset($_GET['cliente']) == false) {
            ?>
        <script>
            M.toast({html: "Regresando a clientes.", classes: "rounded"});
            setTimeout("location.href='clientes.php'", 800);
        </script>
        <?php
        }else{
            $no_cliente = $_GET['cliente'];
        }
    }else{
        $no_cliente = $_POST['no_cliente'];
    }#VERIFICAMOS QUE RECIBAMOS UN ID DE CLIENTE VALIDO
    if ($no_cliente > 0) {
        if (isset($_POST['resp']) == false) {
          $respuesta = 'Ver';
        }else{
            $respuesta = $_POST['resp'];
        }
    ?>
<?php } ?>

<?php
  //DEFINIMOS LA MENSUALIDAD --->
  $sql2 = "SELECT * FROM reporte_sicflix WHERE cliente = $no_cliente AND descripcion = 'Activar Sicflix' AND estatus >= 1 ORDER BY id DESC LIMIT 1";
  $info_reporte = mysqli_fetch_array(mysqli_query($conn, $sql2));
  $mensualidad=$info_reporte['precio_paquete'];
  if($mensualidad == NULL){
    $mensualidad=0;
    $txt_pqt="Selecciona paquete";
  }elseif($mensualidad==100){
    $txt_pqt="Paquete Premium $";
  }else{
    $txt_pqt="Paquete Básico $";
  };
?>
<!-- <<<///////////////>>> -->
<main>
  <body onload="total_cantidad();">
    <!-- //////////////////////////////////////// -->
    <!-- VARIABLES PARA OBTENER DATOS NECESARIOS  -->
    <!-- //////////////////////////////////////// -->
    <?php
      //Información del cliente
      $sql = "SELECT * FROM clientes WHERE id_cliente=$no_cliente";
      $datos = mysqli_fetch_array(mysqli_query($conn, $sql));
      //Sacamos la Comunidad
      $id_comunidad = $datos['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));

      //Información del usuario
      $user_id = $_SESSION['user_id'];
    ?>
    <!-- CONDICIONES PARA ASIGNAR EL TEXTO A LA FICHA DEL CLIENTE -->
    <?php
    //fecha de corte de sicflix
    $estatus_fecha_pago=$datos['fecha_corte_sicflix'];
    if($estatus_fecha_pago >  $Fecha_Hoy){
      $a_est_f_p = 'AL CORRIENTE';
      $b_est_f_p = "<strong><font color='1BD20A'>" .$a_est_f_p. "</font></strong>";
    }else{
      $a_est_f_p = 'INACTIVO';
      $b_est_f_p = "<strong><font color='red'>" .$a_est_f_p. "</font></font>";
    }
    ///////////////////////////////////////////////////////////////
    //contraseña de sicflix
    $estatus_pass=$datos['contraseña_sicflix'];
    if($estatus_pass != '0' AND $estatus_pass != ''){
      $a_pass= 'ACTIVADA';
      $b_pass= "<strong><font color='1BD20A'>" .$a_pass. "</font></strong>";
    }else{
      $a_pass= 'DESACTIVADA';
      $b_pass= "<strong><font color='red'>" .$a_pass. "</font></font>";
    }
    ?>

    <!-- CUADRO DE LOS DATOS DEL CLIENTE -->
    <div class="container">
      <h3 class="hide-on-med-and-down">Realizando pago del cliente SICFLIX:</h3>
      <h5 class="hide-on-large-only">Realizando pago del cliente SICFLIX:</h5>
      <ul class="collection">
        <li class="collection-item avatar">
          <img src="../img/cliente.png" alt="" class="circle">
          <span class="title"><b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
          <p><b>Nombre(s): </b><?php echo $datos['nombre'];?><br>
          <?php if ($area['area'] != 'Cobrador') { ?><b>Telefono: </b><?php echo $datos['telefono'];?><br><?php }?>
          <b>Comunidad: </b><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?><br>
          <?php if ($area['area'] != 'Cobrador') { ?>
          <b>Dirección: </b><?php echo $datos['direccion'];?><br>
          <b>Referencia: </b><?php echo $datos['referencia'];?><br>
          <?php }?>
          <b>Fecha Corte Sicflix: </b><span id="corte"><?php echo $datos['fecha_corte_sicflix'];?></span><br>
          <b>Contraseña: </b><?php echo $b_pass;?><br>
          <b>Estatus: </b><?php echo $b_est_f_p;?><br>
          </p> 
        </li>
      </ul>
    </div>

    <!-- CUADRO DEL FORMULARIO DE PAGO -->
    <div class="container">
      <h3 class="hide-on-med-and-down pink-text "><< SICFLIX >></h3>
      <h5 class="hide-on-large-only  pink-text"><< SICFLIX >></h5>
      <?php 
      if ($datos['sicflix'] < 1 ){
        echo "<center><b><h4>Es necesario activar reporte SICFLIX para realizar pago</h4></b></center>";
      }?>
      <!-- ----------------------------  FORMULARIO CREAR PAGO  ---------------------------------------->
      <div class="row">
        <div class="col s12">
          <br>
          <div class="row">
            <form class="col s12" name="formMensualidad">
              <div class="row">
                <!-- ----------------------------  VARIABLES DE USUARIOS CON ACCESO  ---------------------------------------->
                <?php 
                  $Ser = (in_array($user_id, array(10, 101, 103, 105, 49, 84, 106, 39)))? '': 'disabled="disabled"';
                  $Ser2 = (in_array($user_id, array(10, 101, 103, 105, 49, 106)))? '': 'disabled="disabled"';
                ?>
                <!-- ----------------------------  CONDICION DE DESHABILITACIÓN DE CASILLAS  ---------------------------------------->
                <?php 
                  if($datos['sicflix'] < 1){
                    $disabled = 'disabled="disabled"';
                  }else{
                    $disabled = '';
                  }
                ?>
                <!-- ----------------------------  CASILLA DE BANCO  ---------------------------------------->
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="banco" <?php echo $Ser; echo $disabled;?>/>
                    <label for="banco">Banco</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA DE SAN  ---------------------------------------->
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="san" <?php echo $Ser2; echo $disabled;?>/>
                    <label for="san">SAN</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA DE REFERENCIA  ---------------------------------------->
                <div class="col s6 m2 l2">
                  <div class="input-field">
                    <input id="ref" type="text" class="validate" data-length="15" required value="" <?php echo $disabled;?>>
                    <label for="ref">Referencia:</label>
                  </div>
                </div>
                <!-- ----------------------------  CASILLA DE CREDITO  ---------------------------------------->
                <div class="col s6 m2 l2">
                  <p>
                    <br>
                    <input type="checkbox" id="credito" <?php echo $disabled;?>/>
                    <label for="credito">Credito</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA PARA SELECCIONAR MES  ---------------------------------------->
                <div class="row col s8 m2 l2"><br>
                  <select id="mes" class="browser-default" <?php echo $disabled;?>>
                    <option value="0" selected>Seleccione Mes</option>
                    <option value="ENERO">Enero</option>
                    <option value="FEBRERO">Febrero</option>
                    <option value="MARZO">Marzo</option>
                    <option value="ABRIL">Abril</option>
                    <option value="MAYO">Mayo</option>
                    <option value="JUNIO">Junio</option>
                    <option value="JULIO">Julio</option>
                    <option value="AGOSTO">Agosto</option>
                    <option value="SEPTIEMBRE">Septiembre</option>
                    <option value="OCTUBRE">Octubre</option>
                    <option value="NOVIEMBRE">Noviembre</option>
                    <option value="DICIEMBRE">Diciembre</option>
                  </select>
                </div>
                <!-- ----------------------------  CASILLA PARA SELECCIONAR AÑO  ---------------------------------------->
                <div class="row col s8 m2 l2"><br>
                  <select id="año" class="browser-default" <?php echo $disabled;?>>
                    <option value="0" >Seleccione Año</option>       
                    <option value="2022" selected>2022</option>         
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>          
                  </select>
                </div>
                <!-- ----------------------------  CAJA DE SELECCION DE PAQUETES  ---------------------------------------->
                <div class="input-field col s10 m4 l4">
                  <!-- <select id="cambio3" class="browser-default col s12 m8 l8" required onchange="javascript:showContent()"> -->
                  <select id="paquete" class="browser-default col s12 m10 l10" <?php echo $disabled;?> required onchange="javascript:showContent()">
                    <option value="paq_default" selected ><?php echo $txt_pqt ." ". $mensualidad ?></option>
                    <option value="Básico" >Básico $60</option>
                    <option value="Premium" >Premium $100</option>
                  </select>
                </div>
                <script>
                  <?php
                    
                  ?>
                </script>
                <!-- ----------------------------  CASILLA DE TOTAL  ---------------------------------------->
                <?php $total=$mensualidad;?>
                <div class="row col s12 m2 l2">
                  <h5 class="indigo-text" >TOTAL  <input class="col s11" type="" id="total1" value="$<?php echo $total ?>"></h5>
                </div>     
              </div>
              <input id="id_cliente" value= "<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
              <input id="total" value="<?php echo htmlentities($total);?>" type="hidden">
              <input id="id_comunidad" value="<?php echo htmlentities($comunidad['id_comunidad']);?>" type="hidden">
              <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
            </form>
          <!-- ----------------------------  BOTON REGISTRAR PAGO  ----------------------------------------> 
          <a onclick="insert_pago(<?php echo ($datos['sicflix'] == 1 ) ?>);" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a>
        </div>
        <br>
        <!------------------------------  TABLA DE PAGOS  ---------------------------------------->
        <h4>Historial de Pagos</h4>
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
                <th>Cambio</th>
                <th>Imprimir</th>
                <th>Borrar</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // SELECCIONAMOS ÚNICAMENTE LOS PAGOS DE SICFLIX
              $sql_pagos = "SELECT * FROM `pagos` WHERE id_cliente = ".$datos['id_cliente']." AND cantidad > 0 AND tipo = 'SICFLIX' ORDER BY id_pago DESC";
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
                    <td><?php echo ($pagos['tipo_cambio'] != 'Credito')?$pagos['tipo_cambio'] : '<form method="post" action="../views/credito.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-small waves-effect waves-light indigo">Credito</button>' ; ?></td>
                    <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                    <td><a onclick="verificar_eliminar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a>    </td>
                  </tr>
                  <?php
                  $aux--;
                }//Fin while
              }else{
                echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
              }
              ?> 
            </tbody>
          </table>    
        </div>
      </div><!------------------ row FORMULARIO CREAR PAGO  -------------------------------------> 
    </div><!-------------------------  CONTAINER  -------------------------------------->
  </body><!-------------------------  BODY  -------------------------------------->
</main><!-------------------------  MAIN  -------------------------------------->       
</html>



<!-- ###########SCRIPTS PARA LAS FUNCIONES################# -->
<script>
  function showContent(){
    var $sel = $("#paquete").val();
    //var $sel = $("#paquete option:selected").val();
    if($sel="Básico"){
      $total = 60;
      //$total = $("#total1").val(60);
    }else if($sel="Premium"){
      $total = 100;
      //$total = $("#total1").val(100);
    }else{
      $total = $mensualidad;
    }
  };
  //FUNCIÓN TOTAL_CANTIDAD------------------------------------------>
  function total_cantidad(){
    var MensualidadAux = $("input#total").val();
    var Mensualidad = parseInt(MensualidadAux);

    //document.formMensualidad.total.value = '$0.00';
    if (Mensualidad > 0) {
      Mostrar = Mensualidad;
      document.formMensualidad.total1.value = '$'+Mostrar;
      //document.formMensualidad.total.value = '$'+$mensualidad;
    }
  };
  //-----------------------------------------------------------------||

  //FUNCIÓN VERIFICAR_ELIMINAR------------------------------------------>
  function verificar_eliminar(IdPago){ 
    var textoIdCliente = $("input#id_cliente").val();  
    $.post("../php/verificar_eliminar_pago_sicflix.php", {
      valorIdPago: IdPago,
      valorIdCliente: textoIdCliente,
    }, function(mensaje) {
      $("#modalBorrar").html(mensaje);
    }); 
  };
  function imprimir(id_pago){
    var a = document.createElement("a");
    a.target = "_blank";
    a.href = "../php/imprimir_sicflix.php?IdPago="+id_pago;
    a.click();
  };


  //FUNCIÓN INSERT_PAGO------------------------------------------>
  function insert_pago(sicflix) {  
    textoTipo = "SICFLIX";
    var CantidadAUX = $("input#total").val();
    var textoTotal = parseInt(CantidadAUX);
    var textoMes = $("select#mes").val();
    var textoAño = $("select#año").val();
    var textoRef = $("input#ref").val();
    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = textoMes+" "+textoAño;
          
    var textoComunidad = $("input#id_comunidad").val();

    if (true) {}

    if(document.getElementById('banco').checked==true){
      textoTipo_Cambio = "Banco";
    }else if (document.getElementById('credito').checked==true) {
      textoTipo_Cambio = "Credito";
    }else if (document.getElementById('san').checked==true) {
      textoTipo_Cambio = "SAN";
    }else{
      textoTipo_Cambio = "Efectivo";
    } 

    var textoIdCliente = $("input#id_cliente").val();
    var textoRespuesta = $("input#respuesta").val();

    if (textoTotal == "" || textoTotal == 0) {
      M.toast({html: 'El campo Total se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoMes == '0') {
      M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
    }else if (textoAño == '0') {
      M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if ((document.getElementById('banco').checked==true || document.getElementById('san').checked==true) && textoRef == "") {
      M.toast({html: 'Los pagos en banco y san deben de llevar una referencia.', classes: 'rounded'});
    }else if (document.getElementById('banco').checked==false && document.getElementById('san').checked==false && textoRef != "") {
      M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
    }else {
      $.post("../php/insert_pago_sicflix.php" , { 
        valorTipo_Cambio: textoTipo_Cambio,
        valorTipo: textoTipo,
        valorTotal: textoTotal,
        valorDescripcion: textoDescripcion,
        valorIdCliente: textoIdCliente,
        valorRef: textoRef,
        valorRespuesta: textoRespuesta,
        valorMes: textoMes,
        valorAño: textoAño
      }, function(mensaje) {
        $("#mostrar_pagos").html(mensaje);
      });  
    }    
  };
</script>