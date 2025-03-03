<html>
<head>
  <title>SIC | Reporte</title>
<?php 
include('fredyNav.php');
?>
<script>
  function showContent() {
    //Elementos que muestran los inputs requeridos
    element = document.getElementById("content");
    element2 = document.getElementById("content2");
    element3 = document.getElementById("content3");
    var textoReporte = $("select#reporte").val();

    if (textoReporte == 'Cambio De Domicilio') {
      element.style.display='block';content3
    }
    else {
      element.style.display='none';
    }
    if (textoReporte == 'Cambio De Contraseña') {
      element2.style.display='block';
    }
    else {
      element2.style.display='none';
    }
    //Sí se selecciona Activar Sicflix va a traer el elment3
    if (textoReporte == 'Activar Sicflix') {
      element3.style.display='block';
    }
    else {
      element3.style.display='none';
    }        
  };

  function verificar_reporte() {
    
    var textoNombre = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenadas = $("input#coordenadas").val();
    var textoReporte = $("select#reporte").val();
    var textoIdCliente = $("input#id_cliente").val();
    var textoPaquete = $("select#cambio3").val();
    //OPCION DE ACTIVACION DE SICFLIX NOS MADA A UNA NUEVA PESTAÑA
    if (textoReporte == 'Activar Sicflix'){
      //SI NO SE SELECCIONA NINGUN PAQUETE MANDA EL SIGUIENTE MENSAJE
      if (textoPaquete == "0"){
        M.toast({html:"Elige un tipo de paquete para el reporte SICFLIX.", classes: "rounded"})
      }else{
        //Inicio para mandar variables Reporte Sicflix
        $.post("modal_reporte_sicflix.php", {
          valorNombre: textoNombre,
          valorTelefono: textoTelefono,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenadas,
          valorReporte: textoReporte,
          valorIdCliente: textoIdCliente,
          valorPaquete: textoPaquete
          },function(mensaje) {
            $("#Continuar").html(mensaje);
          });
        //Final para mandar variables Reporte Sicflix
        //Sí no entonces continua...
      }//FIN ELSE SELECCIONAR TIPO DE PAQUETE
    }else{
      if (textoReporte == 'Cambio De Domicilio') {
        var textoCambio = $("input#cambio").val();
      if (textoCambio == '') {
        No = 'No';
        text = 'Colocar el domicilio nuevo.';
      }else{
        No = 'Si';
        textoDescripcion = textoReporte+': '+textoCambio;
      }
      }else if (textoReporte == 'Cambio De Contraseña') {
        var textoCambio = $("input#cambio2").val();
      if (textoCambio.length < 8) {
        No = 'No';
        text = 'La Contraseña debe de ser minimo de 8 caracteres.';
      }else{
        No = 'Si';
        textoDescripcion = textoReporte+' A: '+textoCambio;
      }
      }else{
        No ='Si';
        textoDescripcion = textoReporte;
      }
      if(document.getElementById('otros').checked==true){
        textoMas = $("input#mas").val();
        if (textoMas == '') {
          Entra = 'No';
        }else{
          Entra = 'Si';
          textoDescripcion = textoMas;

          if (textoIdCliente > 10000) {
            if(document.getElementById('mantenimiento').checked==true){
              textoDescripcion = 'Mantenimiento: '+textoMas;
            }
            if(document.getElementById('especial').checked==true){
              textoDescripcion = 'Reporte Especial: '+textoMas;
            }
          }
        }
      }else{
        Entra = 'Si';
      }

      if(document.getElementById('otros').checked==false && textoReporte == 0){
        M.toast({html:"Elige una opcion de reporte.", classes: "rounded"})
      }else if(Entra == "No"){
        M.toast({html:"Especifique el reporte !", classes: "rounded"})
      }else if((textoTelefono.length) < 10){
        M.toast({html:"Ingrese un numero de Telefono valido", classes: "rounded"})
      }else if(No == "No"){
        M.toast({html:""+text, classes: "rounded"})
      }else{
        //
        $.post("modal_rep.php", {
          valorNombre: textoNombre,
          valorTelefono: textoTelefono,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenadas,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente 
        },function(mensaje) {
          $("#Continuar").html(mensaje);
        });
      }
    }
  };
