<?php
include '../php/conexion.php';
$id = $_POST["idDC"]; 
function generarRandomString($length) { 
  return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
}
$key = generarRandomString(3);
//CREAR EL NOMBRE DEL ARCHIVO
$name_file = $id;
//-------------Vemos si recibe un archivo de documento ------
if (is_uploaded_file($_FILES['documentoCentral']['tmp_name'])) {
    $nombrearchivo= trim ($_FILES['documentoCentral']['name']); //Eliminamos los espacios en blanco
    $nombrearchivo= str_replace (" ", "", $nombrearchivo);//Sustituye una expresión regular
    $upload= '../files/centrales/'.$nombrearchivo;  

    $name_documento = $name_file.'_central.pdf';

    //--- AQUI COPIAMOS EL ARCHIVO A LA CARPETA ---
    if(move_uploaded_file($_FILES['documentoCentral']['tmp_name'], "$upload")) {
       mysqli_query($conn, "UPDATE centrales SET documentoPdf = '$name_documento' WHERE id=$id");
       rename ($upload, "../files/centrales/".$name_documento);   
       echo '<script>M.toast({html:"Documento subido con exito.", classes: "rounded"})</script>';   
    }
}
?>
<script>
   id = <?php echo $id; ?>;
    var a = document.createElement("a");
      a.href = "../views/files_centralG.php?id_central="+id;
      a.click();
</script>