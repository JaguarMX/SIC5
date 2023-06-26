<!DOCTYPE html>
<html>
<head>
	<title>SIC | Orden de Servicio</title>
<?php
include('fredyNav.php');
include ('../php/cobrador.php');
?>
<script>
	
	function showContent() {
    element2 = document.getElementById("content_o");

	elementoBancos 		= document.getElementById("bancos");
	elementoReferencia 	= document.getElementById("referencia");

    var textoEstatus = $("select#estatus").val();

    if (textoEstatus == 'Cotizado') {
      element2.style.display='block';
    }
    else {
      element2.style.display='none';
    }

	if(document.getElementById('bancoOrden').checked==true) {

            elementoBancos.style.display='block';
            elementoReferencia.style.display='block';
        } else {
            elementoBancos.style.display='none';
            elementoReferencia.style.display='none';
        }
        if (document.getElementById('sanOrden').checked==true || document.getElementById('banco').checked==true) {
            elementoReferencia.style.display='block';
        } else {
            elementoReferencia.style.display='none';
    }
  };
function buscar() {
    var texto = $("input#nombresC").val();
	$.post("../php/orden_cliente.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
};
function create_orden() {
    var textoNombreC 		= $("input#nombresC").val();
    var textoTelefono 		= $("input#telefono").val();
    var textoComunidad 		= $("select#comunidad").val();
    var textoEstatus 		= $("select#estatus").val();
    var textoDpto 			= $("select#dpto").val();
    var textoSolicitud 		= $("textarea#solicitud").val();
    var textoColor 			= $("textarea#color").val();
    var textoCerca 			= $("textarea#cercas").val();    
    var textoEsp 			= $("textarea#especificacion").val();
	var textoCalleOrden 	= $("input#calleOrden").val();
	var textoNumeroOrden 	= $("input#numeroOrden").val();
	var textoColoniaOrden 	= $("input#coloniaOrden").val();
	var textoReferencia		= $("input#referenciaOrden").val();
	var textoBanco			= $("select#bancosOrden").val();

	if(document.getElementById('bancoOrden').checked==true){
		textoTipo_pago = "Banco";
		}else if (document.getElementById('sanOrden').checked==true) {
		textoTipo_pago = "SAN";
		}else{
		textoTipo_pago = "Efectivo";
	}

	if ($("input#anticipo_orden").val()) {
		var textoAnticipo = $("input#anticipo_orden").val();

	}else{
		var textoAnticipo = 0;
	}
	 
    No = 'si';
    if (textoEstatus == 'Cotizado') {
      var textoCosto = $("input#costo").val();
      if (textoCosto == '' || textoCosto <= 0) {
        No = 'No';
        text = 'Colocar un costo valido a la orden.';
      }else{
        No = 'Si';
      }
    }else{
    	textoCosto = 0;
    }

	if (document.getElementById('bancoOrden').checked != false || document.getElementById('sanOrden').checked !=true) {
		if (document.getElementById('bancoOrden').checked == true) {
			if (textoBanco == "0") {
				M.toast({html: 'Debes seleccionar un Banco.', classes: 'rounded'});
			}
			
		}
		if (textoReferencia == "") {
      		M.toast({html: 'El campo Referencia se encuentra vacío.', classes: 'rounded'});
		}
		
	}
  
    if (textoNombreC == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoNombreC.length < 10){
      M.toast({html: 'Almenos debe llevar un Apellido.', classes: 'rounded'});
    }else if(textoTelefono == ""){
      M.toast({html: 'El campo Telefono se encuentra vacío.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoEstatus == "0"){
      M.toast({html: 'No se ha seleccionado un Estatus aún.', classes: 'rounded'});
    }else if(textoDpto == "0"){
      M.toast({html: 'No se ha seleccionado una Departamento aún.', classes: 'rounded'});
    }else if(textoSolicitud == ""){
      M.toast({html: 'El campo Solicitud se encuentra vacío.', classes: 'rounded'});
    }else if(textoColor == ""){
      M.toast({html: 'El campo Color se encuentra vacío.', classes: 'rounded'});
    }else if(textoCerca == ""){
      M.toast({html: 'El campo Cerca De se encuentra vacío.', classes: 'rounded'});
    }else if(textoCalleOrden == ""){
      M.toast({html: 'El campo Calle se encuentra vacío.', classes: 'rounded'});
    }else if(textoNumeroOrden == ""){
      M.toast({html: 'El campo Número se encuentra vacío.', classes: 'rounded'});
    }else if(textoColoniaOrden == ""){
      M.toast({html: 'El campo Colonia se encuentra vacío.', classes: 'rounded'});
    }else if(textoEsp == ""){
      M.toast({html: 'El campo Especificación se encuentra vacío.', classes: 'rounded'});
    }else if(No == "No"){
      M.toast({html:""+text, classes: "rounded"})
    }else{
      $.post("../php/create_orden.php", {
          valorNuevo: 		'Si',
          valorNombres: 	textoNombreC,
          valorTelefono: 	textoTelefono,
          valorComunidad: 	textoComunidad,
          valorEstatus: 	textoEstatus,
          valorDpto: 		textoDpto,
          valorSolicitud: 	textoSolicitud,
          valorCosto: 		textoCosto,
		  valorCalle: 		textoCalleOrden,
          valorNumero: 		textoNumeroOrden,
          valorColonia: 	textoColoniaOrden,
		  valorAnticipo: 	textoAnticipo,
		  valorRefAntic:	textoReferencia,
		  valorTipoPago:	textoTipo_pago,
		  valorBanco:		textoBanco,
          valorReferencia: 'Casa de color: '+textoColor+', Cercas de '+textoCerca+' ('+textoEsp+')'
        }, function(mensaje) {
            $("#orden").html(mensaje);
        }); 
     }
};
</script>
</head>
<body onload="buscar();">
	<div class="container">

	  <div>
	    <h3 class="hide-on-med-and-down">Registar Orden de Servicio</h3>
	    <h5 class="hide-on-large-only">Registar Orden de Servicio</h5>
	  </div>
	  <div id="orden"></div>

	   <div class="row">
	    <div  name="fomulario">

	        <div class="row">

	          <div class="input-field col s12 m7 l7">

	            <i class="material-icons prefix">account_circle</i>
	            <input id="nombresC" type="text" class="validate" data-length="30"  onkeyup="buscar();">
	            <label for="nombresC">Nombre (s)   Apellido Paterno   Apellido Materno:</label>
	          </div> 

	          <div class="input-field col s12 m5 l5">

		        <i class="material-icons prefix">phone</i>
		        <input id="telefono" type="text" class="validate" data-length="13" >
		        <label for="telefono">Teléfono:</label>
		      </div> 

	        </div>

	        <div class="row"  id="datos"></div>

			<div class="row">

				<h6><br><i class="material-icons prefix">add_location</i><b> Dirección</b></h6>

				<div class="input-field col s12 m5 l5">
					<input type="text" id="calleOrden" validate data-length="100" >
					<label for="calleOrden">Calle:</label>
				</div>

				<div class="input-field col s12 m2 l2">
					<input type="number" id="numeroOrden" validate data-length="100" >
					<label for="numeroOrden">Número:</label>
				</div>

				<div class="input-field col s12 m5 l5">
					<input type="text" id="coloniaOrden" validate data-length="100" >
					<label for="coloniaOrden">Colonia:</label>
				</div>

			</div> 

	        <h6><i class="material-icons prefix">comment</i><b> Referencia:</b></h6>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="color" class="materialize-textarea validate" data-length="100" ></textarea>
	          <label for="color">Casa de Color:</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="cercas" class="materialize-textarea validate" data-length="100" ></textarea>
	          <label for="cercas">Cercas De:  ej. (Escuela, Iglesia)</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="especificacion" class="materialize-textarea validate" data-length="150" ></textarea>
	          <label for="especificacion">Especificación: ej. (Dos pisos, Porton blanco)</label>
	        </div>
	        <div class="col s12 m6 l3"> <br>
		        <div class="input-field row">
		          <select id="comunidad" class="browser-default col s10">
		            <option value="0" selected>COMUNIDAD:</option>
		            <?php
		            require('../php/conexion.php');
		                $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
		                while($comunidad = mysqli_fetch_array($sql)){
		                  ?>
		                    <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'].' <==> '.$comunidad['municipio'];?></option>
		                  <?php
		                } 
		            ?>
		          </select>
		        </div>   
		    </div>
		    <div class="col s12 m6 l2"> <br>
		        <div class="input-field row">
		          <select id="estatus" class="browser-default col s11" onchange="javascript:showContent()">
		            <option value="0" selected>Estatus:</option>
		            <option value="PorConfirmar">Por Confirmar(No Seguro)</option>		   
                <option value="Revisar">Revisar(Ver ¿Que Hacer?)</option> 
                <option value="Cotizar">Cotizar(Dar Precio al Cliente)</option> 
                <option value="Cotizado">Cotizado(Precio Fijado)</option>        
		          </select>
		        </div>   
		    </div>
		    <div class="col s12 m6 l2"> <br>
		        <div class="input-field row">
		          <select id="dpto" class="browser-default col s11">
		            <option value="0" selected>Departamento:</option>
		            <option value="1">Redes</option>		            
		            <option value="2">Taller</option>		            
		            <option value="3">Ventas</option>
					<option value="4">CCTV</option>		            
		          </select>
		        </div>   
		    </div>
	        <div class="col s12 m6 l5">
	        	<div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="solicitud" class="
		         materialize-textarea validate" data-length="100"></textarea>
		          <label for="solicitud">Solicitud Del Cliente o Trabajo a Realizar:</label>
		        </div>       
	       </div>

		   <div class="input-field col s12 m4 l4">
				<i class="material-icons prefix">monetization_on</i>
				<input type="number" id="anticipo_orden">
				<label for="anticipo_orden">Anticipo</label>
			</div>

			<div class="col s6 m1 l1">
              <p>
                <br>
                <input type="checkbox" id="bancoOrden" onchange="showContent()" />
                <label for="bancoOrden">Banco</label>
              </p>
            </div>
            <div class="col s6 m1 l1">
              <p>
                <br>
                <input type="checkbox" id="sanOrden" onchange="showContent()"/>
                <label for="sanOrden">SAN</label>
              </p>
            </div>

			<div class="row col s6 m2 l2" id="bancos" style="display: none;"><br>
              <select id="bancosOrden" class="browser-default">
                <option value="0" selected>Banco: </option>
                <option value="BBVA">BBVA</option>
                <option value="BANORTE">BANORTE</option>
                <option value="HSBC">HSBC</option>
              </select>
            </div>

            <div class="col s6 m4 l4" id="referencia" style="display: none;">
              <div class="input-field" >
                <input id="referenciaOrden" type="text" class="validate" data-length="15" required value="">
                <label for="referenciaOrden">Referencia:</label>
              </div>
            </div>

	       	<div class="input-field col s10 m4 l4 right" id="content_o" style="display: none;">
		        <i class="material-icons prefix">attach_money</i>
				<input id="costo" type="number" class="validate" data-length="100" required>
				<label for="costo">Costo de la orden:</label>
			</div>
			

        </div>
		
	    </div>
		
		<a onclick="create_orden();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
		</div>
		
      
  </div> 
</div><br>
</div>
</body>
</html>