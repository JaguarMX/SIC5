<?php
include '../php/conexion.php';
$id = $_POST["id"]; 
$Documento  = $_POST["doc"];
function generarRandomString($length) { 
  return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, $length); 
}


#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');


$key = generarRandomString(5);
//CREAR EL NOMBRE DEL ARCHIVO
$name_file = "PRODUCTOS($key)";
//-------------Vemos si recibe un archivo de documento ------
if (is_uploaded_file($_FILES['documento']['tmp_name'])) {
    $nombrearchivo= trim ($_FILES['documento']['name']); //Eliminamos los espacios en blanco
    $nombrearchivo= str_replace (" ", "", $nombrearchivo);//Sustituye una expresión regular
    $upload= '../files/catalogo_imagen/'.$nombrearchivo;  
    //--- SI HAY UN ARCHIVO EN LA CARPETA CON ESE NOMBRE LO BORRAMOS---
    if (file_exists("../files/catalogo_imagen/".$Documento)) {
      unlink("../files/catalogo_imagen/".$Documento);
    } 

    $name_documento = $name_file.'_CATALOGO.pdf';

    //--- AQUI COPIAMOS EL ARCHIVO A LA CARPETA ---
    if(move_uploaded_file($_FILES['documento']['tmp_name'], "$upload")) {
        if(mysqli_query($conn, "UPDATE catalogo SET nombre = '$name_documento' WHERE id=$id")){
            if(mysqli_query($conn, "UPDATE catalogo SET usuario = '$id_user' WHERE id=$id")){
                if(mysqli_query($conn, "UPDATE catalogo SET fecha = '$Fecha_hoy' WHERE id=$id")){
                    echo '<script>M.toast({html:"ACTUAIZA BD...", classes: "rounded"})</script>';
            if(rename ($upload, "../files/catalogo_imagen/".$name_documento)){
                ?>
                <script>
                    id = <?php echo $id; ?>;
                    var a = document.createElement("a");
                    a.href = "../views/catalogo.php";
                    a.click();
                </script>
                <?php
            }else{
                echo 'ERROR AL RENOMBRAR !';
            }
        }else{
            echo 'ERROR AL ACTUALIZAR BD!';
        }
         
            }
        }
    }
}
?>
