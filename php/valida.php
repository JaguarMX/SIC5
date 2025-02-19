<?php 
	include ('conexion.php');
	$Texto = $conn->real_escape_string($_POST['texto']);
	$mensaje = '';
	if ($Texto != "") {
		$Com = explode("-", $Texto);
		$SiIp = explode("*", $Texto);
		if (count($Com)>1) {			//PRIMERO VERA SI ESTAMOS BUSCANDO UNA COMUNIDAD EN ESTE IF Y MOSTRARA TODOS LOS CLIENTES DE ESA COMUNIDAD
			$nombre = $Com[1];
			$consulta = mysqli_query($conn, "SELECT * FROM comunidades WHERE nombre LIKE '%$nombre%' LIMIT 1");
			$filas = mysqli_num_rows($consulta);
			if ($filas == 0) {
				echo '<script>M.toast({html: "No se encontraron comunidades.", classes: "rounded"})</script>';
				$sql = "SELECT * FROM users WHERE user_id = 200000";
			}else{
				$comunidad = mysqli_fetch_array($consulta);
				$id_comunidad = $comunidad['id_comunidad'];
				$sql = "SELECT * FROM clientes WHERE lugar = '$id_comunidad' AND instalacion IS NOT NULL ORDER BY id_cliente";
			}
		}else if (count($SiIp)>1) {
			//DESPUES VERA SI ESTAMOS BUSACANDO UN CLIENTE POR IP
			$ip = trim($SiIp[1]);
			$sql = "SELECT * FROM clientes WHERE ip LIKE '$ip%' AND instalacion IS NOT NULL ORDER BY id_cliente LIMIT 20";
		}else{
			//AQUI BUSCARA SI ES POR NOMBRE O POR ID DE CLIENTE
			$sql = "SELECT * FROM clientes WHERE ( nombre LIKE '%$Texto%' OR id_cliente = '$Texto') AND instalacion IS NOT NULL ORDER BY id_cliente LIMIT 20";
		}
	}else{
		//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR...
		$sql = "SELECT * FROM clientes WHERE instalacion IS NOT NULL ORDER BY id_cliente LIMIT 30";
	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	$filas2 = 1;
	if ($filas == 0) {
		$sql2 = "SELECT * FROM especiales WHERE nombre LIKE '%$Texto%' OR  id_cliente LIKE '$Texto'";
		$consulta = mysqli_query($conn, $sql2);
		//Obtiene la cantidad de filas que hay en la consulta
		$filas2 = mysqli_num_rows($consulta);
	}
	if ($filas2 == 0) {
			echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
		
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle		
		while($resultados = mysqli_fetch_array($consulta)) {
			$id_comunidad = $resultados['lugar'];
			$sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
			$no_cliente = $resultados['id_cliente'];
			if ($no_cliente > 10000) {
				$servicio = 'Internet';
			}else{
				$servicio = $resultados['servicio'];
			}

			//Output
			$mensaje .= '			
		          <tr>
		            <td>'.$no_cliente.'</td>
		            <td><b>'.$resultados['nombre'].'</b></td>
		            <td><b>'.$servicio.'</b></td>
		            <td>'.$sql_comunidad['nombre'].', '.$sql_comunidad['municipio'].'</td>
		            <td><a class="btn-floating btn-tiny waves-effect waves-light pink modal-trigger" href="#" onclick="selCliente('.$no_cliente.')"><i class="material-icons">payment</i></a></td>
		            <td><form method="post" action="../views/form_reportes.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">report_problem</i></button></form></td>
		            <td><form method="post" action="../views/credito.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
		          </tr>';     

		}//Fin while $resultados
	} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>