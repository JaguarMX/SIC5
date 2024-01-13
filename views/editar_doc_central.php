<?php
include('../php/conexion.php');
$id = $conn->real_escape_string($_POST['valorId']);
$documentoSubido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales WHERE id = '$id'"));
echo $documentoSubido['documentoPdf'];
?>
<script>
	$(document).ready(function(){
	    $('#modalEditarDoc').modal();
	    $('#modalEditarDoc').modal('open'); 
	 });
</script>
<div id="modalEditarDoc" class="modal">
    <div class="modal-content">
     <h5>Editar documento de central:</h5> 
     <h6 class="red-text"><b>Al momento de editar el archivo se reelmplazará por el actualmente seleccionado. Solo se aceptan archivos con extensión PDF.</b></h6> 
      <form id="respuesta" action="../php/update_doc_central.php" method="post" enctype="multipart/form-data">
        <div class="input-field col s12">
        DOCUMENTO: <a href = "../files/centrales/<?php echo $documentoSubido['documentoPdf']; ?>" target = "blank"> <?php echo $documentoSubido['documentoPdf']; ?></a></div>
        <div class="input-field col s12 m6 l6">
            <div class="file-field input-field">
              <div class="btn">
                <span>SELECCIONAR DOCUMENTO</span>
                <input type="file" name="documento" id = "documento" required>
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text" placeholder="Subir Documento PDF">
              </div>
            </div>
            <input id="id" name="id" type="hidden" value="<?php echo $id ?>">
            <input id="doc" name="doc" type="hidden" value="<?php echo $documentoSubido['documentoPdf'] ?>">
        </div><br><br><br><br><br><br><br>
        <button href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></button>
        <button class="btn waves-effect waves-light pink right" type="submit" name="action">Actualizar<i class="material-icons right">file_upload</i></button>
      </form>
    </div>
</div>