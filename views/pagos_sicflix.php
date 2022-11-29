<html>
<head>
  <title>SIC | Pago Sicflix</title>
</head>
    <?php 
    include('fredyNav.php');
    include('../php/conexion.php');
    date_default_timezone_set('America/Mexico_City');
    $Fecha_Hoy = date('Y-m-d');
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
  //DEFINIMOS LA MENSUALIDAD
  //¿Cuánto de mensualidad?
  $sql2 = "SELECT * FROM reporte_sicflix WHERE cliente=$no_cliente";
  $mensualidad = mysqli_fetch_array(mysqli_query($conn, $sql2));
?>
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

      //ok aquí hay que preguntar cuándo se cambia la fecha de vencimiento porque esta establecida para 12 meses despues de la fecha de corte
      $Instalacion = $datos['fecha_corte_sicflix'];
      $nuevafecha = strtotime('+12 months', strtotime($Instalacion));
      $Vence = date('Y-m-d', $nuevafecha);

      //Información del usuario
      $user_id = $_SESSION['user_id'];


      //VER CUANTOS DIAS HAN PASADO DESDE EL ULTIMO CORTE SOLO SI LA FECHA DE CORTE ES MENOR A HOY
      //HAY QUE PROBARLO CON UNA FECHA MAS ADELANTADA PORQUE TODO ES 0 
      $Descuento = 0;
      $corteInt = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM int_cortes ORDER BY id DESC LIMIT 1"));
      if ($datos['fecha_corte_sicflix'] < $Fecha_Hoy ) {
        $mesA = date('Y-m');
        $ver = explode("-", $corteInt['fecha']);
        $ver2 = explode("-", $datos['fecha_corte_sicflix']);
        $mesC = $ver[0].'-'.$ver[1];
        $mesF = $ver2[0].'-'.$ver2[1];
        $date1 = new DateTime($Fecha_Hoy);
        $date2 = new DateTime($corteInt['fecha']);

        //Le restamos a la fecha date1-date2
        $diff = $date1->diff($date2);
        $Dias_pasaron= $diff->days;
        //Tengo que preguntar si aquí la cambio ref['contrato'] por ['sicflix']
        if ($mesA == $mesC and $mesA == $mesF and $datos['sicflix'] != 1) {
          //¿Cuanto descuento por día?¿Depende de cuánto sea el pago total dividido entre 30 dias?
          $xDia = ($mensualidad['precio_paquete']/30);
          $Descuento = $Dias_pasaron*$xDia;
          $Descuento = round($Descuento, 0, PHP_ROUND_HALF_DOWN);
        }
      }
    ?>

    <!-- CUADRO DE LOS DATOS DEL CLIENTE -->
    <div class="container">
      <h3 class="hide-on-med-and-down">Realizando pago del cliente Sicflix:</h3>
      <h5 class="hide-on-large-only">Realizando pago del cliente Sicflix:</h5>
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
          <b>Contraseña: </b><br>
          <b>Estatus: </b><br>
          <?php
          $color = "green";
          $Estatus = "Vigente";
          //ok aquí hay que preguntar cuándo se cambia la fecha de vencimiento
          if ($Hoy > $Vence) {
            $color = "red accent-4";
            $Estatus = "Vencido";
          }
          if ($datos['sicflix'] == 1) {
            ?>
          <b>Vencimiento de Plan: </b><?php echo $Vence;?><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $Estatus; ?></span><br>
          <?php } ?>
          </p> 
        </li>
      </ul>
    </div>

    <!-- CUADRO DEL FORMULARIO DE PAGO -->
    <div class="container">
      <h3 class="hide-on-med-and-down pink-text "><< Sicflix >></h3>
      <h5 class="hide-on-large-only  pink-text"><< Sicflix >></h5>
      <!-- ----------------------------  FORMULARIO CREAR PAGO  ---------------------------------------->
      <div class="row">
        <div class="col s12">
          <br>
          <div class="row">
            <form class="col s12" name="formMensualidad">
              <div class="row">
                <div class="col s6 m2 l2">
                  <p>
                    <br>
                    <!-- ----------------------------  CASILLA DE CALCULAR DIAS RESTANTES  ---------------------------------------->
                    <input id="totalizador" value="<?php echo htmlentities($mensualidad['precio_paquete']); ?>" type="hidden">
                    <input type="checkbox" onclick="resto_dias();total_cantidad();" id="resto"/>
                    <label for="resto">Calcular días restantes</label>
                  </p>
                </div>
                <!-- ----------------------------  VARIABLES DE USUARIOS CON ACCESO  ---------------------------------------->
                <?php 
                  $Ser = (in_array($user_id, array(10, 101, 103, 105, 49, 84, 106, 39)))? '': 'disabled="disabled"';
                  $Ser2 = (in_array($user_id, array(10, 101, 103, 105, 49, 106)))? '': 'disabled="disabled"';
                ?>
                <!-- ----------------------------  CASILLA DE BANCO  ---------------------------------------->
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="banco" <?php echo $Ser;?>/>
                    <label for="banco">Banco</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA DE SAN  ---------------------------------------->
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="san" <?php echo $Ser2;?>/>
                    <label for="san">SAN</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA DE REFERENCIA  ---------------------------------------->
                <div class="col s6 m2 l2">
                  <div class="input-field">
                    <input id="ref" type="text" class="validate" data-length="15" required value="">
                    <label for="ref">Referencia:</label>
                  </div>
                </div>
                <!-- ----------------------------  CASILLA DE CREDITO  ---------------------------------------->
                <div class="col s6 m2 l2">
                  <p>
                    <br>
                    <input type="checkbox" id="credito"/>
                    <label for="credito">Credito</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA PARA SELECCIONAR MES  ---------------------------------------->
                <div class="row col s8 m2 l2"><br>
                  <select id="mes" class="browser-default">
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
                  <select id="año" class="browser-default">
                    <option value="0" >Seleccione Año</option>       
                    <option value="2022" selected>2022</option>         
                    <option value="2023">2023</option>
                    <option value="2023">2024</option>          
                  </select>
                </div>
                <!-- ----------------------------  CASILLA DE RECARGO  ---------------------------------------->
                <div class="col s4 m2 l2">
                  <p>
                  <br>
                  <?php 
                  $estado="";
                  if ($datos['fecha_corte_sicflix']<$Fecha_Hoy) {
                    $estado = "checked";
                  } 
                  ?>
                  <input id="totalizador" value="<?php echo htmlentities($mensualidad['precio_paquete']); ?>" type="hidden">
                  <input onclick="total_cantidad();" type="checkbox" <?php echo ($datos['fecha_corte_sicflix']<$Fecha_Hoy)?"checked":"";?> id="recargo"/>
                  <label for="recargo">Recargo</label>
                  </p>
                </div>
                <!-- ----------------------------  CASILLA DE DESCUENTO  ---------------------------------------->
                <div class="row col s12 m2 l2">
                  <div class="input-field">
                    <i class="material-icons prefix">money_off</i>
                    <input id="descuento" type="number" class="validate" data-length="6" required value="<?php echo $Descuento;?>" onkeyup= 'total_cantidad();'>
                    <label for="descuento">Descuento ($ 0.00):</label>
                  </div>
                </div>
                <!-- ----------------------------  CASILLA DE TOTAL  ---------------------------------------->
                <div class="row col s12 m2 l2">
                  <h5 class="indigo-text" >TOTAL  <input class="col s11" type="" id="total" value="$<?php echo $mensualidad['precio_paquete'] ?>"></h5>
                </div>     
              </div>
              <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
              <input id="id_comunidad" value="<?php echo htmlentities($comunidad['id_comunidad']);?>" type="hidden">
              <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
            </form>
          <!-- ----------------------------  BOTON REGISTRAR PAGO  ----------------------------------------> 
          <a onclick="insert_pago(<?php echo ($datos['sicflix'] == 1 ) ? ($Fecha_Hoy > $Vence) ? 0:1 : 0; ?>);" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a>
        </div>
        <br>
      </div><!------------------ row FORMULARIO CREAR PAGO  -------------------------------------> 
    </div><!-------------------------  CONTAINER  -------------------------------------->
  </body><!-------------------------  BODY  -------------------------------------->
