<html>
<head>
  <title>SIC | Formulario Incidencia</title>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
?>
<script>
  function update_incidencia(id) {
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
      $.post("../php/update_incidencia.php", {
        valorID: id,
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
<?php
if (isset($_POST['id']) == false) {
  ?>
  <script>
    M.toast({html: "Regreando al listado...", classes: "rounded"});
    SetTimeout("location.href='incidencias.php'",1000);
  </script>
  <?php
}else{
  $id = $_POST['id'];
  $incidencia = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM incidencias WHERE id=$id"));
  $id_comunidad = $incidencia['comunidad'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
  ?>
  <body>
    <div class="container">
      <div class="row" >
          <h3 class="hide-on-med-and-down">Editar Incidencia</h3>
          <h5 class="hide-on-large-only">Editar Incidencia</h5>
      </div>
      <div id="resultado_in"></div>
       <div class="row">
        <form class="col s12">
          <div class="col s12 m6 l6">
            <br>
            <div class="input-field row">
              <i class="col s1"> <br></i>
              <select id="comunidad" class="browser-default col s10" >
                <option value="<?php echo $id_comunidad;?>" selected><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?></option>
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
              <input id="descripcion" type="text" class="validate" data-length="50" value="<?php echo $incidencia['descripcion'];?>">
              <label for="descripcion">Descripción de la Incidencia:</label>
            </div>  
            <div class="input-field row">
              <i class="col s1"> <br></i>
              <select id="prioridad" class="browser-default col s10" >
                <option value="<?php echo $incidencia['prioridad'];?>" selected><?php echo $incidencia['prioridad'];?></option>
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
              <input id="fecha_to" type="date" value="<?php echo $incidencia['fecha_to'];?>">
              <label for="fecha_to">Fecha TO (Tiempo Ocurrido):</label>
            </div>
            <div class="input-field">
              <input type="time" id="hora_to" value="<?php echo $incidencia['hora_to'];?>">
              <label for="hora_to">Hora TO (Tiempo Ocurrido):</label>
            </div>
          </div>
        </form>
        <a onclick="update_incidencia(<?php echo $id;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
      </div> 
    </div><br>
  </body>
  <?php
  }
?>
</main>
</html>
