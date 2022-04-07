<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (Buscar2)
include('../php/conexion.php');

$codigo=$_GET['codigo'];;// RECIBIMOS EL ID DE LA RUTA POR GET

#CONSULTAMOS TODAS LAS INSTALACIONES QUE ALLA DE ESTA RUTA
$resultado = $conn->query("SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente WHERE ruta_inst =$codigo");

$array = array();//CREAMOS UN ARRAY VACIO PARA COLOCAR LA INFORAMCION NECESARIA
#RECORREMOS CADA INSTALACION CON UN CICLO Y LO VACIAMOS EN UN ARRAY 
while($cliente=$resultado -> fetch_array()){	
    $id_comunidad = $cliente['lugar'];
    $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
    $id_paquete = $cliente['paquete'];
    $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT subida, bajada, mensualidad FROM paquetes WHERE id_paquete=$id_paquete"));
    $Apagar = $cliente['total']-$cliente['dejo'];
	#LLEMANMOS NUESTRO ARRAY POR CADA REPORTE ENCONTRADO
	$array['id_cliente'] =$cliente['id_cliente'];
	$array['nombre'] =$cliente['nombre'];
	$array['servicio'] =$cliente['servicio'];
	$array['telefono'] =$cliente['telefono'];
	$array['comunidad'] =$sql_comunidad['nombre'];
	$array['referencia'] =$cliente['referencia'];
	$array['total'] =$cliente['total'];
	$array['dejo'] =$cliente['dejo'];
	$array['Apagar'] =$Apagar;
	$array['paquete'] ='(Subida/Bajada)'.$paquete['subida']."/".$paquete['bajada'];
	$array['fecha'] =$cliente['fecha_registro'];

    $instalaciones[] = $array;
}

echo json_encode($instalaciones, JSON_UNESCAPED_UNICODE);
?>
