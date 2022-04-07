
<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (Buscar)
include('../php/conexion.php');

$codigo=$_GET['codigo'];// RECIBIMOS EL ID DE LA RUTA POR GET

#CONSULTAMOS TODAS LAS INSTALACIONES QUE ALLA DE ESTA RUTA
$resultado = $conn->query("SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente WHERE tmp_pendientes.ruta_inst =$codigo");

#RECORREMOS CADA REPORTE CON UN CICLO Y LO VACIAMOS EN UN ARRAY 
$arr = array();//CREAMOS UN ARRAY VACIO PARA COLOCAR LA INFORAMCION NECESARIA (id_reporte, id_cliente, clientes.nombre, telefono, comunidades.nombre, referencia, coordenadas, reporte.descripcion, diagnostico, reporte.fecha)
while($listado=$resultado -> fetch_array()){

	#LLEMANMOS NUESTRO ARRAY POR CADA REPORTE ENCONTRADO

	$arr['id_cliente'] =$listado['id_cliente'];
    $producto[] = $arr;
}

echo json_encode($producto, JSON_UNESCAPED_UNICODE);
?>

