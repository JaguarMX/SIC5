<?php
include('../php/conexion.php');
$id = $conn->real_escape_string($_POST['valorId']);
?>
<script>
	$(document).ready(function(){
	    $('#modalDocCentral').modal();
	    $('#modalDocCentral').modal('open'); 
	 });
</script>
<div id="modalDocCentral" class="modal">
    <div class="modal-content">
     <h5>Seleccionar Documento Nuevo PDF:</h5> 
     <h6 class="red-text"><b>Solo se aceptan documentos con extensión PDF.</b></h6> 
      <form id="respuestaSubirDocCentral" action="../php/subir_doc_central.php" method="post" enctype="multipart/form-data">
      <div class="input-field col s12 m6 l6">
          <div class="file-field input-field">
            <div class="btn">
              <span>SELECCIONAR DOCUMENTO</span>
              <input type="file" name="documentoCentral" id = "documentoCentral" required>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" placeholder="Subir Documento PDF">
            </div>
          </div>
          <input id="idDC" name="idDC" type="hidden" value="<?php echo $id ?>">
      </div><br><br><br><br><br>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></a>
      <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
      </form>
    </div>
</div>