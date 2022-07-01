<?php
include('../php/conexion.php');
$valorId = $conn->real_escape_string($_POST["valorId"]);
$valorEstatus = $conn->real_escape_string($_POST["valorEstatus"]);

$sql= "UPDATE users SET estatus = '$valorEstatus' WHERE user_id = '$valorId'";

if(mysqli_query($conn, $sql)){
    ?>
    <script>
        M.toast({html:"Usuario actualizado.", classes: "rounded"});
        setTimeout("location.href='usuarios.php'", 800);
    </script>
    <?php
}else{
    echo '<script>M.toast({html:"Hubo un error, intentelo mas tarde.", classes: "rounded"})</script>';
}
?>