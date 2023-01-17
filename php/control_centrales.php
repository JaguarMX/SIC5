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
echo $Accion;
//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borrar = 3)
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

    	//CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "add_cliente.php" QUE NESECITAMOS PARA INSERTAR
    	$Nombre = $conn->real_escape_string($_POST['valorNombre']);
		$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
		$Email = $conn->real_escape_string($_POST['valorEmail']);
		$RFC = $conn->real_escape_string($_POST['valorRFC']);
		$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
		$Colonia = $conn->real_escape_string($_POST['valorColonia']);
		$Localidad = $conn->real_escape_string($_POST['valorLocalidad']);
		$CP = $conn->real_escape_string($_POST['valorCP']);

		//VERIFICAMOS QUE NO HALLA UN CLIENTE CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE(nombre='$Nombre' AND direccion='$Direccion' AND colonia='$Colonia' AND cp='$CP') OR rfc='$RFC' OR email='$Email'"))>0){
	 		echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	}else{
	 		// SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
	 		$sql = "INSERT INTO `punto-venta_clientes` (nombre, telefono, direccion, colonia, cp, rfc, email, localidad, usuario, fecha) 
				VALUES('$Nombre', '$Telefono', '$Direccion', '$Colonia', '$CP', '$RFC', '$Email', '$Localidad', '$id_user','$Fecha_hoy')";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El cliente se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
				echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
				echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
			}//FIN else DE ERROR
	 	}// FIN else DE BUSCAR CLIENTE IGUAL

        break;
    case 1:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

    	//CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO DE "clientes_punto_venta.php"
    	$Texto = $conn->real_escape_string($_POST['texto']);

    	//VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LAS CENTRALES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...... Buscar(N° Central, N° Comunidad, Encargado)
			$sql = "SELECT * FROM `centrales` WHERE id = '$Texto' OR comunidad = '$Texto' OR nombre LIKE '%$Texto%' ORDER BY id";
		}else{
			//ESTA CONSULTA SE HARA SIEMPRE QUE NO HALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
      		$sql = "SELECT * FROM `centrales`";
		}//FIN else $Texto VACIO O NO

		// REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
			echo '<script>M.toast({html:"No se encontraron centrales.", classes: "rounded"})</script>';
		} else {
			//SI NO ESTA EN == 0 SI TIENE INFORMACION
			//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
			//RECORREMOS UNO A UNO LAS CENTRALES CON EL WHILE	
			while($tmp = mysqli_fetch_array($consulta)){
          		$id_comundad = $tmp['comunidad'];
          		$cominidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = $id_comundad"));
				//Output
          
				$contenido .= '			
		          <tr>
		            <td>'.$tmp['id'].'</td>
		            <td>'.$cominidad['nombre'].'</td>
		            <td>'.$tmp['nombre'].'</td>
		            <td>'.$tmp['telefono'].'</td>
		            <td><form method="post" action="../views/central.php"><input name="id_central" type="hidden" value="'.$tmp['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></button></form></td>
          			<td><form method="post" action="../views/editar_central.php"><input name="id_central" type="hidden" value="'.$tmp['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
          			<td><a onclick="borrar('.$tmp['id'].');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';
			}//FIN while
		}//FIN else
		echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

    	//CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "editar_cliente_pv.php" QUE NESECITAMOS PARA ACTUALIZAR
    	$id = $conn->real_escape_string($_POST['id']);
    	$Nombre = $conn->real_escape_string($_POST['valorNombre']);
		$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
		$Email = $conn->real_escape_string($_POST['valorEmail']);
		$RFC = $conn->real_escape_string($_POST['valorRFC']);
		$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
		$Colonia = $conn->real_escape_string($_POST['valorColonia']);
		$Localidad = $conn->real_escape_string($_POST['valorLocalidad']);
		$CP = $conn->real_escape_string($_POST['valorCP']);

		//VERIFICAMOS QUE NO HALLA UN CLIENTE CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE (telefono = '$Telefono' OR rfc='$RFC' OR email='$Email') AND id != $id"))>0){
	 		echo '<script >M.toast({html:"El RFC, Telefono o Email ya se encuentra registrados en la BD.", classes: "rounded"})</script>';
	 	}else{
			//CREAMO LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL CLIENTE Y LA GUARDAMOS EN UNA VARIABLE
			$sql = "UPDATE `punto-venta_clientes` SET nombre = '$Nombre', telefono = '$Telefono', email = '$Email', rfc = '$RFC', direccion = '$Direccion', colonia = '$Colonia', localidad = '$Localidad', cp = '$CP' WHERE id = '$id'";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El cliente se actualizo con exito.", classes: "rounded"})</script>';	
				echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
				echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
			}//FIN else DE ERROR
		}// FIn else Validacion
        break;
    case 3:
        // $Accion es igual a 3 realiza:
    	//CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "clientes_punto_venta.php" QUE NESECITAMOS PARA BORRAR
    	$id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario

		if(mysqli_query($conn, "DELETE FROM centrales WHERE id = '$id'")){
		    echo '<script >M.toast({html:"Central Borrada..", classes: "rounded"})</script>';
			?>
			<script>
				var a = document.createElement("a");
				a.href = "../views/centrales.php";
				a.click();   
			</script>
			<?php	
		}else{ 
			#SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
			echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
		}  
    	break;
    case 4:
    	// code...
		$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);
		$Tipo = $conn->real_escape_string($_POST['valorTipo']);
		$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
		$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
		$Vencimiento = $conn->real_escape_string($_POST['valorVence']);

		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos_centrales WHERE descripcion='$Descripcion' AND tipo='$Tipo' AND id_central='$IdCentral'"))>0){
			echo '<script >M.toast({html:"Ya se encuentra un pago con los mismos datos registrados.", classes: "rounded"})</script>';
		}else{
			$sql = "INSERT INTO pagos_centrales (cantidad, descripcion, tipo, usuario, fecha, id_central) VALUES('$Cantidad', '$Descripcion', '$Tipo', '$id_user', '$Fecha_hoy', '$IdCentral')";
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El pago se dió de alta satisfactoriamente.", classes: "rounded"})</script>';
				mysqli_query($conn, "UPDATE centrales SET vencimiento_renta='$Vencimiento'WHERE id='$IdCentral'");
			}else{
				echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
			}
		}
		?>
		<table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Cantidad</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <th>Imprimir</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_pagos = "SELECT * FROM pagos_centrales WHERE tipo != 'Dispositivo' AND id_central = '$IdCentral' ORDER BY id DESC";
          $resultado_pagos = mysqli_query($conn, $sql_pagos);
          $aux = mysqli_num_rows($resultado_pagos);
          if($aux>0){
          while($pagos = mysqli_fetch_array($resultado_pagos)){
            $id_user = $pagos['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td>$<?php echo $pagos['cantidad'];?></td>
              <td><?php echo $pagos['tipo'];?></td>
              <td><?php echo $pagos['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo $pagos['fecha'];?></td>
              <td><a href = "../php/ticket_central.php?id=<?php echo $pagos['id'];?>" target = "blank" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
              <td><a onclick="borrar(<?php echo $pagos['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado pagos</h5 ></b></center>";
          }
          ?> 
          </tbody>
        </table>
		<?php
    	break;
}// FIN switch
mysqli_close($conn);