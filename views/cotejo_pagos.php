<!DOCTYPE html>
<html>
	<head>
		<title>SIC | Cotejo de Pagos</title>
	<?php 
	include('fredyNav.php');
	include('../php/conexion.php');

	?>
	<script>
		function mostrar_comprobante(imagen, idPago, referencia, idCliente, img) {
			
			$(".midpago").text(idPago);
			$(".mReferencia").text(referencia);
			$('#btnCotejar').replaceWith('<a id="btnCotejar" class="btn waves-light waves-effect center pink modal-close" onclick="buscar_pagos_cotejo('+"'cotejar'"+','+idPago+');"><i class="material-icons prefix right">check</i> Cotejar</a>');
			//$('#imgComprobante').replaceWith('<img id="imgComprobante" src="/sic5/imgReferencias/'+idCliente+'/'+img+'" style="width: 100%" >');
			$('#imgComprobante').replaceWith('<img id="imgComprobante" src="/SIC5.0/imgReferencias/'+idCliente+'/'+img+'" style="width: 100%" >');
			$('#modalComprobante').modal();
			$('#modalComprobante').modal('open'); 
		}

		function buscar_pagos_cotejo(accion, id) {

			if ($("input#fromDateCojeto").val() != '' && $("input#toDateCojeto").val() != '') {
				if (banco = $("select#idbanco").val() != null) {

					var fromDateCotejo = $("input#fromDateCojeto").val();
					var toDateCotejo = $("input#toDateCojeto").val();
					var banco = $("select#idbanco").val();
					
					$.post("../php/buscar_cotejos_pagos.php", {
							fechaInicio: fromDateCotejo,
							fechaFinal: toDateCotejo,
							banco: banco,
							accion: accion,
							idPago: id,
							}, function(mensaje) {
							$("#cPendiente").html(mensaje); 
					});

					$.post("../php/pagos_banco_cotejados.php", {
							fechaInicio: fromDateCotejo,
							fechaFinal: toDateCotejo,
							banco: banco,
							}, function(cotejados) {
							$("#pCotejados").html(cotejados); 
					});
					
				} else {
					M.toast({html:"Selecciona Un Banco", classes: "rounded"});
				}
				
			} else {
				M.toast({html:"Ingresa un rango de fechas valida", classes: "rounded"});
			}
			
		};
	</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<h3 class="hide-on-med-and-down">Cotejo de Pagos</h3>
				<h5 class="hide-on-large-only">Cotejo de Pagos</h5>
			</div>
			<br><br>

			<div class="row">
				<div class="col s12 l4 m4">
					<label for="fromDateCojeto">De:</label>
					<input id="fromDateCojeto" type="date">    
				</div>
				<div class="col s12 l4 m4">
					<label for="toDateCojeto">A:</label>
					<input id="toDateCojeto"  type="date">
				</div>

				<label for="toDateCojeto">Banco</label>
				<div class="input-field col s12 l4 m4">
				<select id="idbanco" class="browser-default">
					<option value="" selected disabled hidden>Seleccione un tipo:</option>
					<option value="BBVA">BBVA</option>
					<option value="BANORTE">BANORTE</option>
					<option value="HSBC">HSBC</option>
				</select>
				</div>

				<div>
				<button class="btn waves-light waves-effect right pink" onclick="buscar_pagos_cotejo('buscar',0);"><i class="material-icons prefix right">search</i> Buscar</button>
				</div>
			</div>	
			<?php
			// $referencias = mysqli_query($conn,"SELECT p.id_pago, r.descripcion, p.fecha, r.banco
			// FROM pagos p
			// JOIN referencias r 
			// ON p.id_pago = r.id_pago
			// WHERE r.banco != ''
			// ");

			// $result = $referencias->fetch_all(MYSQLI_ASSOC);

			// var_dump($result);
			?>
			<!-- ----------------------------  TABs o MENU  ---------------------------------------->
			<div class="row">
				<div class="col s12">
					<ul id="tabs-swipe-demo" class="tabs">
					<li class="tab col s6"><a class="active black-text" href="#test-swipe-1">Pendientes</a></li>
					<li class="tab col s6"><a class="black-text" href="#test-swipe-2">Cotejados</a></li>
					</ul>

				</div>
				<!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
				<div  id="test-swipe-1" class="col s12">
					<div class="row">
						<div id="cPendiente">

						</div>
					</div>
				</div>

				<!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
				<div  id="test-swipe-2" class="col s12">

					<div class="row">
						<div id="pCotejados">

						</div>
					</div>
					
				</div>

				
			</div>
			<!-- <div id="cPendientes">
			</div>         -->
		</div>

		<div id="modalComprobante" class="modal">
			<div class="modal-content">
				<h4><b>Id Pago: </b><strong class="midpago">Sin Id Pago</strong></h4> 
				<b>Referencia: </b> <strong class="mReferencia">Sin Referencia</strong>
				
				<div style="max-height: 250px; max-width: 100%; overflow: scroll;">
					<!-- <img id="imgComprobante" src="/sic5/imgReferencias/1111.png" style="width: 100%" > -->
					<img id="imgComprobante" src="/SIC5.0/imgReferencias/1111.png" style="width: 100%" >
				</div>
				
			</div>
			<div class="modal-footer">
				<a id="btnCotejar" class="btn waves-light waves-effect center pink modal-close" onclick="buscar_pagos_cotejo('dadasda',0);"><i class="material-icons prefix right">check</i> Cotejar</a>
				<a class="btnCotejar modal-close waves-effect waves-green btn-flat">Salir</a>
				
			</div>
		</div>
	</body>
</html>