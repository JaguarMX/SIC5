
<?php
include '../php/conexion.php';
function generarRandomString($length) { 
  return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
}

#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');

$key = generarRandomString(4);
//CREAR EL NOMBRE DEL ARCHIVO
$name_file = "PRODUCTOS($key)";

//-------------Vemos si recibe un archivo de documento ------
if (is_uploaded_file($_FILES['documento']['tmp_name'])) {
    $nombrearchivo= trim ($_FILES['documento']['name']); //Eliminamos los espacios en blanco
    $nombrearchivo= str_replace (" ", "", $nombrearchivo);//Sustituye una expresión regular
    $upload= '../files/catalogo_imagen/'.$nombrearchivo;  

    $name_documento = $name_file.'_CATALOGO.pdf';

    //--- AQUI COPIAMOS EL ARCHIVO A LA CARPETA ---
    if(move_uploaded_file($_FILES['documento']['tmp_name'], "$upload")) {
       mysqli_query($conn, "INSERT INTO catalogo (nombre, usuario, fecha) VALUES ('$name_documento', '$id_user', '$Fecha_hoy')");
       rename ($upload, "../files/catalogo_imagen/".$name_documento);   
       echo '<script>M.toast({html:"Documento subido con exito :> .", classes: "rounded"})</script>';   
    }
}
?>
<script>
    var a = document.createElement("a");
      a.href = "../views/catalogo.php";
      a.click();
</script>