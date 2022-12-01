<html>
  <?php
  session_start();
  include('../php/conexion.php');
  date_default_timezone_set('America/Mexico_City');

  //ASÍ OBTENEMOS LAS VARIABLES DEL ARCHIVO activar_sicflix.php
  $IdReporte = $conn->real_escape_string($_POST ['valorID_Reporte']);
  $estatus = $conn->real_escape_string($_POST ['valorEstatus']);
  $Fecha_hoy = $conn->real_escape_string($_POST['valorFecha_Atendio']);
  $id_user = $conn->real_escape_string($_POST['valorAtendio']);
  $no_usuario = $conn->real_escape_string($_POST['valorUsuario_Sicflix']);
  $pass = $conn->real_escape_string($_POST['valorContraseña']);
  $IdCliente = $conn->real_escape_string($_POST['valorId_Cliente']);

  //DEFINIMOS LAS SIGUIENTES VARIABLES
  $sicflix=1;
  $pass_act=1;

  //SE HACE LA INCERCIÓN DE DATOS A LAS TABLAS reporte_sicflix Y clientes
  $sql = "UPDATE `reporte_sicflix` SET estatus = $estatus, fecha_atendio = '$Fecha_hoy', atendio=$id_user, nombre_usuario_sicflix=$no_usuario, contraseña_sicflix ='$pass' WHERE id =$IdReporte";
  $sql2 = "UPDATE `clientes` SET sicflix = $sicflix, contraseña_sicflix = $pass_act WHERE id_cliente=$IdCliente ";
  
  if(mysqli_query($conn, $sql)){
    if(mysqli_query($conn, $sql2)){
        echo  '<script>M.toast({html:"ACTIVACIÓN EXITOSA.", classes: "rounded"})</script>';
        ?>
        <script>
            var a = document.createElement("a");	
            a.href = "../views/reportes_sicflix.php";
            a.click();
        </script>
        <?php
    }else{
        echo  '<script>M.toast({html:"Ha ocurrido un error con el insert a clientes.", classes: "rounded"})</script>';
    }
  }else{
    echo  '<script>M.toast({html:"Ha ocurrido un error con el insert a reporte_sicflix.", classes: "rounded"})</script>';	
  }

  mysqli_close($conn);
  ?>
</html>  