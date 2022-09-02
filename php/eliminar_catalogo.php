<?php
include '../php/conexion.php';
$id = $_POST["id"]; 
$Documento  = $_POST["doc"];

#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA VARIABLE EL ID DEL USUARIO LOGUEADO

#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');

//--- AQUI ELIMINAMOS EL ARCHIVO DE LA BD---

//--- AQUI ELIMINAMOS EL ARCHIVO CARPETA ---
    if (file_exists("../files/catalogo_imagen/".$Documento)) {
        mysqli_query($conn, "DELETE FROM catalogo WHERE id = '$id'");
        unlink("../files/catalogo_imagen/".$Documento);
      
    } 
?>

<script>
    var a = document.createElement("a");
      a.href = "../views/catalogo.php";
      a.click();
</script>