<?php
include('../php/conexion.php');
$id = $conn->real_escape_string($_POST['id']);
?>
<script>
	$(document).ready(function(){
	    $('#modalContrasena').modal();
	    $('#modalContrasena').modal('open'); 
	 });
</script>
<div id="modalContrasena" class="modal">
    <div class="modal-content row">
      <h5 class="red-text center"><b>¡Cambio de contraseña!</b></h5>
      <h6><b>Una vez realizado el cambio de contraseña la sesion sera cerrada</b></h6><br>
      <form class="row">
        <div class="row">
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">security</i>
            <input type="password" class="validate" required  id="contra_anterior" name="contra_anterior">
            <label for="contra_anterior">Contraseña Anterior</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">security</i>
            <input type="password" class="validate" required id="contra" name="contra">
            <label for="contra">Nueva Contraseña</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">security</i>
            <input type="password" class="validate" required id="repite_contra" name="repite_contra">
            <label for="repite_contra">Repite Contraseña</label>
          </div>
        </div>
        <a onclick="update_contra(<?php echo $id ?>);" class="btn waves-effect waves-light grey darken-3 right">Cambiar<i class="material-icons right">save</i></a>
        <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2 right">Cancelar<i class="material-icons right">close</i></a>
      </form>
    </div>
</div>