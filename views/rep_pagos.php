<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/conexion.php');
  include ('../php/cobrador.php');
  include ('../php/superAdmin.php');
?>
<title>SIC | Reporte Pagos</title>
<script>
  function buscar_pagos_tipoCambio(){
    var fechaInicio = $("input#fecha_de3").val();
    var fechaFinal = $("input#fecha_a3").val();
    var tipoPago = $("select#tipo3").val();
    var banco = $("select#select_bancos").val();
    $.post("../php/control_reporte_pagos.php", {
            accion : 3,
            fechaInicio: fechaInicio,
            fechaFinal: fechaFinal,
            tipoPago: tipoPago,
            banco: banco,
          }, function(mensaje) {
              $("#resultado_pagos").html(mensaje);
        }); 
        
  } 


function buscar_pagos(tipo) {
  entra = "Si";
  if (tipo == 1) {
    var textoDe = $("input#fecha_de2").val();
    var textoA = $("input#fecha_a2").val();
    var textoTipo = $("select#tipo").val();
    var textoUsuario = $("select#usuario2").val();
    var textoBanco = $("select#select_bancos").val();
    if (textoUsuario == "") {
      M.toast({html:"Seleccione un usuario.", classes: "rounded"});      
      entra = "No";    
    }  
    if (textoTipo == "") {
      M.toast({html:"Seleccione un tipo de cambio.", classes: "rounded"});  
      entra = "No";    
    }
   
  }else if (tipo == 2 ) {
    var textoDe = $("input#fecha_de3").val();
    var textoA = $("input#fecha_a3").val();
    var textoTipo = $("select#tipo3").val();
    var textoUsuario = $("select#usuario2").val();
    var textoBanco = $("select#select_bancos").val();
    if (textoTipo == "") {
      M.toast({html:"Seleccione un tipo de cambio.", classes: "rounded"});  
      entra = "No";    
    }

  }else if (tipo == 0 ){
    textoTipo = "";
    var textoDe = $("input#fecha_de").val();
    var textoA = $("input#fecha_a").val();
    var textoUsuario = $("select#usuario").val();
    if (textoUsuario == "") {
      M.toast({html:"Seleccione un usuario.", classes: "rounded"});      
      entra = "No";    
    }    
  }
  if (tipo == 3) {
    entra = "No"; 
    var textoDe = $("input#fecha_de4").val();
    var textoA = $("input#fecha_a4").val();
    if (textoDe == "" || textoA == ""){
    M.toast({html:"Ingrese un rango de fechas.", classes: "rounded"});
    }else {
        $.post("../php/buscar_pagos_borrados.php", {
            valorDe: textoDe,
            valorA: textoA,
          }, function(mensaje) {
              $("#resultado_pagos").html(mensaje);
        }); 
    }
  }else{
    if (textoDe == "" || textoA == ""){
      M.toast({html:"Ingrese un rango de fechas.", classes: "rounded"});
    }else if (entra == "Si") {
      
        $.post("../php/buscar_pagos.php", {
            valorDe: textoDe,
            valorA: textoA,
            valorUsuario: textoUsuario,
            valorTipo: textoTipo,
            valorBanco: textoBanco
          }, function(mensaje) {
              $("#resultado_pagos").html(mensaje);
        }); 
    }
  }
  
};

  function esconder_select_bancos() {
    document.getElementById("select_bancos").style.display = "none";
  } 

  $(document).ready(function(){
    $('#tipo3').on('change', function(){
        var tipoPago2 = $(this).val();
        if(tipoPago2 == 'Banco'){
          document.getElementById("select_bancos").style.display = "block";
        }else{
          document.getElementById("select_bancos").style.display = "none";   
        }
    });
});
</script>
</head>
<main>
<body onload="esconder_select_bancos()">
	<div class="container">
    <br>
    <h3 class="hide-on-med-and-down">Reporte de pagos</h3>
    <h5 class="hide-on-large-only">Reporte de pagos</h5><br>
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
    <div class="row">
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s3"><a class="active black-text" href="#test-swipe-1">Por Usuarios</a></li>
          <li class="tab col s3"><a class="black-text" href="#test-swipe-2">Por Tipo de Cambio Y USUARIO</a></li>
          <li class="tab col s3"><a class="black-text" href="#test-swipe-3">Por Tipo de Cambio</a></li>
          <li class="tab col s3"><a class="black-text" href="#test-swipe-4">Borrados</a></li>
        </ul>
      </div>
      <br><br><br><br>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
        <div  id="test-swipe-1" class="col s12">
          <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a"  type="date">
            </div>

            <div class="input-field col s12 l4 m4">
              <select id="usuario" class="browser-default">
                <option value="" selected>Seleccione un usuario</option>
                <?php 
                $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE estatus = 1");
                while($tecnico = mysqli_fetch_array($sql_tecnico)){
                  ?>
                    <option value="<?php echo $tecnico['user_id'];?>"><?php echo $tecnico['user_name'];?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            <br><br><br>
            <div>
              <button class="btn waves-light waves-effect right pink" onclick="buscar_pagos(0);"><i class="material-icons prefix right">search</i> Buscar</button>
            </div>
          </div>
        </div>
        <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
        <div  id="test-swipe-2" class="col s12">
          <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de2">De:</label>
                <input id="fecha_de2" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a2">A:</label>
                <input id="fecha_a2"  type="date">
            </div>
            <div class="input-field col s12 l2 m2">
              <select id="tipo" class="browser-default">
                <option value="" selected>Seleccione un tipo:</option>
                <option value="Banco">BANCO</option>
                <option value="Efectivo">EFECTIVO</option>
                <option value="Credito">CREDITO</option>
                <option value="SAN">SAN</option>
              </select>
            </div>
            <div class="input-field col s12 l2 m2">
              <select id="usuario2" class="browser-default">
                <option value="" selected>Seleccione un usuario</option>
                <?php 
                $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE estatus = 1");
                while($tecnico = mysqli_fetch_array($sql_tecnico)){
                  ?>
                    <option value="<?php echo $tecnico['user_id'];?>"><?php echo $tecnico['user_name'];?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            <br><br><br>
            <div>
              <button class="btn waves-light waves-effect right pink" onclick="buscar_pagos(1);"><i class="material-icons prefix right">search</i> Buscar</button>
            </div>
          </div>
        </div>
        <!-- ----------------------------  FORMULARIO 3 Tabs  ---------------------------------------->
        <div  id="test-swipe-3" class="col s12">
          <div class="row">
            <div class="col s12 l6 m6">
                <label for="fecha_de3">De:</label>
                <input id="fecha_de3" type="date">    
            </div>
            <div class="col s12 l6 m6">
                <label for="fecha_a3">A:</label>
                <input id="fecha_a3"  type="date">
            </div>
            <div class="input-field col s12 l6 m6">
              <select id="tipo3" class="browser-default">
                <option value="" selected>Seleccione un tipo:</option>
                <option value="Banco">BANCO</option>
                <option value="Efectivo">EFECTIVO</option>
                <option value="Credito">CREDITO</option>
                <option value="SAN">SAN</option>
              </select>
            </div>
            <div class="input-field col s12 l6 m6">
              <select id="select_bancos" class="browser-default">
                <option value="0" selected>Seleccione un banco:</option>
                <option value="BBVA">BBVA</option>
                <option value="BANORTE">BANORTE</option>
                <option value="HSBC">HSBC</option>
                <option value="">Sin banco asignado</option>
                <option value="1">Sin referencia</option>
              </select>
            </div>
            
            <div class="col s12 l12 m12">
              <button class="btn waves-light waves-effect right pink" onclick="buscar_pagos_tipoCambio();"><i class="material-icons prefix right">search</i> Buscar</button>
            </div>
          </div>
        </div>
        <!-- ----------------------------  FORMULARIO 4 Tabs  ---------------------------------------->
        <div  id="test-swipe-4" class="col s12">
          <div class="row">
            <div class="col s12 l5 m5">
                <label for="fecha_de4">De:</label>
                <input id="fecha_de4" type="date">    
            </div>
            <div class="col s12 l5 m5">
                <label for="fecha_a4">A:</label>
                <input id="fecha_a4"  type="date">
            </div>
            <br><br><br>
            <div>
              <button class="btn waves-light waves-effect right pink" onclick="buscar_pagos(3);"><i class="material-icons prefix right">search</i> Buscar</button>
            </div>
          </div>
        </div>
    <div id="resultado_pagos">
    </div>        
    </div>
</body>
</main>
</html>