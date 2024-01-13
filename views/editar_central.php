<html>
<head>
	<title>SIC | Editar Central</title>
</head>
<?php 
  include('fredyNav.php');
  include('../php/cobrador.php');
  if (isset($_POST['id_central']) == false) {
    ?>
    <script>    
      M.toast({html: "Regresando a centrales.", classes: "rounded"});
      setTimeout("location.href='centrales.php'", 800);
    </script>
    <?php
  }else{
  $id_central = $_POST['id_central'];
  $central = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales WHERE id='$id_central'"));
  $id_comunidad = $central['comunidad'];
  $id_paquete = $central['paqueteInternet'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
  $paqueteInternet = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$id_paquete'"));
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
  if ($central['paqueteInternet'] !=0) {
    $paqueteInternetCheck = "checked";
    $selectStyle = "block";
  }else{
    $paqueteInternetCheck = "";
    $totalPaqueteInternet = 0;
    $selectStyle = "none";
  }
  if ($central['montoRenta'] !=0) {
    $rentaCheck = "checked";
    $rentaStyle = "block";
  }else{
    $rentaCheck= "";
    $rentaStyle = "none";
  }
  if ($central['pagoCfe'] !=0) {
    $cfeCheck = "checked";
  }else{
    $cfeCheck= "";
  }
?>
<script>
function update_central(IdCentral) {
    var textoNombres = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDireccion = $("textarea#direccion").val();
    var textoCoordenada = $("input#coordenadas").val();
    var inputRenta = 0;
    var inputCfe = 0;

    if (document.getElementById('checkCfe').checked==true) {
      inputCfe = 1;
    } else {
      inputCfe = 0;
    }

    if (document.getElementById('checkInternet').checked==true) {
      var textoPaquete = $("select#selectPaquetes").val();
    } else {
      var textoPaquete = 0;
    }

    if (document.getElementById('checkRenta').checked==true) {
      var textoMontoRenta = $("input#inputRenta").val();
    } else {
      var textoMontoRenta = 0;
    }

    if (textoNombres == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono.length < 10){
      M.toast({html: 'El telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoDireccion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else if(textoPaquete == 0 && document.getElementById('checkInternet').checked==true ){
      M.toast({html: 'Seleccione un paquete.', classes: 'rounded'});
    }else if(textoMontoRenta == "" && document.getElementById('checkRenta').checked==true || textoMontoRenta == 0 && document.getElementById('checkRenta').checked==true ){
      M.toast({html: 'Introduza el monto de renta.', classes: 'rounded'});
    }else{
      $.post("../php/update_central.php", {
          valorIdCentral: IdCentral,
          valorNombres: textoNombres,
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

<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Editar Central</h3>
      <h5 class="hide-on-large-only">Editar Central</h5>
  </div>
  <div id="resultado_central">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="row">  
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombres" type="text" class="validate" data-length="30" value="<?php echo $central['nombre'];?>" required>
          <label for="nombres">Nombre:</label>
        </div> 
        <div class="input-field">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" value="<?php echo $central['telefono'];?>" required>
          <label for="telefono">Teléfono:</label>
        </div>               
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <textarea id="direccion" class="
         materialize-textarea validate" data-length="100" required><?php echo $central['direccion'];?></textarea>
          <label for="direccion">Direccion:</label>
        </div>
        <h5>Pagos que recibe la central</h5><br>
        <div class="col s3 m3 l3">
          <input type="checkbox" id="checkInternet" name="checkInternet" class="filled-in" onclick="showSelectPaquetes();" <?php echo $paqueteInternetCheck;?>/>
          <label for="checkInternet">Internet</label> 
        </div>
        <div class="col s3 m3 l3">
          <input type="checkbox" class="filled-in" id="checkRenta" name="checkRenta" onclick="showInputRenta();" <?php echo $rentaCheck;?>/>
          <label for="checkRenta">Renta</label>
        </div>
        <div class="col s3 m3 l3">
          <input type="checkbox" class="filled-in" onclick="showInputCfe();" <?php echo $cfeCheck;?>  id="checkCfe" />
          <label for="checkCfe">Servicio CFE</label>
        </div>
      </div>
         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <input id="coordenadas" type="text" class="validate" data-length="6" value="<?php echo $central['coordenadas'];?>" required value="0">
          <label for="coordenadas">Coordenadas:</label>
        </div>
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="comunidad" class="browser-default col s11" required>
            <option value="<?php echo $comunidad['id_comunidad'];?>" selected><?php echo $comunidad['nombre'];?></option>
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
        <select id="selectPaquetes" class="browser-default col s12" required <?php echo "style='display:".$selectStyle."'" ?>>
        <?php
          if($central['paqueteInternet'] ==0){
            echo '<option value="0" selected>No se ha seleccionado ningun paquete.</option>';
          }else{
            
         
         ?>
        <option value="<?php echo $paqueteInternet['id_paquete'];?>" selected><?php echo $paqueteInternet['bajada']."--".$paqueteInternet['descripcion']."--$".$paqueteInternet['mensualidad'];?></option>
        <?php
        }
        ?>
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
              <input id="inputRenta" type="number" value="<?php echo $central['montoRenta'];?>" class="validate" data-length="13" <?php echo "style='display:".$rentaStyle."'" ?>>
              <label for="inputRenta" id="labelRenta" <?php echo "style='display:".$rentaStyle."'" ?>> Monto mensual de renta:</label>
            </div>   
            <div class="input-field">
              <input id="inputCFE" type="number" class="validate" data-length="13" style="display: none;">
              <label for="inputCFE" id="labelCFE" style="display: none;">Monto del recibo CFE:</label>
            </div>   
        </div>
      </div>
      </div>
    </div>
</form>
      <a onclick="update_central(<?php echo $central['id'];?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</body>
<?php } ?>
</html>
