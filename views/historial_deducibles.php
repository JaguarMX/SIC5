<!DOCTYPE html>
<html>
<head>
	<title>SIC | Historial deducibles</title>
<?php 
include('fredyNav.php');

if (in_array($_SESSION['user_id'], array(10, 49, 101, 25, 28, 128, 105, 70)) == false) {
  ?>
  <script>  
    M.toast({html: "NO TIENES ACCESO!...", classes: "rounded"});
    setTimeout("location.href='home.php'", 1000);
  </script>
<?php }  ?>
<script>
  function buscarDeducible() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
      if (textoDe == "" || textoA == "") {
        M.toast({html:"Seleccione el rango de fechas.", classes: "rounded"});
      }else{
        $.post("../php/buscar_deducible.php", {
            valorDe: textoDe,
            valorA: textoA,
          }, function(mensaje) {
              $("#resultado_pagos").html(mensaje);
          }); 
      }
  };
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h4 class="hide-on-med-and-down">Historial de Deducibles</h4>
  			<h5 class="hide-on-large-only">Historial de Deducibles</h5><br>
        <h5 class="blue-text">Seleccione un rango de fechas y de click al botón buscar:</h5>
		</div><br>
        <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a"  type="date">
            </div>

            
            <br><br><br>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="buscarDeducible();">Buscar</button>
            </div>
        </div>
	    <div id="resultado_pagos">
	    </div>        
	</div>
</body>
</html>