<?php 
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borrar = 3)
$Accion = $conn->real_escape_string($_POST['accion']);
//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borrar = 3)
switch ($Accion) {
    case 3:  ///////////////           IMPORTANTE               ///////////////
		$fechaInicio = $conn->real_escape_string($_POST["fechaInicio"]);
		$fechaFinal = $conn->real_escape_string($_POST["fechaFinal"]);
		$tipoPago = $conn->real_escape_string($_POST["tipoPago"]);
		$banco = $conn->real_escape_string($_POST["banco"]);
		$totalBancoSeleccionado = 0;
		$totalTodos = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE 
		fecha>='$fechaInicio' AND fecha<='$fechaFinal' AND tipo_cambio='$tipoPago'"));
		$head = $tipoPago.':  .  TOTAL = $'.$totalTodos['precio'];
		if($tipoPago === "Efectivo" OR $tipoPago === "Credito" OR $tipoPago === "SAN"){
			$queryPagos = mysqli_query($conn, "SELECT * FROM pagos WHERE fecha>='$fechaInicio' 
			AND fecha<='$fechaFinal' AND tipo_cambio = '$tipoPago'");
			$metodoPago = $tipoPago;
			$tipoPagoTitulo = $tipoPago;
		}else if ($banco != "1" && $tipoPago === "Banco"){
			$queryPagos = mysqli_query($conn,"SELECT a.id_user, a.id_pago, a.id_cliente, a.descripcion AS descripcion_pago, a.cantidad, a.tipo, 
			a.fecha, a.hora, a.tipo_cambio, c.banco, c.descripcion, c.id_pago FROM pagos a INNER JOIN referencias c ON a.id_pago = c.id_pago 
			WHERE a.fecha >= '$fechaInicio' AND a.fecha <= '$fechaFinal' AND a.tipo_cambio = '$tipoPago' AND c.banco = '$banco'");
			$queryPagosTotal = mysqli_query($conn,"SELECT a.id_user, a.id_pago, a.id_cliente, a.descripcion AS descripcion_pago, a.cantidad, a.tipo, 
			a.fecha, a.hora, a.tipo_cambio, c.banco, c.descripcion, c.id_pago FROM pagos a INNER JOIN referencias c ON a.id_pago = c.id_pago 
			WHERE a.fecha >= '$fechaInicio' AND a.fecha <= '$fechaFinal' AND a.tipo_cambio = '$tipoPago' AND c.banco = '$banco'");
			$metodoPago = $banco;
			if($banco === ""){
				$tipoPagoTitulo = "pagos sin banco asignado";
			}else{
				$tipoPagoTitulo = $banco;
			}
			while($totalBancoElegido = mysqli_fetch_array($queryPagosTotal)){
				$totalBancoSeleccionado += $totalBancoElegido['cantidad'];
			}
		}else if($banco === "1" && $tipoPago === "Banco"){
			$queryPagos = mysqli_query($conn, "SELECT * FROM pagos t WHERE t.id_pago NOT IN 
			(SELECT id_pago FROM referencias) && t.tipo_cambio = '$tipoPago' && t.fecha >= '$fechaInicio' && t.fecha <= '$fechaFinal'");
			$queryPagosTotal = mysqli_query($conn, "SELECT * FROM pagos t WHERE t.id_pago NOT IN 
			(SELECT id_pago FROM referencias) && t.tipo_cambio = '$tipoPago' && t.fecha >= '$fechaInicio' && t.fecha <= '$fechaFinal'");
			$metodoPago = "";
			$tipoPagoTitulo = "pagos sin referencia";
			while($totalBancoElegido = mysqli_fetch_array($queryPagosTotal)){
				$totalBancoSeleccionado += $totalBancoElegido['cantidad'];
		  	}
		}
		
		?>
		<div>
		<h4 class="blue-text"><?php echo $head;?></h4><br>
		<h4 class="blue-text">Total de  <?php echo $tipoPagoTitulo;?> <?php echo "$";?><?php echo $totalBancoSeleccionado;?></h4><br>
		<table class="bordered highlight responsive-table">
			<thead>
			<tr>
				<th>#Cliente</th>
				<th>Cliente</th>
				<th>Cantidad</th>
				<th>Tipo</th>
				<th>Descripción</th>
				<th>Fecha</th>
				<th>Cambio</th>
				<th>Referencia</th>
				<th>Banco</th>           
				<th>Registró</th>
			</tr>
			</thead>
			<tbody>
		<?php
		$aux = mysqli_num_rows($queryPagos);
		if($aux>0){
		while($pagos = mysqli_fetch_array($queryPagos)){
		$id_cliente = $pagos['id_cliente'];
		$id_user = $pagos['id_user'];
		$queryClientes = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
		$filas = mysqli_num_rows($queryClientes);
		if ($filas == 0) {
			$queryClientes = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
		}
		$cliente= mysqli_fetch_array($queryClientes);
		$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"));
		if($tipoPago === "Efectivo" OR $tipoPago === "Credito" OR $tipoPago === "SAN"){
			$descripcionPago = $pagos['descripcion'];
			$referencia = "N/A";
			$valorBanco = "N/A";
		}else if ($tipoPago === "Banco" && $banco != "1"){
			$descripcionPago = $pagos['descripcion_pago'];
			$referencia = $pagos['descripcion'];
			$valorBanco = $pagos['banco'];
		}else if ($tipoPago === "Banco" && $banco === "1"){
			$descripcionPago = $pagos['descripcion'];
			$referencia = "N/A";
			$valorBanco = "N/A";
		}
		
		if ($banco === ""){
			$valorBanco = "N/A";
		}
		?>
		<tr>
			<td><b><?php echo $id_cliente;?></b></td>
			<td><?php echo $cliente['nombre'];?></td>
			<td>$<?php echo $pagos['cantidad'];?></td>
			<td><?php echo $pagos['tipo'];?></td>
			<td><?php echo $descripcionPago;?></td>
			<td><?php echo $pagos['fecha'].' '.$pagos['hora'];?></td>
			<td><?php echo $pagos['tipo_cambio'];?></td>
			<td><?php echo $referencia;?></td>
			<td><?php echo $valorBanco;?></td>
			<td><?php echo $usuario['firstname'];?></td>
		</tr>
		<?php
		$aux--;
		}
		}else{
		echo "<center><b><h5>No hay pagos de $metodoPago registrados en esta fecha</h5></b></center>";
		}
		?>
		<?php 

		?>        
				</tbody>
			</table>
		</div>
		<br>
		<?php
	   break;
    case 1:///////////////           IMPORTANTE               ///////////////
        
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
       
        break;
    case 3:
        
    	break;
    case 4:
    	
}// FIN switch
mysqli_close($conn);