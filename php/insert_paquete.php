<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Subida = $conn->real_escape_string($_POST['valorSubida']);
$Bajada = $conn->real_escape_string($_POST['valorBajada']);
$Mensualidad = $conn->real_escape_string($_POST['valorMensualidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM paquetes WHERE subida='$Subida' AND bajada = '$Bajada' AND mensualidad = '$Mensualidad'"))>0){
    echo '<script>M.toast({html :"Ya se encuentra un paquete con la misma informacion.", classes: "rounded"})</script>';
}else{
    //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
    $sql = "INSERT INTO paquetes (subida, bajada, mensualidad, descripcion) VALUES('$Subida', '$Bajada', '$Mensualidad', '$Descripcion')";
    if(mysqli_query($conn, $sql)){
       echo '<script>M.toast({html:"El paquete se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
       ?>
        <script>    
            setTimeout("location.href='../views/paquetes.php'", 800);
        </script>
        <?php
    }else{
       echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';
    }  
}
?>