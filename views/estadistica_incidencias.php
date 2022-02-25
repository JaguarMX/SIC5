<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
?>
<title>SIC | Estadisticas Incidencias</title>
<script>
  function buscar_incidencias() {
    var textoDe = $("input#fecha_de").val();
    var textoA = $("input#fecha_a").val();
    $.post("../php/buscar_incidencias.php", {
      valorDe: textoDe,
      valorA: textoA
    }, function(mensaje) {
      $("#resultado_inc").html(mensaje);
    }); 
  };
</script>
</head>
<main>
<body>
	<div class="container"><br>
    <h3 class="hide-on-med-and-down">Estadisticas Incidencias</h3>
    <h5 class="hide-on-large-only">Estadisticas Incidencias</h5><br>
    <div class="row">
      <div class="col s12 l5 m5">
        <label for="fecha_de">De:</label>
        <input id="fecha_de" type="date" >    
      </div>
      <div class="col s12 l5 m5">
        <label for="fecha_a">A:</label>
        <input id="fecha_a" type="date" >
      </div><br>
      <div>
        <button class="btn waves-light waves-effect right pink" onclick="buscar_incidencias();"><i class="material-icons prefix right">send</i>BUSCAR</button>
      </div>
    </div>
    <div id="resultado_inc"></div>        
  </div>
</body>
</main>
</html>