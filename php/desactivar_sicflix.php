<html>
  <?php
  session_start();
  include('../php/conexion.php');
  date_default_timezone_set('America/Mexico_City');

  //ASÍ OBTENEMOS LAS VARIABLES DEL ARCHIVO modal_desactivar_sicflix.php
  $ID_reporte = $conn->real_escape_string($_POST ['valorid_reporte']);
  $ID_cliente = $conn->real_escape_string($_POST ['valorid_cliente']);

  //Obtenemos las otras variables que se nececitan para insertar
  $Fecha = date('Y-m-d');
  $Hora = date('H:i:s');
  $id_user = $_SESSION['user_id'];
  $sicflix = 0;
  $pass = 0;
  $Estatus = 1;

  //UPDATE EN EL REPORTE PARA ACTUALIZARLO  
  $sql = "UPDATE `reporte_sicflix` SET estatus = $Estatus,  fecha_atendio = '$Fecha', atendio = $id_user WHERE id = $ID_reporte ";
  //UPDATE EN LA TABLA CLIENTES PARA QUITAR EL VALOR DE sicflix Y contraseña_sicflix
  $sql2= "UPDATE clientes SET sicflix = $sicflix, contraseña_sicflix = $pass WHERE id_cliente = $ID_cliente";
  
  if(mysqli_query($conn, $sql)){
    if (mysqli_query($conn, $sql2)) {
        echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
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