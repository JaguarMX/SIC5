<html>
<head>
  <title>SIC | Formulario Incidencia</title>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
?>
<script>
  function insert_incidencia() {
    var textoComunidad = $("select#comunidad").val();
    var textoPrioridad = $("select#prioridad").val();
    var textoDescripcion = $("input#descripcion").val();
    var textoFechaTO = $("input#fecha_to").val();
    var textoHoraTO = $("input#hora_to").val();

    if (textoComunidad == 0) {
      M.toast({html: 'Seleccione una comunidad.', classes: 'rounded'});
    }else if(textoDescripcion == ""){
      M.toast({html: 'Escriba una descripción de la incidencia.', classes: 'rounded'});
    }else if(textoFechaTO == ""){
      M.toast({html: 'Seleccione una Fecha de Tiempo Ocurrido.', classes: 'rounded'});
    }else if(textoHoraTO == ""){
      M.toast({html: 'Seleccione una Hora de Tiempo Ocurrido.', classes: 'rounded'});
    }else if(textoPrioridad == 0){
      M.toast({html: 'Seleccione prioridad.', classes: 'rounded'});
    }else{
      M.toast({html: 'Creando incidencia...', classes: 'rounded'});
      $.post("../php/insert_incidencia.php", {
        valorComunidad: textoComunidad,
        valorDescripcion: textoDescripcion,
        valorPrioridad: textoPrioridad,
        valorFechaTO: textoFechaTO,
        valorHoraTO: textoHoraTO
      }, function(mensaje) {
          $("#resultado_in").html(mensaje);
      }); 
    }
  };
</script>
</head>
<main>
<body>
  <div class="container">
    <div class="row" >
        <h3 class="hide-on-med-and-down">Registrar Incidencia</h3>
        <h5 class="hide-on-large-only">Registrar Incidencia</h5>
    </div>
    <div id="resultado_in"></div>
     <div class="row">
      <form class="col s12">
        <div class="col s12 m6 l6">
          <br>
          <div class="input-field row">
            <i class="col s1"> <br></i>
            <select id="comunidad" class="browser-default col s10" >
              <option value="0" selected>Comunidad</option>
              <?php
              require('../php/conexion.php');
              $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
              while($com = mysqli_fetch_array($sql)){
                ?><option value="<?php echo $com['id_comunidad'];?>"><?php echo $com['nombre'].', '.$com['municipio'];?></option><?php
              } 
              ?>
            </select>
          </div>
          <div class="input-field">
            <i class="material-icons prefix">edit</i>
            <input id="descripcion" type="text" class="validate" data-length="50" required>
            <label for="descripcion">Descripción de la Incidencia:</label>
          </div> 
          <div class="input-field row">
            <i class="col s1"> <br></i>
            <select id="prioridad" class="browser-default col s10" >
              <option value="0" selected>Prioridad</option>
              <option value="Baja" >Baja</option>
              <option value="Media" >Media</option>
              <option value="Alta" >Alta</option>              
            </select>
          </div>    
        </div>
           <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
        <div class="col s12 m6 l6">
          <br>
          <div class="input-field">
            <input id="fecha_to" type="date">
            <label for="fecha_to">Fecha TO (Tiempo Ocurrido):</label>
          </div>
          <div class="input-field">
            <input type="time" id="hora_to">
            <label for="hora_to">Hora TO (Tiempo Ocurrido):</label>
          </div>
        </div>
      </form>
      <a onclick="insert_incidencia();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
    </div> 
  </div><br>
</body>
</main>
</html>
