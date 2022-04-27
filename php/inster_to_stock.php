<?php
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#INCLUIMOS LA CONEXION A LA BASE DE DATOS PARA PODER HACER CUALQUIER MODIFICACION, INSERCION O SELECCION
include('../php/conexion.php');
#TOMAMOS EL ARCHIVO DONDE ESTA LA INFOMACION DEL INICIO SE SECCION AL SISTEMA
include('is_logged.php');
#TOMAMOS EL ID DEL USUARION LOGEADO
$Registro = $_SESSION['user_id'];
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');

#RECIBIMOS LOS VALORES QUE SE NOS ENVIA DESDE EL FORMULARIO DEL A VISTA STOCK (PARA INSERTAR)
$IdTecnico = $conn->real_escape_string($_POST['valorIdTecnico']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);#TIPO DE MATERIAL ANTENA, ROUTER, TUBOS, ETC.
$Nombre = $conn->real_escape_string($_POST['valorNombre']);#NOMBRE MIMOSA, TP-LINK, ETC.
$Serie = $conn->real_escape_string($_POST['valorSerie']);#NUMERO DE SERIE EN CASO DE SER ANTENA O ROUTER BIENEN EN LA CAJA
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);#CANTIDAD A REGISTRAR DEL MATERIAL
$Regreso = $conn->real_escape_string($_POST['valorRegreso']);
$Ruta = $conn->real_escape_string($_POST['valorRuta']);#EN QUE RUTA PIDIO DICHO MATERIAL PARA MEJOR CONTROL
$Es = $conn->real_escape_string($_POST['valorEs']);#ORIGEN

#COMPARAREMOS SI EL MATERIAL A INSRTAR ES UNA ANTENA O UN ROUTER Y SI LA SERIE YA SE ENCUENTRA REGISTRADA EN LA TABLA stock_tecnicos
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE serie = '$Serie' AND (tipo = 'Router' OR tipo = 'Antena') AND es = '$Es'"))>0) {
  #SI YA SE ENCUENTRA REGISTRARA UNA SERIE IGUAL EN UNA ANTENA O ROUTER REGRESARA UNA ALERTA Y NO SE HARA LA ISERCION
  echo "<script>M.toast({html: 'Ya se encuentra esta serie registrada en el stock.', classes: 'rounded'})</script>";
}else{
  #COMPARAMOS SI EL VALOR DE $Regreso == 'Si' QUIERE DECIR QUE REGRESO UN CARRETE Y AUTOMATICAMENTE LA BOBINA ANTERIOR SE PONE COMO USADA Y SE AGREGA UNA NIEVA CON 300 mts
  if ($Regreso == 'Si') {
    #MODIFICAMOS LA BOBINA ANTERIOR 
    if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = 300, fecha_salida = '$Fecha_hoy', disponible = 1  WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
      #SI SE MODIFICA SIN NINGUN ERROR MANDARA UNA ALERTA
      echo '<script>M.toast({html:"Se actualizo la Bobina...", classes: "rounded"})</script>';
    }else{
      #SI NO SE HACE LA MODIFICACION DE LA BOBINA ANTERIOR LANZARA UNA ALERTA DE ERROR
      echo '<script>M.toast({html:"Ocurrio un error al actualizar la Bobina...", classes: "rounded"})</script>';
    }
  }
  #CREAMOS EL SQL CON EL CUAL SE HARA LA INSERCION DEL MATERIAL
  $sql = "INSERT INTO stock_tecnicos (nombre, serie, tipo, cantidad, fecha_alta, registro, tecnico, ruta, es) VALUES('$Nombre', '$Serie', '$Tipo', '$Cantidad', '$Fecha_hoy', '$Registro', '$IdTecnico','$Ruta','$Es')";
  #AQUI COMPARAMOS Y COMPROBAMOS SI SE HACE LA INSERCION EN LA BASE DE DATOS
  if(mysqli_query($conn, $sql)){
    #SI SE INSERTO CORRECTAMENTE MANDARA UNA ALERTA DE EXITO!
    echo '<script>M.toast({html:"El material se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
  }else{
    #SI NO SE INSERTA EL MATERIAL EN LA BD MANDARA UNA ALERTA DE ERROR
    echo '<script>M.toast({html:"Ha ocurrido un error al insertar.", classes: "rounded"})</script>';
  }
}
?>
<script>
  function atras() {
      M.toast({html: "Refrescando...", classes: "rounded"});
      //REDIRECCIONAMOS A LA VISTA stock.php (RETROCEDEMOS)
      setTimeout("location.href='stock_tecnico.php?id_tecnico=<?php echo $IdTecnico ?>'", 500);
    }
    atras();
</script>
