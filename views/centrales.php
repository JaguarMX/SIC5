<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
  require('../php/conexion.php');
  $numeroCentrales = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM centrales;"));
?>
<script>
    function borrar(id){
      var answer = confirm("Deseas eliminar la central N°"+id+"?");
      if (answer) {
        $.post("../php/control_centrales.php", { 
          id: id,
          accion: 3,
        }, function(mensaje) {
          $("#borrar").html(mensaje);
        }); 
      }
    };
    //FUNCION QUE HACE LA BUSQUEDA DE CLIENTES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscar_centrales(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_centrales.php"
      $.post("../php/control_centrales.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 1,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_centrales.php"
            $("#centralesALL").html(mensaje);
      });//FIN post
    };//FIN function 
</script>
<title>SIC | Centrales</title>
</head>
<body onload="buscar_centrales();">
  <div class="container" >
    <div class="row" >
      <h3 class="hide-on-med-and-down col s12 m7 l7">Centrales registradas <?php echo $numeroCentrales['COUNT(*)'];?></h3>
      <h5 class="hide-on-large-only col s12 m7 l7">Centrales registradas <?php echo $numeroCentrales['COUNT(*)'];?></h5><br>
      <a href="form_central.php" class="waves-effect waves-light btn pink right">AGREGAR CENTRAL<i class="material-icons right">add</i></a>
      <form class="col s12 m7 l7 right">
        <div class="input-field col s12">
          <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_centrales();">
          <label for="busqueda">Buscar(Lugar,  Encargado)</label>
        </div>
      </form>
    </div>
    <div id="borrar"></div>
    <table class="bordered highlight">
      <thead>
        <tr>
        
          <th>#</th>
          <th>Comunidad</th>
          <th>Encargado</th>
          <th>Telefono</th>
          <th>Ver</th>
          <th>Editar</th>
          <th>Borrar</th>
          <th>Archivos</th>
        </tr>
      </thead>
      <!-- DENTRO DEL tbody COLOCAMOS id = "centralesALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_clientes() -->
      <tbody id="centralesALL">
      </tbody>
    </table><br>
  </div>
</body>
</html>