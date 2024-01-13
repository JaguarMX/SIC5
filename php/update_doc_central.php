<?php
include '../php/conexion.php';
$id = $_POST["id"]; 
$DocAnterior  = $_POST["doc"];
function generarRandomString($length) { 
  return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
}
$key = generarRandomString(3);
//CREAR EL NOMBRE DEL ARCHIVO
$name_file = $id;
//-------------Vemos si recibe un archivo de documento ------
if (is_uploaded_file($_FILES['documento']['tmp_name'])) {
    $nombrearchivo= trim ($_FILES['documento']['name']); //Eliminamos los espacios en blanco
    $nombrearchivo= str_replace (" ", "", $nombrearchivo);//Sustituye una expresión regular
    $upload= '../files/centrales/'.$nombrearchivo;  
    //--- SI HAY UN ARCHIVO EN LA CARPETA CON ESE NOMBRE LO BORRAMOS---
    if (file_exists("../files/centrales/".$DocAnterior)) {
      unlink("../files/centrales/".$DocAnterior);
    } 
    $name_documento = $name_file.'_central.pdf';
    //--- AQUI COPIAMOS EL ARCHIVO A LA CARPETA ---
    if(move_uploaded_file($_FILES['documento']['tmp_name'], "$upload")) {
       mysqli_query($conn, "UPDATE centrales SET documentoPdf = '$name_documento' WHERE id=$id");
       rename ($upload, "../files/centrales/".$name_documento);   
       echo '<script>M.toast({html:"Documento acltualizado con exito.", classes: "rounded"})</script>';   
    }
}
?>
<script>
    id = <?php echo $id; ?>;
    var a = document.createElement("a");
      a.href = "../views/files_centralG.php?id_central="+id;
      a.click();
</script>