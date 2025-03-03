<!DOCTYPE html>
<html>
<head>
  <title>SIC | Stock</title>
</head>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include('fredyNav.php');
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#INCLUIMOS UN ARCHIVO QUE PROHIBE EL ACCESO A ESTA VISTA A LOS USUARIOS CON EL ROL DE COBRADOR 
include('../php/cobrador.php');
#VERIFICAMOS SI CON EL METODO POST ESTAMOS RECIBIENDO ALGUN VALOR DE LA VARIABLE id_tecnico
if (isset($_POST['id_tecnico']) == false AND isset($_GET['id_tecnico'])== false) {
?>
  <script>
    M.toast({html: "Regresando a listado tecnicos...", classes: "rounded"});
    //REDIRECCIONAMOS A LA VISTA stock.php (RETROCEDEMOS)
    setTimeout("location.href='stock.php'", 1000);
  </script>
<?php
}else{
  #SI EL VALOR NO ESTA VACIO RECIVIMOS LA VARIAVLE Y LA GUARDAMOS $id_tecnico Y MOSTRAMOS TODO EL CONTENIDO DE LA PAGINA
  if (isset($_POST['id_tecnico']) == false) {
    $id_tecnico = $_GET['id_tecnico'];
  }else{  $id_tecnico = $_POST['id_tecnico']; }
?>
<script>
  //FUNCION QUE VERIFICARA LA EMIMINACION DE MATERIAL Y ABRIRA EL MODAL PARA PEDRI EL MOTIVO (OPCION RESTRINGIDA)
  function verificar_eliminar(serie){
    textoSerie = serie;
    $.post("../php/verificar_eliminar_material.php", {
          valorSerieV: textoSerie,
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        }); 
   };
  //FUNCION QUE SEGUN LA OPCION QUE ELIGAN DEL MATERIAL A REGISTRAR MUESTRA UN FORMULARIO U OTRO
  function showContent() {
    element = document.getElementById("content");
    element2 = document.getElementById("content2");
    element3 = document.getElementById("content3");
    element4 = document.getElementById("content4");
    element5 = document.getElementById("content5");
    element6 = document.getElementById("content6");
    element7 = document.getElementById("content7");

    var textoTipo = $("select#tipo").val();

    if (textoTipo == 'Antena') { element.style.display='block'; }  
    else { element.style.display='none';  }

    if (textoTipo == 'Router') { element2.style.display='block'; }  
    else { element2.style.display='none'; }

    if (textoTipo == 'Bobina') { element3.style.display='block'; }  
    else { element3.style.display='none'; }

    if (textoTipo == 'Tubo(s)') { element4.style.display='block'; } 
    else { element4.style.display='none'; }

    if (textoTipo == 'Grabador' || textoTipo == 'Camara') { 
      element5.style.display='block';
      element7.style.display='block';
    }else{
      element5.style.display='none';
      element7.style.display='none';
    }
    if (textoTipo == 'Otro') { 

      element6.style.display='block';
      element7.style.display='block';
    }else{
      element6.style.display='none';
    }

    var textoTipo2 = $("select#tipo2").val();

    elementdos = document.getElementById("content.2");
    element2dos = document.getElementById("content2.2");
    element4dos = document.getElementById("content4.2");


    if (textoTipo2 == 'Antena') { elementdos.style.display='block'; }  
    else { elementdos.style.display='none';  }

    if (textoTipo2 == 'Router') { element2dos.style.display='block'; }  
    else { element2dos.style.display='none'; }

    if (textoTipo2 == 'Tubo(s)') { element4dos.style.display='block'; } 
    else { element4dos.style.display='none'; }

  };
  //FUNCION QUE ENVIA LA INFORMACION PARA QUE SE VALLA AGREGANDO EL MATERIAL A EL STOCK DEL USUARIO
  function update_stock(id) {
      if (id == 1) {
        var textoTipo = $("select#tipo").val();
        var textoIdTecnico = $("input#tecnico").val();
        var textoRuta = $("input#ruta").val();
        var textoEs = $("input#es").val();
        if (textoTipo == 'Antena') {
          var textoNombre = $("select#nombreA").val();
          var textoSerie = $("input#serieA").val();
          textoCantidad = 1;
        }else if (textoTipo == 'Router') {
          var textoNombre = $("select#nombreR").val();
          var textoSerie = $("input#serieR").val();
          textoCantidad = 1;
        }else if (textoTipo == 'Bobina') {
          textoNombre = 'Bobina Nueva';
          textoSerie = '111111';
          textoCantidad = 300;
        }else if (textoTipo == 'Tubo(s)') {
          textoNombre = 'Tubos';
          textoSerie = '222222';
          var textoCantidad = $("input#cantidad").val();
        }else if (textoTipo == 'Grabador' || textoTipo == 'Camara') {
          var textoNombre = $("select#marca").val();serieDisp
          textoSerie = $("input#serieDisp").val();
          textoCantidad = 1;
        }else if (textoTipo == 'Otro') {
          var textoNombre = $("input#nomDisp").val();serieDisp
          textoSerie = $("input#serieDisp").val();
          textoCantidad = 1;
        }

      }else if (id == 2) {
        var textoTipo = $("select#tipo2").val();
        var textoIdTecnico = $("input#tecnico2").val();
        var textoRuta = $("input#ruta2").val();
        var textoEs = $("input#es2").val();

        if (textoTipo == 'Antena') {
          var textoNombre = $("select#nombreA2").val();
          var textoSerie = $("input#serieA2").val();
          textoCantidad = 1;
        }else if (textoTipo == 'Router') {
          var textoNombre = $("select#nombreR2").val();
          var textoSerie = $("input#serieR2").val();
          textoCantidad = 1;
        }else if (textoTipo == 'Tubo(s)') {
          textoNombre = 'Tubos';
          textoSerie = '222222';
          var textoCantidad = $("input#cantidad2").val();
        }
      }

      if(document.getElementById('regreso').checked==true){ textoRegreso = 'Si'; }
      else{ textoRegreso = 'No'; }

      if(textoTipo == 0){
        M.toast({html:"Elige un tipo para agregar...", classes: "rounded"})
      }else if(textoNombre == 0){
        M.toast({html:"Elige un nombre...", classes: "rounded"})
      }else if(textoSerie == ""){
        M.toast({html:"Ingrese la serie...", classes: "rounded"})
      }else if(textoCantidad == "" || textoCantidad <= 0){
        M.toast({html:"Ingrese un valor correcto en cantidad", classes: "rounded"})
      }else if(textoRuta == "" || textoRuta <= 0){
        M.toast({html:"Ingrese un numero de ruta..", classes: "rounded"})
      }else{
        $.post("../php/inster_to_stock.php", {
            valorIdTecnico: textoIdTecnico,
            valorTipo: textoTipo,
            valorNombre: textoNombre,
            valorSerie: textoSerie,
            valorCantidad: textoCantidad,
            valorRegreso: textoRegreso,
            valorRuta: textoRuta,
            valorEs: textoEs,
        }, function(mensaje) {
            $("#resultado_update_stock").html(mensaje);
        });
      }
  };
</script>
<body>
  <div class="container" id="resultado_update_stock">
    <div class="row">
      <h3 class="hide-on-med-and-down">Stock:</h3>
      <h5 class="hide-on-large-only">Stock:</h5>
    </div>
    <?php   
    #SELECCIONAMOS LA INFORMACION DEL TECNICO QUE SACAMOS DE LA VARIABLE QUE RECIBIMOS ID $id_tecnico
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tecnico"));
    #CONTAMOS CUANTAS ANTENAS TIENE EN STOCK ESTE USUARIO
    $Antenas = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Antena'"));
    #CONTAMOS CUANTOS ROUTERS TIENE EN ESTCOK ESTE USUARIO
    $Routers = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Router'"));
    #SELECCIONAMOS SI TIENE BOBINA DISPONIBLE AUN EN STOCK
    $bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Bobina'"));
    $CantidadB = $bobina['cantidad']-$bobina['uso'];//SACAMOS CUANTOS METROS LE QUEDA A SU BOBINA
    $totalC = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
    $totalU = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(uso) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
    #SACANOS EL TOTAL DE TUBOS QUE LE QUEDAN VIENDO CUANTOS TUBOS A USADO Y CUANTOS HAY DISPONIBLES
    $Tubos = $totalC['total']-$totalU['total'];
    ?>
    <div class="row">
      <ul class="collection">
            <li class="collection-item avatar">
              <!-- MOSTRAMOS LA INFORMACION DEL USUARIO Y SU STOCK QUE TIENE -->
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Id: </b><?php echo $id_tecnico;?></span>
              <p><b>Nombre: </b><?php echo $datos['firstname'].' '.$datos['lastname'];?><br>
                <b>Antenas: <?php echo $Antenas['total'];?></b><br>                  
                <b>Routers: <?php echo $Routers['total'];?></b><br>                  
                <b>Metros de bobina: <?php echo $CantidadB;?></b><br>                  
                <b>Tubos: <?php echo $Tubos;?></b><br>
                <hr>
                <!-- BOTON QUE SIRVE PARA VER UN HISTRORIAL DEL STOCK QUE A USARO, QUE DIA ETC, -->
                <a href="history_stock.php?id=<?php echo $id_tecnico;?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>Historial</a><br>
              </p>
              <br>
            </li>
      </ul>
      <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="row">
        <div class="col s12">
          <ul id="tabs-swipe-demo" class="tabs">
            <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">General</a></li>
            <li class="tab col s6"><a class="black-text" href="#test-swipe-2">Devoluciones</a></li>
          </ul>
        </div>
        <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
        <div  id="test-swipe-1" class="col s12">
          <!--FORMULARIO QUE SIRVE PARA AGREGAR MATERIAL AL STOCK DEL USUARIO -->
          <form class="row col s12 m12 l12"><br>
            <h3>Agregar:</h3>
            <div class="input-field col s12 m3 l3">
              <div class="input-field col s12 m12 l12">
                <select id="tipo" class="browser-default" required onchange="javascript:showContent()">
                  <option value="0" selected>Tipo: </option>
                  <option value="Antena">Antena</option>
                  <option value="Router">Router</option>
                  <option value="Bobina">Bobina Nueva</option>
                  <option value="Tubo(s)">Tubo(s)</option>
                  <option value="Grabador">Grabador</option>
                  <option value="Camara">Camara</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
            </div>

            <div class="input-field col s12 m3 l3" id="content5" style="display: none;">
              <!--CONTENIDO PARA ANTENA-->
              <div class="input-field col s12 m12 l12">
                <select id="marca" class="browser-default col s11" required>
                  <option value="0" selected>Marca:</option>
                  <option value="Provision">Provision</option>
                  <option value="Hik-Vision">Hik-Vision</option>
                  <option value="Dahua">Dahua</option>
                  <option value="Epcom">Epcom</option>  
                </select>
              </div>
            </div>

            <div class="input-field col s12 m3 l3" id="content6" style="display: none;">
              <div class="input-field col s12 m12 l12">
                <input id="nomDisp" type="text" class="validate" data-length="100" required>
                <label for="nomDisp">Nombre Dispositivo:</label>
              </div>
            </div>
            <div class="input-field col s12 m3 l3" id="content7" style="display: none;">
              <div class="input-field col s12 m12 l12">
                <input id="serieDisp" type="text" class="validate" data-length="100" required>
                <label for="serieDisp">Serie:</label>
              </div>
            </div>

            <div class="input-field row col s12 m6 l6" id="content" style="display: none;">
              <!--CONTENIDO PARA ANTENA-->
                <div class="input-field col s12 m6 l6">
                <select id="nombreA" class="browser-default" required>
                  <option value="0" selected>Nombre: </option>
                  <option value="LiteBeam M5">LiteBeam M5</option>
                  <option value="NanoBeam M2">NanoBeam M2</option>
                  <option value="NanoBeam M5">NanoBeam M5</option>
                  <option value="LiteBeam AC">LiteBeam AC</option>
                  <option value="PowerBeam AC">PowerBeam AC</option>
                  <option value="PowerBeam M5">PowerBeam M5</option>
                  <option value="PowerBeam M2">PowerBeam M2</option>
                  <option value="NanoStation AC">NanoStation AC</option>
                  <option value="NanoStation M2">NanoStation M2</option>
                  <option value="NanoStation M5">NanoStation M5</option>
                  <option value="Rocket M2">Rocket M2</option>
                  <option value="Rocket M5">Rocket M5</option>
                  <option value="Rocket AC">Rocket AC</option>
                  <option value="Rocket AC Prism">Rocket AC Prism</option>
                  <option value="MIMOSA B5C">MIMOSA B5C</option>
                  <option value="MIMOSA C5C">MIMOSA C5C</option>
                  <option value="Cambium ePMP">Cambium ePMP</option>
                  <option value="Cambium ePMP Force">Cambium ePMP Force</option>
                </select>
                </div>
                <div class="input-field col s12 m6 l6">
                  <input id="serieA" type="text" class="validate" data-length="100" required>
                  <label for="serieA">Serie:</label>
                </div>
            </div>
            <div class="input-field row col s12 m6 l6" id="content2" style="display: none;">
              <!--CONTENCIDO PARA ROUTER-->
              <div class="input-field col s12 m6 l6">
                <select id="nombreR" class="browser-default col s11" required>
                  <option value="0" selected>Nombre: </option>
                  <option value="Tp-Link">Tp-Link</option>
                  <option value="TELMEX">TELMEX</option>
                  <option value="Tenda">Tenda</option>
                  <option value="Mercusys">Mercusys</option>
                </select>
              </div> 
              <div class="input-field col s12 m5 l5">
                <input id="serieR" type="text" class="validate" data-length="100" required>
                <label for="serieR">Serie:</label>
              </div>         
            </div>
            <div class="input-field row col s12 m3 l3" id="content3" style="display: none;">
              <!--CONTENIDO PARA BOBINA-->
              
                <input type="checkbox" id="regreso"/>
                <label for="regreso">Regreso Bobina Anterior</label>
       
            </div>
            <div class="input-field row col s12 m3 l3" id="content4" style="display: none;">
              <!--CONTENIDO PARA TUBOS-->

                <input id="cantidad" type="number" class="validate" data-length="100" required>
                <label for="cantidad">Cantidad:</label>     
            </div>
            <div class="input-field  col s12 m3 l3">
              <!--CONTENIDO ID RUTA-->
              <div class="input-field col s12 m12 l12">
                <input id="ruta" type="number" class="validate" data-length="100" required>
                <label for="ruta">Ruta:</label>
              </div>
            </div>
            <div class="row">
                <input id="tecnico" value="<?php echo htmlentities($id_tecnico);?>" type="hidden">
                <input id="es" value="Almacen" type="hidden">
                <a onclick="update_stock(1);" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
            </div>  
          </form> 
        </div>
        <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
        <div  id="test-swipe-2" class="col s12">
          <!--FORMULARIO QUE SIRVE PARA AGREGAR MATERIAL AL STOCK DEL USUARIO -->
          <form class="col s12"><br>
            <h3>Devolver:</h3>
            <div class="input-field row col s12 m3 l3">
              <i class="col s1"> <br></i>
              <select id="tipo2" class="browser-default col s11" required onchange="javascript:showContent()">
                <option value="0" selected>Tipo: </option>
                <option value="Antena">Antena</option>
                <option value="Router">Router</option>
                <option value="Tubo(s)">Tubo(s)</option>
              </select>
            </div>
            <div class="input-field row col s12 m6 l6" id="content.2" style="display: none;">
              <!--CONTENIDO PARA ANTENA-->
                <select id="nombreA2" class="browser-default col s11" required>
                  <option value="0" selected>Nombre: </option>
                  <option value="LiteBeam M5">LiteBeam M5</option>
                  <option value="NanoBeam M2">NanoBeam M2</option>
                  <option value="NanoBeam M5">NanoBeam M5</option>
                  <option value="LiteBeam AC">LiteBeam AC</option>
                  <option value="PowerBeam AC">PowerBeam AC</option>
                  <option value="PowerBeam M5">PowerBeam M5</option>
                  <option value="PowerBeam M2">PowerBeam M2</option>
                  <option value="NanoStation AC">NanoStation AC</option>
                  <option value="NanoStation M2">NanoStation M2</option>
                  <option value="NanoStation M5">NanoStation M5</option>
                  <option value="Rocket M2">Rocket M2</option>
                  <option value="Rocket M5">Rocket M5</option>
                  <option value="Rocket AC">Rocket AC</option>
                  <option value="Rocket AC Prism">Rocket AC Prism</option>
                  <option value="MIMOSA B5C">MIMOSA B5C</option>
                  <option value="MIMOSA C5C">MIMOSA C5C</option>
                  <option value="Cambium ePMP">Cambium ePMP</option>
                  <option value="Cambium ePMP Force">Cambium ePMP Force</option>
                </select>
              <div class="input-field col s12 m5 l5">
                <input id="serieA2" type="text" class="validate" data-length="100" required>
                <label for="serieA2">Serie:</label>
              </div>        
            </div>
            <div class="input-field row col s12 m6 l6" id="content2.2" style="display: none;">
              <!--CONTENCIDO PARA ROUTER-->
              <div class="input-field col s12 m6 l6">
                <i class="col s1"> <br></i>
                <select id="nombreR2" class="browser-default col s11" required>
                  <option value="0" selected>Nombre: </option>
                  <option value="Tp-Link">Tp-Link</option>
                  <option value="TELMEX">TELMEX</option>
                  <option value="Tenda">Tenda</option>
                  <option value="Mercusys">Mercusys</option>
                </select>
              </div> 
              <div class="input-field col s12 m5 l5">
                <input id="serieR2" type="text" class="validate" data-length="100" required>
                <label for="serieR2">Serie:</label>
              </div>         
            </div>
            <div class="input-field row col s12 m6 l6" id="content4.2" style="display: none;">
              <!--CONTENIDO PARA TUBOS-->
              <div class="input-field col s12 m6 l6">
                <input id="cantidad2" type="number" class="validate" data-length="100" required>
                <label for="cantidad2">Cantidad:</label>
              </div>        
            </div>
            <div class="input-field row col s12 m3 l3">
              <!--CONTENIDO ID RUTA-->

                <input id="ruta2" type="number" class="validate" data-length="100" required>
                <label for="ruta2">Ruta:</label>
        
            </div>
          </form> 
          <div class="row">
                <input id="tecnico2" value="<?php echo htmlentities($id_tecnico);?>" type="hidden">
                <input id="es2" value="Devolucion" type="hidden">
                <a onclick="update_stock(2);" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
            </div> 
        </div>
      </div>
      <div class="row" id="Continuar">
          <div class="col s2"></div>
          <div class="row col s8">
          <!--CREAMOS UNA TABLA QUE MUESTRA A DETALLE LAS ANTENAS Y ROUTER QUE TIENE EN STOCK -->
          <table class="bordered highlight responsive-table">
            <thead>
              <th>#</th>
              <th>Tipo</th>
              <th>Nombre</th>
              <th>Serie</th>
              <th>Ruta</th>
              <th>Origen</th>
              <?php if(in_array($_SESSION['user_id'], array(59, 66, 49,127))){ ?>
              <th>Borrar</th>
              <?php } ?>
            </thead>
            <tbody>
            <?php
            #SELECCIONAMOS TODOS LOS ROUTERS Y ANTENAS DISPONIBLES EN STOCK
            $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tipo IN ('Antena', 'Router','Grabador','Camara','Otro') AND disponible = 0 AND tecnico = $id_tecnico");
            #SI TIENE DISPONIBLES LA RECORREMOS UNA POR UNA Y MOSTRAMOS LA INFORMACION
            while($unidad = mysqli_fetch_array($tab)){
              ?>
              <tr>
                <td><?php echo $unidad['id']; ?></td>
                <td><?php echo $unidad['tipo']?></td>
                <td><?php echo $unidad['nombre']; ?></td>
                <td><?php echo $unidad['serie']; ?></td>
                <td><?php echo $unidad['ruta']; ?></td>
                <td><?php echo $unidad['es']; ?></td>
                <?php if(in_array($_SESSION['user_id'], array(59, 66, 49, 25, 108,127))){ ?>
                <td><a onclick="verificar_eliminar('<?php echo $unidad['serie'] ?>')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                <?php } ?>
              </tr>
            <?php
            }
            ?> 
            </tbody>
          </table>
          </div>
      </div>    
    </div>
  </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>