<!DOCTYPE html>
<html>
<head>
	<title>SIC | Total Cortes</title>
<?php
include ('fredyNav.php');

if (in_array($_SESSION['user_id'], array(10, 49, 101, 25, 105)) == false) {
  ?>
  <script>    
    function regresacortes() {
      M.toast({html: "NO TIENES ACCESO!...", classes: "rounded"});
      setTimeout("location.href='home.php'", 1000);
    };
    regresacortes();
  </script>
<?php }  ?>
<script>
  function buscar_cortes() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
        $.post("../php/todos_cortes.php", {
            valorDe: textoDe,
            valorA: textoA,
          }, function(mensaje) {
              $("#resultado_cortes").html(mensaje);
          }); 
  };
  function imprimir_cortes() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
        $.post("../php/cortedeCortes.php", {
            valorDe: textoDe,
            valorA: textoA,
           
          }, function(mensaje) {
              $("#resultado_cortes").html(mensaje);
                window.open('../files/cortes/cortedeCortes.pdf', '_blank')
          }); 
  };
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down"> Cortes:</h3>
  			<h5 class="hide-on-large-only"> Cortes:</h5>
		</div><br><br>
        <div class="row">
            <div class="col s12 l5 m5">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l5 m5">
                <label for="fecha_a">A:</label>
                <input id="fecha_a"  type="date">
            </div>
            <br><br><br>
        </div>
        <div class="col s12 l12 m12">
                <button class="btn waves-light waves-effect right pink" onclick="buscar_cortes();"><i class="material-icons prefix">send</i></button>
            </div>
            <div class="col s12 l6 m6">
                <button class="btn waves-light waves-effect right pink" onclick="imprimir_cortes();"><i class="material-icons prefix">print</i></button>
            </div>
	    <div id="resultado_cortes">
	    </div>  
	</div>
</body>
</html>