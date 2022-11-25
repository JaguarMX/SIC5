<html>
  <?php
  session_start();
  include('../php/conexion.php');
  date_default_timezone_set('America/Mexico_City');

  //ASÍ OBTENEMOS LAS VARIABLES DEL ARCHIVO activar_sicflix.php
  $estatus = $conn->real_escape_string($_POST ['valorEstatus']);
  $Fecha_hoy = $conn->real_escape_string($_POST['valorFecha_Atendio']);
  $id_user = $conn->real_escape_string($_POST['valorAtendio']);
  $no_usuario = $conn->real_escape_string($_POST['valorUsuario_Sicflix']);
  $pass = $conn->real_escape_string($_POST['valorContraseña']);
  $IdCliente = $conn->real_escape_string($_POST['valorId_Cliente']);

  //SE HACE LA INCERCIÓN DE DATOS 
  $sql = "UPDATE `reporte_sicflix` SET estatus = $estatus, fecha_atendio = '$Fecha_hoy', atendio=$id_user, nombre_usuario_sicflix=$no_usuario, contraseña_sicflix ='$pass' WHERE cliente=$IdCliente ";
  if(mysqli_query($conn, $sql)){
    echo  '<script>M.toast({html:"ACTIVACIÓN EXITOSA.", classes: "rounded"})</script>';
    ?>
    <script>
        var a = document.createElement("a");	
        a.href = "../views/reportes_sicflix.php";
        a.click();
    </script>
    <?php
  }else{
    echo  '<script>M.toast({html:"Ha ocurrido un error con el insert.", classes: "rounded"})</script>';	
  }

  mysqli_close($conn);
  ?>
</html>  