</script>

</head>
<main>
<?php
require('../php/conexion.php');

if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a clientes.", classes: "rounded"})
      setTimeout("location.href='clientes.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$no_cliente = $_POST['no_cliente'];
$sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$no_cliente");
$filas = mysqli_num_rows($sql);
if ($filas == 0) {
  $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$no_cliente");
}
$datos = mysqli_fetch_array($sql);

//Sacamos la Comunidad
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
?>
<script>
  function irconsumo(){  
    textoIdCliente = <?php echo $no_cliente; ?>;
    $.post("../php/ir_consumo.php", { 
      valorCliente:textoIdCliente,
    }, function(mensaje) {
    $("#consumo_ir").html(mensaje);
    }); 
  };
</script>
<body>
  <div id="consumo_ir"></div>
  <div class="container row" id="Continuar" >
  <div class="row" >
      <h3 class="hide-on-med-and-down">Creando Reporte para el cliente:</h3>
      <h5 class="hide-on-large-only">Creando Reporte para el cliente:</h5>
      </div>
  <div id="resultado_insert_pago">
  </div>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title">
        <b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
          <br>
         <div class="col s12"><br>
          <b class="col s4 m2 l2">Nombre(s):  </b>
          <div class="col s12 m9 l9">
          <input id="nombres" type="text" class="validate" value="<?php echo $datos['nombre'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Telefono:  </b>
          <div class="col s12 m9 l9">
          <input id="telefono" type="text" class="validate" value="<?php echo $datos['telefono'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Direccion: </b>
          <div class="col s12 m9 l9">
          <input id="direccion" type="text" class="validate" value="<?php echo $datos['direccion'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Referencia: </b>
          <div class="col s12 m9 l9">
            <input id="referencia" type="text" class="validate" value="<?php echo $datos['referencia'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Coordenadas: </b>
          <div class="col s12 m9 l9">
            <input id="coordenadas" type="text" class="validate" value="<?php echo $datos['coordenadas'];?>">
          </div>
         </div><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <b>Fecha de Instalación: </b><?php echo $datos['fecha_instalacion'];?><a onclick="irconsumo();" class="waves-effect waves-light btn pink right"><i class="material-icons right">equalizer</i>CONSUMO</a><br>
         <?php
         if ($datos['id_cliente'] < 10000) {
          $Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT descripcion FROM pagos WHERE id_cliente = '$no_cliente'  AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1"));
          //Separamos el stringv
          if ($datos['servicio'] == 'Internet y Telefonia' OR $datos['servicio'] == 'Internet') {
          date_default_timezone_set('America/Mexico_City');
          $mes_actual = date('Y-m');

          if ($Pago != "") {
            $ver = explode(" ", $Pago['descripcion']);
            $array =  array('ENERO' => '01','FEBRERO' => '02', 'MARZO' => '03','ABRIL' => '04', 'MAYO' => '05', 'JUNIO' => '06', 'JULIO' => '07', 'AGOSTO' => '08', 'SEPTIEMBRE' => '09', 'OCTUBRE' => '10', 'NOVIEMBRE' => '11',  'DICIEMBRE' => '12');
            $fecha_pago = date($ver[1].'-'.$array[$ver[0]]);
            if ($fecha_pago >= $mes_actual) {

              $color = "green";
              $MSJ = "AL-CORRIENTE";
            }else{
              $color = "red darken-3";
              $MSJ = "DEUDOR !";
            }
         ?>
         <a href="#!" class="secondary-content"><span class="new badge <?php echo $color;?>" data-badge-caption="<?php echo $MSJ;?>"></span></a>
         <?php
         }
        }
        if ($datos['servicio'] == 'Internet y Telefonia' OR $datos['servicio'] == 'Telefonia') {
         if ($datos['tel_cortado'] == 0) {
           $estado = "ACTIVO";
           $col = "green";
         }else{
           $estado = "CORTADO";
           $col = "red";
         }
         ?>
         <b>Extención:  <?php echo $datos['tel_servicio'];?></b><br>
         <b>Telefono:  <a class="<?php echo $col;?>-text"><?php echo $estado;?></a></b><br>
          <?php  
        }
      }?>
      </p>
    </li>
  </ul>

  <div class="row">
    <div class="col s12">
      <form class="col s12" name="formMensualidad">
      <br>
      <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="reporte" class="browser-default col s12 m4 l4" required onchange="javascript:showContent()">
            <option value="0" selected >Opciones:</option>
            <option value="No Tiene Internet" >No Tiene Internet</option>
            <option value="Internet Intermitente" >Internet Intermitente</option>
            <option value="Internet Lento" >Internet Lento</option>
            <option value="Cambio De Domicilio" >Cambio De Domicilio</option>
            <option value="Cambio De Contraseña" >Cambio De Contraseña</option>
            <option value="Activar Sicflix" >Activar Sicflix</option>
          </select>
          <div class="input-field col s12 m6 l6" id="content" style="display: none;">
            <input id="cambio" type="text" class="validate" data-length="100" required>
            <label for="cambio">Referencia (Casa de color blanco, dos pisos cercas de la iglesia):</label>
        </div>
        <div class="input-field col s10 m5 l5" id="content2" style="display: none;">
            <input id="cambio2" type="text" class="validate" data-length="100" required>
            <label for="cambio2">Contraseña (Minimos 8 caracteres):</label>
        </div>

        <!-- Select que aparece cuando se selecciona Activar Sicflix -->
        <div class="input-field col s10 m5 l5" id="content3" style="display: none;">
          <!-- <select id="cambio3" class="browser-default col s12 m8 l8" required onchange="javascript:showContent()"> -->
          <select id="cambio3" class="browser-default col s12 m8 l8" required onchange="javascript:showContent()">
            <option value="0" selected >Seleccione paquete:</option>
            <option value="Básico" >Básico $60</option>
            <option value="Premium" >Premium $100</option>
          </select>
        </div>
      </div>
      <div class="row">
        <?php if ($datos['id_cliente']<10000) {
        ?>
        <div class="col s1">
          <br>
        </div>
        <?php } ?>
        <div class="col s3 m2 l2">
          <p><br>
            <input type="checkbox" id="otros"/>
            <label for="otros">Otra Opción</label>
          </p>
        </div>
        <div class="input-field col s8 m6 l6">
            <input id="mas" type="text" class="validate" data-length="100" required>
            <label for="mas">Especifica (ej: Revicion de camaras, Aumentar paquete, etc.):</label>
        </div>
        <?php if ($datos['id_cliente']>=10000) {
        ?>
        <div class="col s4 m2 l2">
          <p><br>
            <input type="checkbox" id="mantenimiento"/>
            <label for="mantenimiento">Mantenimiento</label>
          </p>
        </div>
        <div class="col s4 m2 l2">
          <p><br>
            <input type="checkbox" id="especial"/>
            <label for="especial">Especial</label>
          </p>
        </div>
      <?php } ?>
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
    </form>
    <a onclick="verificar_reporte();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Reporte</a>
    </div>
  </div>

<h4>Historial Reportes</h4>
  <div id="mostrar_pagos">
    <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Descripción</th>
        <th>Ultima Modificación</th>
        <th>Solución</th>
        <th>Técnico</th>
        <th>Estatus</th>
      </tr>
    </thead>
    <tbody>
<?php
$sql_pagos = "SELECT * FROM reportes WHERE id_cliente = ".$datos['id_cliente']." ORDER BY id_reporte DESC";
$resultado_pagos = mysqli_query($conn, $sql_pagos);
$aux = mysqli_num_rows($resultado_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($resultado_pagos)){
  $id_tecnico = $pagos['tecnico'];
  $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_tecnico'"));
  if($pagos['atendido']==1){
    $atendido = '<span class="green new badge" data-badge-caption="Atendido">';
  }else if($pagos['atendido']==2){
    $atendido = '<span class="yellow darken-3 new badge" data-badge-caption="EnProceso">';
  }else{
    $atendido = '<span class="red new badge" data-badge-caption="Revisar">';
  }
  ?>
  <tr>
    <td><b><?php echo $aux;?></b></td>
    <td><?php echo $pagos['fecha'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $pagos['fecha_solucion'];?></td>
    <td><?php echo $pagos['solucion'];?></td>
    <td><?php echo $tecnico['user_name'];?></td>
    <td><?php echo $atendido;?></td>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h3>Este cliente aún no ha registrado reportes</h3></b></center>";
}
?> 
        </tbody>
      </table>
  </div>
<br>
<?php 




$id_user = $_SESSION['user_id'];
include('../php/conexion.php');
//<<<<<<<<<<<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>>>>>>>>>>>//
//CONDICIONES PARA LA DESACTIVACIÓN AUTOMÁTICA DE REPORTES SICFLIX
$Fecha_hoy = date('Y-m-d');
//Aquí se declara una variable para tomar la informacion de la tabla reporte_sicflix
$sql11 = "SELECT * FROM reporte_sicflix";
$consulta = mysqli_query($conn, $sql11);
//Obtiene la cantidad de filas que hay en la consulta
$filas11 = mysqli_num_rows($consulta);
//Si no existe ninguna fila que sea igual a $consulta, entonces mostramos el siguiente mensaje
if ($filas11 == 0) {
  echo '<script>M.toast({html:"No se encontraron clientes para dar de alta.", classes: "rounded"})</script>';
}else{
  //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
  while($resultados = mysqli_fetch_array($consulta)) {
    $id_cliente = $resultados['cliente'];
    $id_reporte = $resultados['id'];
    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
    $reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id=$id_reporte"));
    // SELECCIONAMOS EL ULTIMO REGISTRO PARA COMPROBAR CUAL FUE LA ÚLTIMA OPRACIÓN Y HACER  Ó NO UN NUEVO REPORTE
    $ultimo_resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reporte_sicflix WHERE cliente = $id_cliente ORDER BY id DESC LIMIT 1"));
            
    // SE EJECUTA LA CONDICIÓN AL COMPROBAR LA FECHA DE CORTE SICFLIX PARA ACTIVAR UN NUEVO REPORTE DE DESACTIVACIÓN -->
    if($cliente['fecha_corte_sicflix'] < $Fecha_hoy AND $cliente['fecha_corte_sicflix'] != date('0000-00-00') AND $cliente['fecha_corte_sicflix'] != date('2000-01-01')){
      // CONDICIÓN PARA EVITAR CICLAMINETOS
      //SÍ LA FECHA EN QUE SE REGISTRÓ EL NUEVO REPORTE DE ACTIVACION ES IGUAL A HOY, ENTONCES YA NO DEBE GENERAR REPORTE DE DESACTIVACION PARA EVITAR REPETICION CONSTANTE (en caso de vencimento de fecha)
      if($resultados['estatus'] != 0 AND $ultimo_resultado['descripcion'] != 'Desactivar Sicflix' AND $ultimo_resultado['estatus'] != 0 AND $ultimo_resultado['fecha_registro'] != $Fecha_hoy){
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
    }//FIN DE LA CONDICÓN DE DESACTIVACIÓN AUTOMATICA
  }//FIN WHILE
}//FIN DE LAS CONDICIONES DE ACTIVACIÓN Y DESACTIVACIÓN AUTOMÁTICA 


mysqli_close($conn);
?>
</div>
</body>
<?php 
}
?>
</main>
</html>
