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
<script>
//FUNCIÓN TOTAL_CANTIDAD------------------------------------------>
function total_cantidad(){
        var MensualidadAux = $("select#cantidad").val();
        var Mensualidad = parseInt(MensualidadAux);

        document.formMensualidad.total.value = '$0.00';
        if (Mensualidad > 0) {
          Mostrar = Mensualidad;
          if(document.getElementById('todos').checked==true){
            Mostrar = 10*Mensualidad;
          }
          if (document.getElementById('recargo').checked==true) {
            Mostrar = Mostrar+50;
          }
          var DescuentoAux = $("input#descuento").val();
          var Descuento = parseInt(DescuentoAux);
          if (Descuento > 0) {
            Mostrar = Mostrar-Descuento;
          }
          document.formMensualidad.total.value = '$'+Mostrar;
        }
      }
//FUNCIÓN IMPRIMIR------------------------------------------>
    function imprimir(id_pago){
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/imprimir.php?IdPago="+id_pago;
            a.click();
      };
//FUNCIÓN VERIFICAR_ELIMINAR------------------------------------------>
    function verificar_eliminar(IdPago){ 
        var textoIdCliente = $("input#id_cliente").val();  
        $.post("../php/verificar_eliminar_pago.php", {
              valorIdPago: IdPago,
              valorIdCliente: textoIdCliente,
            }, function(mensaje) {
                $("#modalBorrar").html(mensaje);
            }); 
       };
//FUNCIÓN RESTO_DIAS------------------------------------------>
    function resto_dias(){
        var f = new Date();
        var dia = f.getDate();

        if(document.getElementById('resto').checked==true){
          M.toast({html: 'Calculando días restantes', classes: 'rounded'});
          var MensualidadAux = $("select#cantidad").val();
          var Mensualidad = parseInt(MensualidadAux);
          document.formMensualidad.descuento.value  = "";

          document.formMensualidad.descuento.value  = Math.round((Mensualidad/31)*dia);  
        }else{
          M.toast({html:"Calculando mensualidad", classes: "rounded"});
          var MensualidadAux = $("select#cantidad").val();
          var Mensualidad = parseInt(MensualidadAux);
          document.formMensualidad.descuento.value  = 0;
        }
      };
//FUNCIÓN ENCENDER------------------------------------------>
      function encender(){
        if(document.getElementById('enciende').checked==true){
          textoOrden = "Encender";  
        }else{    
          textoOrden = "Apagar";
        }
        textoIdCliente = <?php echo $no_cliente; ?>;
        $.post("../php/enciende_apaga.php", { 
                valorOrden: textoOrden,
                valorCliente:textoIdCliente,
        }, function(mensaje) {
        $("#Orden").html(mensaje);
        });
    };
