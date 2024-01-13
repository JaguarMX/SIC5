<html>
<head>
  <title>SIC | Pagos Central</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
if (isset($_POST['id_central']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a centrales.", classes: "rounded"});
    setTimeout("location.href='centrales.php'", 800);
  </script>
  <?php
}else{
$id_central = $_POST['id_central'];
?>
<script>   
function imprimir(id_pago){
  var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/imprimir.php?IdPago="+id_pago;
      a.click();
};
function borrar(IdPago){
    var textoIdCentral = $("input#id_central").val();
  $.post("../php/borrar_pago_central.php", { 
          valorIdPago: IdPago,
          valorIdCentral: textoIdCentral
  }, function(mensaje) {
  $("#mostrar_pagos").html(mensaje);
  }); 
};
function insert_pago() {  
    var textoCantidad = $("input#cantidad").val();
    var textoAño = $("select#año").val();
    var textoIdCentral = $("input#id_central").val();
    var textoVence = $("input#vence").val();

    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = "Anualidad de internet"+" "+textoAño;
    
    //if(document.getElementById('anual').checked==true){
      //textoTipo = "Anual";
    //}else if(document.getElementById('mensual').checked==true){
      //textoTipo = "Mensual";
   // }

    if (textoCantidad == "" || textoCantidad ==0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoAño == 0) {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if (textoVence == '') {
      M.toast({html: 'Elige una fecha de vencimiento.', classes: 'rounded'});
    }else {
        $.post("../php/control_centrales.php" , { 
            accion: 4,
            valorCantidad: textoCantidad,
            valorDescripcion: textoDescripcion,
            valorIdCentral: textoIdCentral,
            valorVence: textoVence,
          }, function(mensaje) {
              $("#mostrar_pagos").html(mensaje);
          });  
    }    
};

function insert_pagoRenta() {  
    var textoCantidad = $("input#cantidadRenta").val();
    var textoAño = $("select#selectRenta").val();
    var textoIdCentral = $("input#id_central").val();
    var textoVence = $("input#venceRenta").val();
    textoDescripcion = "Pago de renta"+" "+textoAño;
    if (textoCantidad == "" || textoCantidad ==0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoAño == 0) {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if (textoVence == '') {
      M.toast({html: 'Elige una fecha de vencimiento.', classes: 'rounded'});
    }else {
        $.post("../php/control_centrales.php" , { 
            accion: 5,
            valorCantidad: textoCantidad,
            valorDescripcion: textoDescripcion,
            valorIdCentral: textoIdCentral,
            valorVence: textoVence,
          }, function(mensaje) {
              $("#mostrar_pagos").html(mensaje);
          });  
    }    
};
function insert_pagoCfe() {  
    var textoCantidad = $("input#cantidadCfe").val();
    var textoAño = $("select#selectCfe").val();
    var textoBimestre = $("select#selectCfeBim").val();
    var textoIdCentral = $("input#id_central").val();
    textoDescripcion = "CFE"+" "+textoBimestre+" "+textoAño;
    if (textoCantidad == "" || textoCantidad ==0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoAño == 0) {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else {
        $.post("../php/control_centrales.php" , { 
            accion: 6,
            valorCantidad: textoCantidad,
            valorDescripcion: textoDescripcion,
            valorIdCentral: textoIdCentral,
          }, function(mensaje) {
              $("#mostrar_pagos").html(mensaje);
          });  
    }    
};
</script>
<body>
<style>
  .tabs .tab a{
            color:#000;
        } /*Black color to the text*/

        .tabs .tab a:hover {
            background-color:#eee;
            color:#000;
        } /*Text color on hover*/

        .tabs .tab a.active {
            background-color:#888;
            color:#000;
        } /*Background and text color when a tab is active*/

        .tabs .indicator {
            background-color:#000;
        } /*Color of underline*/
</style>  
<?php
$sql = "SELECT * FROM centrales WHERE id=$id_central";
$datos = mysqli_fetch_array(mysqli_query($conn, $sql));
$id_comunidad = $datos['comunidad'];
$id_paquete = $datos['paqueteInternet'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
$paqueteInternet = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$id_paquete'"));
if ($datos['paqueteInternet'] !=0) {
  $paqueteInternetTab = "disabled";
  $totalPaqueteInternet = $paqueteInternet['mensualidad'] * 12;
}else{
  $paqueteInternetTab = "disabled";
  $totalPaqueteInternet = 0;
}
if ($datos['montoRenta'] !=0) {
  $rentaTab = "";
  $totalRenta = $datos['montoRenta'] * 12;
}else{
  $rentaTab = "disabled";
  $totalRenta = 0;
}
if ($datos['pagoCfe'] !=0) {
  $cfeTab = "";
}else{
  $cfeTab= "disabled";
}

?>
<div class="container">
  <h3 class="hide-on-med-and-down">Realizando pago de la central:</h3>
  <h5 class="hide-on-large-only">Realizando pago de la central:</h5>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title"><b>No. Central: </b><?php echo $datos['id'];?></span>
      <p><b>Encargado: </b><?php echo $datos['nombre'];?><br>
         <b>Telefono: </b><?php echo $datos['telefono'];?><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?>, <b>Municipio:</b> <?php echo $comunidad['municipio'];?><br>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Coordenada: </b><?php echo $datos['coordenadas'];?><br>
         <b class="red-text">Fecha Vencimineto de Renta: <?php echo $datos['vencimiento_renta'];?></b><br>
         <br>
      </p>
    </li>
  </ul> 
<div id="imprimir"></div><br>
<div class="row">
  <div class="col s12">
    <div class="row">
      <a href="equipos.php?id=<?php echo $datos['id'];?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>VER EQUIPOS</a>
        
<!-- ----------------------------  PAGOS   ---------------------------------------->
     
    <div class="col s12 m12 l12"><br>
      <ul class="tabs">
        <li class="tab col s4 <?php echo $paqueteInternetTab;?>"><a href="#internetTab">Internet</a></li>
        <li class="tab col s4 <?php echo $rentaTab;?>"><a href="#rentaTab">Renta</a></li>
        <li class="tab col s4 <?php echo $cfeTab;?>"><a href="#cfeTab">Recibo CFE</a></li>
      </ul>
    </div>
    <div id="internetTab" class="col s12 m12 l12">
    <div class="col s12 m12 l12">
        <form class="row" name="formMensualidad"><br><br>
          <div class="input-field col s4 m4 l4">
            <i class="material-icons prefix">payment</i>
            <input disabled id="cantidad" type="number" class="validate" data-length="6" value=<?php echo $totalPaqueteInternet;?> required>
            <label for="cantidad">Cantidad :</label>
          </div>
          
          <div class="row col s4 m4 l4"><br>
            <select id="año" class="browser-default">
              <option value="0" selected>Seleccione Año</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2024">2025</option>
            </select>
          </div>
          
          <div class="input-field col s4 m4 l4">
            <p>
              <input type="date" id="vence"/>
              <label for="vence">Fecha Vencimiento:</label>
            </p>
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id']);?>" type="hidden">
        </form>
        <a onclick="insert_pago();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a><br>
    </div>
    </div>
    <div id="rentaTab" class="col s12 m12 l12">

    <form class="row" name="formRenta"><br><br>
          <div class="input-field col s4 m4 l4">
            <i class="material-icons prefix">payment</i>
            <input disabled id="cantidadRenta" type="number" class="validate" data-length="6" value=<?php echo $totalRenta;?> required>
            <label for="cantidadRenta">Cantidad :</label>
          </div>
          
          <div class="row col s4 m4 l4"><br>
            <select id="selectRenta" class="browser-default">
              <option value="0" selected>Seleccione Año</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2024">2025</option>
            </select>
          </div>
          
          <div class="input-field col s4 m4 l4">
            <p>
              <input type="date" id="venceRenta"/>
              <label for="venceRenta">Fecha Vencimiento:</label>
            </p>
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id']);?>" type="hidden">
        </form>
        <a onclick="insert_pagoRenta();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a><br>
    </div>


    </div>
    <div id="cfeTab" class="col s12">
    <form class="row" name="formCfe"><br><br>
          <div class="input-field col s4 m4 l4">
            <i class="material-icons prefix">payment</i>
            <input id="cantidadCfe" type="number" class="validate" data-length="6" value="0" required>
            <label for="cantidadCfe">Cantidad :</label>
          </div>
          
          <div class="row col s4 m4 l4"><br>
            <select id="selectCfe" class="browser-default">
              <option value="0" selected>Seleccione Año</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2024">2025</option>
            </select>
          </div>
          
          <div class="input-field col s4 m4 l4">
          <select id="selectCfeBim" class="browser-default">
              <option value="0" selected>Seleccione el bimestre</option>
              <option value="Enero-Febrero">Enero-Febrero</option>
              <option value="Marzo-Abril">Marzo-Abril</option>
              <option value="Mayo-Junio">Mayo-Junio</option>
              <option value="Julio-Agosto">Julio-Agosto</option>
              <option value="Septiembre-Octubre">Septiembre-Octubre</option>
              <option value="Noviembre-Diciembre">Noviembre-Diciembre</option>
            </select>
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id']);?>" type="hidden">
        </form>
        <a onclick="insert_pagoCfe();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a><br>
    </div>
    </div>

        
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
              <th>Imprimir</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_pagos = "SELECT * FROM pagos_centrales WHERE id_central = ".$datos['id']." ORDER BY id DESC";
          $resultado_pagos = mysqli_query($conn, $sql_pagos);
          $aux = mysqli_num_rows($resultado_pagos);
          if($aux>0){
          while($pagos = mysqli_fetch_array($resultado_pagos)){
            $id_user = $pagos['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td>$<?php echo $pagos['cantidad'];?></td>
              <td><?php echo $pagos['tipo'];?></td>
              <td><?php echo $pagos['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo $pagos['fecha'];?></td>
              <td><a href = "../php/ticket_central.php?id=<?php echo $pagos['id'];?>" target = "blank" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
              <td><a onclick="borrar(<?php echo $pagos['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado pagos</h5 ></b></center>";
          }
          ?> 
          </tbody>
        </table>
        </div>
      </div>

    </div>
  </div>
</div>

</div><!----------------CONTAINER----------------->
</body>
<?php } ?>
</html>