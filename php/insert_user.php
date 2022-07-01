<?php
//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
// Si estamos usando una versión de PHP superior entonces usamos la API para encriptar la contrasela con el archivo: password_api_compatibility_library.php
include_once("password_compatibility_library.php");

include("../php/conexion.php");//Contiene las variables de configuración para conectar a la base de datos

$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/",";","?", "php", "echo","$","{","}","=");
$caracteres_buenos = array("", "", "", "", "", "", "", "", "","","", "","","", "","","");
			
// Eliminamos cualquier tipo de código HTML o JavaScript
$valorFirstName = $conn->real_escape_string($_POST["valorNombre"]);
$valorLastName = $conn->real_escape_string($_POST["valorApellidos"]);
$valorUserName = $conn->real_escape_string($_POST["valorUsuario"]);
$valorUserEmail = $conn->real_escape_string($_POST["valorEmail"]);
$valorUserPassword = $conn->real_escape_string($_POST['valorContra']);
$valorUserRol = $conn->real_escape_string($_POST['valorRol']);
//ELIMINAR CODIGO PHP
$valorFirstName = str_replace($caracteres_malos, $caracteres_buenos, $valorFirstName);
$valorLastName = str_replace($caracteres_malos, $caracteres_buenos, $valorLastName);
$valorUserName = str_replace($caracteres_malos, $caracteres_buenos, $valorUserName);
$valorUserEmail = str_replace($caracteres_malos, $caracteres_buenos, $valorUserEmail);       
$valorUserRol = str_replace($caracteres_malos, $caracteres_buenos, $valorUserRol);

$date_added=date("Y-m-d H:i:s");//FECHA Y HORA
// Se encripta el la contraseña del usuario con la función password_hash(), y retorna una cadena de 60 caracteres
$valorUserPassword_hash = password_hash($valorUserPassword, PASSWORD_DEFAULT);
					
// Comprobamos si el usuario o el correo ya existe
$query_check_user=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE user_name = '$valorUserName' OR user_email = '$valorUserEmail'"));
if ($query_check_user > 0) {
    echo '<script>M.toast({html:"Este usuario o correo ya existe en la base de datos.", classes: "rounded"})</script>';
} else {
	// Escribimos el nuevo usuario en la base de datos
    $sql = "INSERT INTO users (firstname, lastname, user_name, user_password_hash, user_email, date_added, area)
            VALUES ('$valorFirstName','$valorLastName','$valorUserName', '$valorUserPassword_hash', '$valorUserEmail','$date_added','$valorUserRol')";
    $query_new_user_insert = mysqli_query($conn,$sql);

    // Si el usuario fue añadido con éxito
    if ($query_new_user_insert) {
        ?>
        <script>
          M.toast({html:"Usuario añadido correctamente.", classes: "rounded"});
          setTimeout("location.href='login.php'", 800);
        </script>
        <?php
    } else {
        echo '<script>M.toast({html:"Hubo un error, intentelo mas tarde.", classes: "rounded"})</script>';
    }
}
?>