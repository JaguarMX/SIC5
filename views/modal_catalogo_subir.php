<?php
include('../php/conexion.php');
?>
<script>
	$(document).ready(function(){
	    $('#SubirCatalogo').modal();
	    $('#SubirCatalogo').modal('open'); 
	 });
</script>
<div id="SubirCatalogo" class="modal">
    <div class="modal-content">
     <h5>Seleccionar Documento Nuevo PDF:</h5> 
     <h6 class="red-text"><b>ATENCION!! seleccionar un archivo PDF ya que solo acepta archivos con extención PDF</b></h6> 
      <form action="../php/subir_catalogo.php" method="post" enctype="multipart/form-data">
      <div class="input-field col s12 m6 l6">
          <div class="file-field input-field">
            <div class="btn">
              <span>SUBIR PDF</span>
              <input type="file" name="documento" id = "documento" required>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" placeholder="Subir Documento PDF">
            </div>
          </div>
      </div><br><br><br><br><br>
      <button href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></button>
      <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
      </form>
    </div>
</div>