<!-- FUNCIÓN QUE SE ACTIVA CON EL BOTON DE: ACTIVAR SICFLIX -->
<script>
	function activar() {
		var textoAtendido = $("select#atendido").val();
		var id_Pago = $("input#id_pago").val();    
		if (textoAtendido == "") {
		M.toast({html:"Elegir si fue riegistrado o no.", classes: "rounded"});
		}else{
		$.post("../php/update_tel.php", {
			valorAtendido: textoAtendido,
			valorIdPago: id_Pago
			}, function(mensaje) {
				$("#resultado_activar").html(mensaje);
			}); 
		}
	}
</script>
<html>
<head>
	<title>SIC | Activar Sicflix al cliente</title>
	<?php 
	include('fredyNav.php');
	include('../php/conexion.php');
	include('../php/cobrador.php');
	date_default_timezone_set('America/Mexico_City');
	$Fecha_hoy = date('Y-m-d');
	$id_user = $_SESSION['user_id'];
	$Hora = date('H:i:s');
	?>
</head>
<!-- RECIBIMOS EL ID DEL REPORTE DESDE EL ARCHIVO reportes_sicflix.php PARA COMPORBAR SI ES VERDADERO -->
<main>
	<?php
	if (isset($_POST['id_reporte_sicflix']) == false) {
	?>
	<script>
		function atras(){
		M.toast({html: "Regresando...", classes: "rounded"})
		setTimeout("location.href='reportes_sicflix.php'", 800);
		}
		atras();
	</script>
	<?php
	}else{
		//Cliente, reporte
		$id_reporte = $_POST['id_reporte_sicflix'];
		// OBTENEMOS LAS VARIABLES QUE NECECITAMOS
		//$id_pago = $_POST['id_pago'];
		$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id = $id_reporte"));
		$id_cliente = $resultado['cliente'];
		$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
		$id_user=$resultado['registro'];
		$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
		$id_comunidad = $cliente['lugar'];
		$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
		$paquete = $resultado['paquete'];
		$p_paquete = $resultado['precio_paquete'];
		?>
		<body>
			<div class="container">
				<h3 class="hide-on-med-and-down">Activar Sicflix para cliente <?php echo $id_cliente;?></h3>
				<h5 class="hide-on-large-only">Activar Sicflix para cliente <?php echo $id_cliente; ?></h5>
				<br><br>
				<!-- DIV PARA MOSTRAR LA INFORMACION DE LA FUNCIÓN -->
				<div id="resultado_activar"></div>
				<!-- CUADRO CON LOS DATOS DEL CLIENTE -->
				<div class="row">
					<ul class="collection">
						<li class="collection-item avatar">
							<img src="../img/cliente.png" alt="" class="circle">
							<span class="title"><b>No. Cliente: </b><?php echo $cliente['id_cliente'];?></span>
							<p><b>Nombre(s): </b><?php echo $cliente['nombre'];?><br>
								<b>Telefono: </b><?php echo $cliente['telefono'];?><br>
								<b>Extención: </b><?php echo $cliente['tel_servicio'];?><br>
								<b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
								<b>Tipo de paquete:  <a class="blue-text"><?php echo $paquete;?></a></b><br>
								<b>Precio: </b><?php echo "$". $p_paquete;?><br>
								<b>Descripción: </b><?php echo $resultado['descripcion'];?><br>
								<b>Registro: </b><?php echo $usuario['firstname'].' ('.$usuario['user_name'].')'; ?><br>
								<span class="new badge pink hide-on-med-and-up" data-badge-caption="<?php echo $resultado['fecha'];?>"></span><br>
							</p>
							<a class="secondary-content "><span class="new badge pink hide-on-small-only" data-badge-caption="<?php echo $resultado['fecha_registro'];?>"></span></a>
						</li>
					</ul>
				</div>
				<!-- CUADRO CON EL NOMBRE DE USUARIO Y CONTRASEÑA -->
				<div class="row">
					<ul class="collection">
						<li class="collection-item avatar">
							<!-- LE SUME 100 AL NÚMERO DE USUARIO -->
							<p><b>Nombre de usuario: </b><?php echo $id_cliente+100;?><br>
								<b>Contraseña: </b><?php echo $cliente['telefono'];?><br>
							</p>
						</li>
					</ul>
					<form class="col s12">
						<a onclick="activar();" class="waves-effect waves-light btn pink right col l3 m3 s8"><i class="material-icons right">send</i>ACTIVAR SICFLIX</a>
					</form>
				</div>  
			</div><!-- FIN DEL CONTAINER --> 
			<br>
		</body>
	<?php
	}
	?>
</main>
</html>