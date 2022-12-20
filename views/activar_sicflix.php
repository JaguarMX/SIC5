<!-- FUNCIÓN QUE SE ACTIVA CON EL BOTON DE: ACTIVAR SICFLIX -->
<script>
	function activar() {

		var textoID_Reporte = $("input#id_reporte").val();
		var textoEstatus = $("input#estatus").val();
    	var textoFecha_Atendio = $("input#fecha_atendio").val();
    	var textoAtendio = $("input#atendio").val();
    	var textoUsuario_Sicflix = $("input#usuario_sicflix").val();
    	var textoContraseña = $("input#contraseña").val();
		var textoId_Cliente = $("input#id_cliente").val();
		var textoSolucion = $("input#solucion").val();

		if (textoSolucion == "" || textoSolucion == 0) {
      		M.toast({html: 'El campo Solución se encuentra vacío o en 0.', classes: 'rounded'});
    	}else{

			$.post("../php/activados_sicflix.php", {

				valorID_Reporte: textoID_Reporte,
				valorEstatus: textoEstatus,
				valorFecha_Atendio: textoFecha_Atendio,
				valorAtendio: textoAtendio,
				valorUsuario_Sicflix: textoUsuario_Sicflix,
				valorContraseña: textoContraseña,
				valorId_Cliente: textoId_Cliente,
				valorSolucion: textoSolucion
			}, function(mensaje) {
				$("#resultado_activar").html(mensaje);
			});
		}
  	};
	function cancelar() {
		var a = document.createElement("a");	
    	a.href = "../views/reportes_sicflix.php";
    	a.click()
  	};
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
		$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id = $id_reporte"));
		$id_cliente = $resultado['cliente'];
		$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
		$id_user=$resultado['registro'];
		$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
		$id_comunidad = $cliente['lugar'];
		$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
		$paquete = $resultado['paquete'];
		$p_paquete = $resultado['precio_paquete'];
		$estatus = $resultado['estatus'];
		?>
		<body>
			<div class="container">
				<h3 class="hide-on-med-and-down">¿Desea activar SICFLIX para cliente <?php echo $id_cliente;?>?</h3>
				<h5 class="hide-on-large-only">¿Desea activar SICFLIX para cliente <?php echo $id_cliente; ?>?</h5>
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
				<?php
					//<!-- LE SUME 10,000,000 AL NÚMERO DE USUARIO PARA QUE TENGA 8 DÍGITOS-->
					//DEINIMOS EL NOMBRE DE USUARIO
					$auxsic='SICFLIX-';
					$no_usuario=$auxsic.$id_cliente;
					//GENERAMOS CONTRASEÑA ALEATORIA
					$caracteres='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
					$longpalabra=8;
					for($pass='', $n=strlen($caracteres)-1; strlen($pass) < $longpalabra ; ) {
  						$x = rand(0,$n);
  						$pass.= $caracteres[$x];
					}
					//CONTRSEÑA OBTENIDA ES $pass;
					//ACTIVAMOS LA VARIABLE $estatus;
					$estatus=$estatus+1;
				?>
				<!-- CUADRO CON EL NOMBRE DE USUARIO Y CONTRASEÑA -->
				<div class="row">
					<ul class="collection">
						<li class="collection-item avatar">
							<p><b>Nombre de usuario: </b><?php echo $no_usuario;?><br>
								<b>Contraseña: </b><?php echo $pass;?><br>
							
							<br>
							<div class="input-field col s12 m7 l7">
								<b>Solución: </b><input id="solucion" type="text" class="validate" data-length="50" required>
								<label for="solución">Motivo por el cual se dará de alta</label>
      						</div>
							</p>
						</li>
					</ul>
					<form class="col s12">
						<!-- VARIABLES PARA INSERTAR QUE SE MANDAN A LA FUNCIÓN activar() -->
						<input id="id_reporte" name="id_reporte" type="hidden" value="<?php echo $id_reporte ?>">
        				<input id="estatus" name="estatus" type="hidden" value="<?php echo $estatus ?>">
        				<input id="fecha_atendio" name="fecha_atendio" type="hidden" value="<?php echo $Fecha_hoy ?>">
        				<input id="atendio" name="atendio" type="hidden" value="<?php echo $id_user ?>">
        				<input id="usuario_sicflix" name="usuario_sicflix" type="hidden" value="<?php echo $no_usuario ?>">
        				<input id="contraseña" name="contraseña" type="hidden" value="<?php echo $pass ?>">
						<input id="id_cliente" name="id_cliente" type="hidden" value="<?php echo $id_cliente ?>">
						<a onclick="activar();" class="waves-effect waves-light btn pink right col l3 m3 s8"><i class="material-icons right">send</i>ACTIVAR SICFLIX</a>
						<a onclick="cancelar();" class="waves-effect waves-light btn pink left col l3 m3 s8"><i class="material-icons left">arrow_back</i>CANCELAR</a>
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