</main><!-------------------------  MAIN  -------------------------------------->       
</html>



<!-- ###########SCRIPTS PARA LAS FUNCIONES################# -->
<script>
  //FUNCIÓN TOTAL_CANTIDAD------------------------------------------>
  function total_cantidad(){
    var MensualidadAux = $("input#totalizador").val();
    var Mensualidad = parseInt(MensualidadAux);

    //document.formMensualidad.total.value = '$0.00';
    if (Mensualidad > 0) {
      Mostrar = Mensualidad;
      if (document.getElementById('recargo').checked==true) {
        //¿Cuánto aumenta el recargo?¿50?
        Mostrar = Mostrar+50;
        //$mensualidad = $mensualidad+50;
      }
      var DescuentoAux = $("input#descuento").val();
      var Descuento = parseInt(DescuentoAux);
      if (Descuento > 0) {
        Mostrar = Mostrar-Descuento;
      }
      document.formMensualidad.total.value = '$'+Mostrar;
      //document.formMensualidad.total.value = '$'+$mensualidad;
    }
  };
  //-----------------------------------------------------------------||

  //FUNCIÓN RESTO_DIAS------------------------------------------>
  function resto_dias(){
    var f = new Date();
    var dia = f.getDate();//Obtiene el número de días

    if(document.getElementById('resto').checked==true){
      M.toast({html: 'Calculando días restantes', classes: 'rounded'});
      var MensualidadAux = $("input#totalizador").val();
      var Mensualidad = parseInt(MensualidadAux);
      document.formMensualidad.descuento.value  = "";

      document.formMensualidad.descuento.value  = Math.round((Mensualidad/31)*dia);  
    }else{
      M.toast({html:"Calculando mensualidad", classes: "rounded"});
      var MensualidadAux = $("input#totalizador").val();
      var Mensualidad = parseInt(MensualidadAux);
      document.formMensualidad.descuento.value  = 0;
    }
  };
  //-----------------------------------------------------------------||

  //FUNCIÓN INSERT_PAGO------------------------------------------>
  function insert_pago(sicflix) {  
    textoTipo = "Mensualidad";
    var textoCantidad = $("select#cantidad").val();
    var textoMes = $("select#mes").val();
    var textoAño = $("select#año").val();
    var textoDescuento = $("input#descuento").val();
    var textoRef = $("input#ref").val();
    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = textoMes+" "+textoAño;
          
    var textoComunidad = $("input#id_comunidad").val();

    if (document.getElementById('recargo').checked==true) {
      textoCantidad = textoCantidad+50;
      textoDescripcion = textoDescripcion+ " + RECARGO (Reconexion o Pago Tardio)";
    }
    if (true) {}
    if (textoDescuento != 0) {
      textoDescripcion = textoDescripcion+" - Descuento: $"+textoDescuento;
    }

    if(document.getElementById('banco').checked==true){
      textoTipo_Campio = "Banco";
    }else if (document.getElementById('credito').checked==true) {
      textoTipo_Campio = "Credito";
    }else if (document.getElementById('san').checked==true) {
      textoTipo_Campio = "SAN";
    }else{
      textoTipo_Campio = "Efectivo";
    } 

    var textoIdCliente = $("input#id_cliente").val();
    var textoRespuesta = $("input#respuesta").val();

    if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoMes == 0) {
      M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
    }else if (textoAño == 0) {
      M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if ((document.getElementById('banco').checked==true || document.getElementById('san').checked==true) && textoRef == "") {
      M.toast({html: 'Los pagos en banco y san deben de llevar una referencia.', classes: 'rounded'});
    }else if (document.getElementById('banco').checked==false && document.getElementById('san').checked==false && textoRef != "") {
      M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
    }else {
      $.post("../php/insert_pago_sicflix.php" , { 
        valorTipo_Campio: textoTipo_Campio,
        valorTipo: textoTipo,
        valorCantidad: textoCantidad,
        valorDescripcion: textoDescripcion,
        valorIdCliente: textoIdCliente,
        valorDescuento: textoDescuento,
        //valorHasta: textoHasta,
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
