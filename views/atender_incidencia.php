<!DOCTYPE html>
<html>
<head>
	<title>SIC | Atender Dispositivo</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a pendientes.", classes: "rounded"})
      setTimeout("location.href='pendientes.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id = $_POST['id'];
  $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM incidencias WHERE id = $id"));
  $id_comunidad = $datos['comunidad'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
  ?>
  <script>
    function mantenimiento(id){
      $.post("../php/pasar_mto.php", { 
        valorId: id
      }, function(mensaje) {
        $("#refrescar").html(mensaje);
      }); 
    }
    function terminar(id){
    	var textoObservacion = $("input#observacion_Inc").val();
        
      if (textoObservacion == "") {
        M.toast({html:"El campo de observacion no puede ir vacio.", classes: "rounded"});
      }else {
        $.post("../php/terminar_incidencias.php", {
          valorObservacion: textoObservacion,
          valorId: id
        }, function(mensaje) {
            $("#refrescar").html(mensaje);
        });  
      }
    };
  </script>
  <body>
  	<div class="container" id="refrescar">
  		<div class="row">
  	     <h3 class="hide-on-med-and-down">Atender Incidencia:</h3>
  	     <h5 class="hide-on-large-only">Atender Incidencia:</h5>
      </div>
      <div class="row">
     		<ul class="collection">
          <li class="collection-item avatar">
            <div class="hide-on-large-only"><br><br></div>
            <img src="../img/cliente.png" alt="" class="circle">
            <span class="title"><b>Folio: </b><?php echo $datos['id'];?></span>
            <p> 
              <b>Comunidad: </b><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?><br>
              <b>Fecha TO: </b><?php echo $datos['fecha_to'];?> - <b>Hora TO: </b><?php echo $datos['hora_to'];?><br>
              <b>Fecha TD: </b><?php echo $datos['fecha_td'];?> - <b>Hora TD: </b><?php echo $datos['hora_td'];?><br>
              <hr>
              <b>Descripción: </b><?php echo $datos['descripcion'];?>
            </p>
          </li>
        </ul>
      </div>
      <div class="row">	
      	<div class="row col s12">
      		<div class="col s12 m3 l3">
      			<h3 class="hide-on-med-and-down">Opciones:</h3>
       			<h5 class="hide-on-large-only">Opciones:</h5>
       		</div>
       		<form class="col s12 m9 l9"> <br>  
  		      <div class="input-field col s11 m6 l6">
  		        <i class="material-icons prefix">edit</i>
  		        <input id="observacion_Inc" type="text" class="validate" data-length="50" required>
  		        <label for="observacion_Inc">Observación:</label>
  		      </div><br>
            <a onclick="mantenimiento(<?php echo $datos['id'];?>);" class="waves-effect waves-light btn indigo col s6 m3 l3"><i class="material-icons right">list</i>Mantenimiento</a>  
  		      <a onclick="terminar(<?php echo $datos['id'];?>);" class="waves-effect waves-light btn pink col s6 m3 l3"><i class="material-icons right">check</i>Terminar</a>  
          </form>		
      	</div>    	
      </div>
  	</div>
  </body>
<?php
}
mysqli_close($conn);
?>
</html>