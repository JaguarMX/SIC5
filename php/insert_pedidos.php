<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
session_start();
date_default_timezone_set('America/Mexico_City');
$nombreCliente = "";
$idOperacion = $conn->real_escape_string($_POST['valorIdOperacion']);
$tipoPedido = $conn->real_escape_string($_POST['valorIdTipoPedido']);
if ($tipoPedido == 0){
    $nombreCliente = $conn->real_escape_string($_POST['valorNombre']);
}else if($tipoPedido == 1){
    $queryFoliosTaller = "SELECT * FROM dispositivos WHERE id_dispositivo = $idOperacion";
    $resultadosQuerieFliosTaller = $conn->query($queryFoliosTaller);
    while($resultadoFolioTaller = $resultadosQuerieFliosTaller->fetch_assoc()){
        $nombreCliente = $resultadoFolioTaller['nombre'];
    }
}else if($tipoPedido == 2){
    $queryRutas = "SELECT * FROM rutas  WHERE id_ruta = $idOperacion";
    $resultadoQueryRutas = $conn->query($queryRutas);
    while($resultadoRutas = $resultadoQueryRutas->fetch_assoc()){
        $nombreCliente = $resultadoRutas['responsable'];
    }
    
}else if($tipoPedido == 3){
    $queryMantenimientos = "SELECT * FROM reportes  WHERE id_reporte = $idOperacion";
    $resultadosMantenimientos = $conn->query($queryMantenimientos);
    while($row = $resultadosMantenimientos->fetch_assoc()){
        $id_cliente = $row['id_cliente'];
        $queryCliente = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
        $filas = mysqli_num_rows($queryCliente);
        if ($filas == 0) {
          $queryCliente = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
          $cliente = mysqli_fetch_array($queryCliente);
          $lugarCliente = $cliente['lugar'];
          $nombreCliente = $cliente['nombre'];
          $queryLugarCliente = mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$lugarCliente");
          $comunidadCliente = mysqli_fetch_array($queryLugarCliente);
          $arregloClienteLugar[] = array("id" => $row['id_reporte'], "descripcion" => $row['descripcion'], "nombre" => $comunidadCliente['nombre'], "municipio" => $comunidadCliente['municipio']);
        }else{
            $cliente = mysqli_fetch_array($queryCliente);
            $nombreCliente = $cliente['nombre'];
        }
        
        $arregloCliente[] = array("id" => $row['id_reporte'], "descripcion" => $row['descripcion']);
      }
}else if($tipoPedido == 4){
    $queryOrdenesServicio = "SELECT * FROM orden_servicios  WHERE id = $idOperacion";
    $resultadosOrdenesServicio = $conn->query($queryOrdenesServicio);
    while($resultadoOrdenServicio = $resultadosOrdenesServicio->fetch_assoc()){
        $id_clienteOrden = $resultadoOrdenServicio['id_cliente'];
        $queryClienteOrden = "SELECT * FROM clientes WHERE id_cliente=$id_clienteOrden";
        $resultadosOrdenNombre = $conn->query($queryClienteOrden);
        while($resultadoNombreOrden = $resultadosOrdenNombre->fetch_assoc()){
            $nombreCliente = $resultadoNombreOrden['nombre'];
        }
        $filasOrden = mysqli_num_rows($resultadosOrdenNombre);
        if ($filasOrden == 0) {
            $queryClienteOrden = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_clienteOrden");
            $clienteOrden = mysqli_fetch_array($queryClienteOrden);
            $lugarClienteOrden = $clienteOrden['lugar'];
            $nombreCliente = $clienteOrden['nombre'];
            $queryLugarClienteOrden = mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$lugarClienteOrden");
            $comunidadClienteOrden = mysqli_fetch_array($queryLugarClienteOrden);
        }
        
    }
}else if($tipoPedido == 5){
    $nombreCliente = $conn->real_escape_string($_POST['valorNombre']);
}

$fechaPedido = $conn->real_escape_string($_POST['valorFecha']);
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');

$sql = "INSERT INTO pedidos (nombre, id_orden, fecha, hora, fecha_requerido, usuario, idOperacion, tipoPedido)
 VALUES('$nombreCliente', '$idOperacion', '$Fecha_hoy', '$Hora', '$fechaPedido', '$id_user', '$idOperacion', '$tipoPedido')";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html :"El pedido se registró satisfactoriamente.", classes: "rounded"})</script>';
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(folio) AS folio FROM pedidos WHERE usuario = $id_user"));            
    $folio = $ultimo['folio'];
    ?>
    <script>
        var a = document.createElement("a");
        a.href = "../views/detalles_pedido.php?folio="+<?php echo $folio; ?>;
        a.click();
    </script>
    <?php
}else{
	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';
    ?>
    <script>
        setTimeout("location.href='pedidos.php", 500);
    </script>
    <?php	
}

mysqli_close($conn);
?>