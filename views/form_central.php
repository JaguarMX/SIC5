<html>
<head>
	<title>SIC | Formulario Centrales</title>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
?>
<script>
function insert_central() {
    var textoNombres = $("input#nombres").val();
    var textoAM = $("input#apellido-M").val();
    var textoAP = $("input#apellido-P").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDireccion = $("textarea#direccion").val();
    var textoCoordenada = $("input#coordenadas").val();
    var textoPaquete = $("select#selectPaquetes").val();
    var textoMontoRenta = $("input#inputRenta").val();
    var inputRenta = 0;
    var inputCfe = 0;
   

    if (document.getElementById('checkCfe').checked==true) {
      inputCfe = 1;
    } else {
      inputCfe = 0;
    }

    if (textoNombres == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoAM == ""){
      M.toast({html: 'El campo Apellido Materno se encuentra vacío.', classes: 'rounded'});
    }else if(textoAP == ""){
      M.toast({html: 'El campo Apellido Paterno se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono.length < 10){
      M.toast({html: 'El telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoDireccion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else if(textoPaquete == 0 && document.getElementById('checkInternet').checked==true ){
      M.toast({html: 'Seleccione un paquete.', classes: 'rounded'});
    }else if(textoMontoRenta == "" && document.getElementById('checkRenta').checked==true || textoMontoRenta == 0 && document.getElementById('checkRenta').checked==true){
      M.toast({html: 'Introduza el monto de renta.', classes: 'rounded'});
    }else{
      $.post("../php/insert_central.php", {
          valorNombres: textoNombres+' '+textoAP+' '+textoAM,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDireccion: textoDireccion,
          valorCoordenada: textoCoordenada,
          valorPaquete: textoPaquete,
          valorRenta: textoMontoRenta,
          valorCfe: inputCfe, 
        }, function(mensaje) {
            $("#resultado_central").html(mensaje);
        }); 
    }
};

  function showSelectPaquetes() {
    selectPaquetes = document.getElementById("selectPaquetes");
    if (document.getElementById('checkInternet').checked==true) {
      selectPaquetes.style.display='block';
    } else {
      selectPaquetes.style.display='none';
    }
        
  };

 function showInputRenta() {
    inputRenta = document.getElementById("inputRenta");
    labelRenta = document.getElementById("labelRenta");
    if (document.getElementById('checkRenta').checked==true) {
      inputRenta.style.display='block';
      labelRenta.style.display='block';
    } else {
      inputRenta.style.display='none';
      labelRenta.style.display='none';
    }
        
  };

  function showInputCfe() {
    inputCfe = document.getElementById("inputCFE");
    labelCfe = document.getElementById("labelCFE");
    if (document.getElementById('checkCfe').checked==true) {
     
    } else {
      
    }
        
  };

</script>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Central</h3>
      <h5 class="hide-on-large-only">Registrar Central</h5>
  </div>
  <div id="resultado_central">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="row">
        <div class="input-field col s12 m4 l4">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombres" type="text" class="validate" data-length="30" required>
          <label for="nombres">Nombre:</label>
        </div> 
        <div class="input-field col s12 m4 l4">
          <input id="apellido-P" type="text" class="validate" data-length="30" required>
          <label for="apellido-P">Apellido Paterno:</label>
        </div> 
        <div class="input-field col s12 m4 l4">
          <input id="apellido-M" type="text" class="validate" data-length="30" required>
          <label for="apellido-M ">Apellido Materno:</label>
        </div> 
        
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" required>
          <label for="telefono">Teléfono:</label>
        </div>               
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <textarea id="direccion" class="materialize-textarea validate" data-length="100" required></textarea>
          <label for="direccion">Direccion:</label>
        </div>
        <h5>Pagos que recibe la central</h5><br>
        <div class="col s3 m3 l3">
          <input type="checkbox" id="checkInternet" name="checkInternet" class="filled-in" onclick="showSelectPaquetes();" />
          <label for="checkInternet">Internet</label> 
        </div>
        <div class="col s3 m3 l3">
          <input type="checkbox" class="filled-in" id="checkRenta" name="checkRenta" onclick="showInputRenta();"/>
          <label for="checkRenta">Renta</label>
        </div>
        <div class="col s3 m3 l3">
          <input type="checkbox" class="filled-in" onclick="showInputCfe();" id="checkCfe"/>
          <label for="checkCfe">Servicio CFE</label>
        </div>
      </div>
      
         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <input id="coordenadas" type="text" class="validate" data-length="6" required value="0">
          <label for="coordenadas">Coordenadas:</label>
        </div><br>
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="comunidad" class="browser-default col s11" required>
            <option value="0" selected>Comunidad</option>
            <?php
            require('../php/conexion.php');
                $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
                while($comunidad = mysqli_fetch_array($sql)){
                  ?>
                    <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'];?></option>
                  <?php
                } 
            ?>
          </select>
        </div>
        <div class="col s12 m12 l12"><br>
        <select id="selectPaquetes" class="browser-default col s12" required style="display: none;">
            <option value="0" selected>Seleccione un paquete</option>
            <?php
            require('../php/conexion.php');
                $sql = mysqli_query($conn,"SELECT * FROM paquetes ORDER BY bajada ASC");
                while($paquetes = mysqli_fetch_array($sql)){
                  ?>
                    <option value="<?php echo $paquetes['id_paquete'];?>"><?php echo $paquetes['bajada']."--".$paquetes['descripcion']."--$".$paquetes['mensualidad'];?></option>
                  <?php
                } 
            ?>
          </select>
          <div class="col s6 m6 l6 id"><br>
            <div class="input-field">
              <input id="inputRenta" type="number" class="validate" data-length="13" style="display: none;">
              <label for="inputRenta" id="labelRenta" style="display: none;">Monto mensual de renta:</label>
            </div>   
            <div class="input-field">
              <input id="inputCFE" type="number" class="validate" data-length="13" style="display: none;">
              <label for="inputCFE" id="labelCFE" style="display: none;">Monto del recibo CFE:</label>
            </div>   
        </div>
        </div>
      </div>
      </div>
    </div>
</form>
      <a onclick="insert_central();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</body>
</main>
</html>