//FUNCIÓN INSERT_PAGO------------------------------------------>
function insert_pago(contrato) {  
          textoTipo = "Mensualidad";
          var textoCantidad = $("select#cantidad").val();
          var textoMes = $("select#mes").val();
          var textoAño = $("select#año").val();
          var textoDescuento = $("input#descuento").val();
          var textoHasta = $("input#hasta").val();
          var textoRef = $("input#ref").val();
    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = textoMes+" "+textoAño;
          
          var textoComunidad = $("input#id_comunidad").val();

          EsContrato = 'No';
          if ((textoComunidad == 92 || textoComunidad == 99) && contrato == 1) {
            if (textoCantidad < 310) {
              EsContrato = 'Si';
            }
          }else if ((textoCantidad < 400 && contrato == 1)) {
            EsContrato = 'Si';
          }
          var textoCantidad = parseInt(textoCantidad);

          if(document.getElementById('todos').checked==true){
            textoCantidad = 10*textoCantidad;
          }
          if (document.getElementById('recargo').checked==true) {
              textoCantidad = textoCantidad+50;
              textoDescripcion = textoDescripcion+ " + RECARGO (Reconexion o Pago Tardio)";
          }
          if (true) {}
          if (textoDescuento != 0) {
              textoDescripcion = textoDescripcion+" - Descuento: $"+textoDescuento;
          }

          if(document.getElementById('todos').checked==true){
            textoPromo = "si";
          }else{
            textoPromo = "no";
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
          }else if (EsContrato == 'Si') {
              M.toast({html: 'Un cliente por contrato debe pagar almenos 400 o 310 para colorada.', classes: 'rounded'});
          }else if (textoMes == 0) {
              M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
          }else if (textoAño == 0) {
              M.toast({html: 'Seleccione un año.', classes: 'rounded'});
          }else if ((document.getElementById('banco').checked==true || document.getElementById('san').checked==true) && textoRef == "") {
              M.toast({html: 'Los pagos en banco y san deben de llevar una referencia.', classes: 'rounded'});
          }else if (document.getElementById('banco').checked==false && document.getElementById('san').checked==false && textoRef != "") {
              M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
          }else {
              $.post("../php/insert_pago.php" , { 
                  valorPromo: textoPromo,
                  valorTipo_Campio: textoTipo_Campio,
                  valorTipo: textoTipo,
                  valorCantidad: textoCantidad,
                  valorDescripcion: textoDescripcion,
                  valorIdCliente: textoIdCliente,
                  valorDescuento: textoDescuento,
                  valorHasta: textoHasta,
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
<main>
<body onload="total_cantidad();">
  <?php
  $sql = "SELECT * FROM clientes WHERE id_cliente=$no_cliente";
  $datos = mysqli_fetch_array(mysqli_query($conn, $sql));
  //Sacamos la mensualidad
  $id_mensualidad=$datos['paquete'];
  $mensualidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$id_mensualidad'"));
  //Sacamos la Comunidad
  $id_comunidad = $datos['lugar'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
  //Sacamos la suma de todas las deudas y abonos...
  $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente='$no_cliente'"));
  $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $no_cliente AND tipo = 'Abono'"));
  //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACIOS
  if ($deuda['suma'] == "") {
    $deuda['suma'] = 0;
  }else if ($abono['suma'] == "") {
    $abono['suma'] = 0;
  }
  //SE HACE LA RESTA Y SI EL SALDO ES NEGATIVO CAMBIAMOS EL COLOR
  $Saldo = $abono['suma']-$deuda['suma'];
  $color1 = 'green';
  if ($Saldo < 0) {
    $color1 = 'red darken-2';
  }
  $Instalacion = $datos['fecha_instalacion'];
  $nuevafecha = strtotime('+12 months', strtotime($Instalacion));
  $Vence = date('Y-m-d', $nuevafecha);
  //VER CUANTOS DIAS HAN PASADO DESDE EL ULTIMO CORTE SOLO SI LA FECHA DE CORTE ES MENOR A HOY
  //PROBABLEMENTE AQUÍ HAY QUE HACER UNA NUEVA TABLA
  $Descuento = 0;
  $corteInt = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM int_cortes ORDER BY id DESC LIMIT 1"));
  if ($datos['fecha_corte'] < $Fecha_Hoy ) {
    $mesA = date('Y-m');
    $ver = explode("-", $corteInt['fecha']);
    $ver2 = explode("-", $datos['fecha_corte']);
    $mesC = $ver[0].'-'.$ver[1];
    $mesF = $ver2[0].'-'.$ver2[1];
    $date1 = new DateTime($Fecha_Hoy);
    $date2 = new DateTime($corteInt['fecha']);

    //Le restamos a la fecha date1-date2
    $diff = $date1->diff($date2);
    $Dias_pasaron= $diff->days;
    if ($mesA == $mesC and $mesA == $mesF and $datos['contrato'] != 1) {
       $xDia = $mensualidad['mensualidad']/30;
       $Descuento = $Dias_pasaron*$xDia;
       $Descuento = round($Descuento, 0, PHP_ROUND_HALF_DOWN);
    }
  }
  $user_id = $_SESSION['user_id'];
  $area = mysqli_fetch_array(mysqli_query($conn, "SELECT area FROM users WHERE user_id='$user_id'"));
  ?>
</main>
<div id="consumo_ir"></div>
  <div class="container">
    <h3 class="hide-on-med-and-down">Realizando pago del cliente Sicflix:</h3>
    <h5 class="hide-on-large-only">Realizando pago del Sicflix:</h5>
    <div id="Orden"></div>
    <div id="resultado_insert_pago"></div>
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
           <b>Fecha Corte Sicflix: </b><span id="corte"><?php echo $datos['fecha_corte'];?></span><br>
           <b>Contraseña: </b><br>
           <b>Estatus: </b><br>
           <?php
           $color = "green";
           $Estatus = "Vigente";
           if ($Hoy > $Vence) {
                $color = "red accent-4";
                $Estatus = "Vencido";
            }
           if ($datos['contrato'] == 1) {
            ?>
        <b>Vencimiento de Contrato: </b><?php echo $Vence;?><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $Estatus; ?></span><br>
           <?php } ?>
        </p> 
        </li>
    </ul>
    <div id="imprimir"></div><br>
    <h3 class="hide-on-med-and-down pink-text "><< Sicflix >></h3>
    <h5 class="hide-on-large-only  pink-text"><< Sicflix >></h5>
    <!-- ----------------------------  FORMULARIO CREAR PAGO  ---------------------------------------->
</html>
