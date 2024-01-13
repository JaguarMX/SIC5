<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
  $queryFoliosTaller = "SELECT * FROM dispositivos WHERE estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-02-01' ORDER BY fecha";
  $resultadosQuerieFliosTaller = $conn->query($queryFoliosTaller);
  while($resultadoFolioTaller = $resultadosQuerieFliosTaller->fetch_assoc()){
    $foliosPendientesTaller[] = array("id" => $resultadoFolioTaller['id_dispositivo'], "nombre" => $resultadoFolioTaller['nombre'], "marca" => $resultadoFolioTaller['marca'], "modelo" => $resultadoFolioTaller['modelo'], "color" => $resultadoFolioTaller['color']);
  }
  $jsonFoliosTaller = json_encode($foliosPendientesTaller);
  $queryMantenimientos = "SELECT * FROM reportes  WHERE (atendido != 1 OR atendido IS NULL) AND id_cliente >= 10000 AND descripcion LIKE 'Mantenimiento:%' ORDER BY fecha";
  $resultadosMantenimientos = $conn->query($queryMantenimientos);
  while($row = $resultadosMantenimientos->fetch_assoc()){
    $manteimientos[] = array("id" => $row['id_reporte'], "descripcion" => $row['descripcion']);
    $id_cliente = $row['id_cliente'];
    $queryCliente = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
    $filas = mysqli_num_rows($queryCliente);
    if ($filas == 0) {
      $queryCliente = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
      $cliente = mysqli_fetch_array($queryCliente);
      $lugarCliente = $cliente['lugar'];
      $queryLugarCliente = mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$lugarCliente");
      $comunidadCliente = mysqli_fetch_array($queryLugarCliente);
      $arregloClienteLugar[] = array("id" => $row['id_reporte'], "descripcion" => $row['descripcion'], "nombre" => $comunidadCliente['nombre'], "municipio" => $comunidadCliente['municipio']);
    }
    $cliente = mysqli_fetch_array($queryCliente);
    $arregloCliente[] = array("id" => $row['id_reporte'], "descripcion" => $row['descripcion']);
  }
  $jsonLugaresMantenimientos = json_encode($arregloClienteLugar);
  $queryOrdenesServicio = "SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Cotizar', 'Cotizado', 'Autorizado', 'Pedir', 'Ejecutar') ORDER BY fecha";
  $resultadosOrdenesServicio = $conn->query($queryOrdenesServicio);
  while($resultadoOrdenServicio = $resultadosOrdenesServicio->fetch_assoc()){
    $id_clienteOrden = $resultadoOrdenServicio['id_cliente'];
    $queryClienteOrden = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_clienteOrden");
    $filasOrden = mysqli_num_rows($queryClienteOrden);
    if ($filasOrden == 0) {
      $queryClienteOrden = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_clienteOrden");
      $clienteOrden = mysqli_fetch_array($queryClienteOrden);
      $lugarClienteOrden = $clienteOrden['lugar'];
      $queryLugarClienteOrden = mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$lugarClienteOrden");
      $comunidadClienteOrden = mysqli_fetch_array($queryLugarClienteOrden);
      $arregloClienteLugarOrden[] = array("id" => $resultadoOrdenServicio['id'], "descripcion" => $resultadoOrdenServicio['solicitud'], "nombre" => $comunidadClienteOrden['nombre'], "municipio" => $comunidadClienteOrden['municipio']);
    }
   
  $clienteOrden = mysqli_fetch_array($queryClienteOrden);
  }
  $jsonLugaresOrdenes = json_encode($arregloClienteLugarOrden);
  $queryRutas = "SELECT * FROM rutas  WHERE estatus = 0 ORDER BY id_ruta";
  $resultadoQueryRutas = $conn->query($queryRutas);
  while($resultadoRutas = $resultadoQueryRutas->fetch_assoc()){
    $arregloRutas[] = array("id" => $resultadoRutas['id_ruta'], "responsable" => $resultadoRutas['responsable']);
  }
  $jsonRutas = json_encode($arregloRutas);
?>
<title>SIC | Pedidos</title>
<script>

  <?php
    echo "var foliosTaller = $jsonFoliosTaller; \n";
    echo "var lugaresMantenimientos = $jsonLugaresMantenimientos; \n";
    echo "var lugaresMantenimientosOrdenes = $jsonLugaresOrdenes; \n";
    echo "var rutas = $jsonRutas; \n";
  ?>

function buscar_pedidos(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_pedidos.php", {
      texto: texto,
    }, function(mensaje){
        $("#PedidosALL").html(mensaje);
  });
};
function buscar_pedidos2(){
  var texto = $("input#busqueda2").val();
  $.post("../php/buscar_pedidos2.php", {
      texto: texto,
    }, function(mensaje){
        $("#pedidosNo").html(mensaje);
  });
};
function insert_pedidos() {
    var textoTipoPedido = $("select#selectTipo").val();
    var textoIdOperacion = 0;
    var textoNombre = "";
    var lugar = 0;
    
    if(textoTipoPedido == 1 || textoTipoPedido == 2 || textoTipoPedido == 3 || textoTipoPedido == 4 ){
      textoIdOperacion = $("select#subcatsSelect").val();
    }else if(textoTipoPedido == 5) {
      textoIdOperacion = 0;
      textoNombre = $("input#nombre").val();
    }
    
    var textoFecha = $("input#fecha_req").val();
    if (textoTipoPedido == null) {
      M.toast({html :"Selecciona un tipo de pedido.", classes: "rounded"});
    }else if (textoTipoPedido== 5 && textoNombre == "") {
      M.toast({html :"Ingresa un nombre.", classes: "rounded"});
    }else{
      $.post("../php/insert_pedidos.php", {
          valorIdTipoPedido: textoTipoPedido,
          valorIdOperacion: textoIdOperacion,
          valorNombre: textoNombre,
          valorFecha: textoFecha
        }, function(mensaje) {
            $("#resultado_pedido").html(mensaje);
        }); 
    }
};
function borrar(folio){
  $.post("../php/borrar_pedido.php", { 
          valorFolio: folio
  }, function(mensaje) {
  $("#resultado_pedido").html(mensaje);
  }); 
};
function entregar(folio){
  M.toast({html :"FOLIO: "+folio, classes: "rounded"});
  $.post("../php/entregar_pedido.php", { 
          valorFolio: folio
  }, function(mensaje) {
  $("#resultado_pedido").html(mensaje);
  }); 
};

$(document).on('change','#selectTipo',function(){
   var selectedOption = $(this).find("option:selected").attr('value') ;
   if(selectedOption == 1){
    var selectSubCat = document.getElementById("divSubCat");
    var divNombre = document.getElementById("divNombre");
    var labelSubCatSelect = document.getElementById("labelNombreSubCatSelect");
    labelSubCatSelect.innerText = "Seleccione el servicio";
    divNombre.style.display = "none";
    selectSubCat.style.display = "block";
    updateSelect(selectedOption);
   }else if (selectedOption == 2){
    var selectSubCat = document.getElementById("divSubCat");
    var divNombre = document.getElementById("divNombre");
    var labelSubCatSelect = document.getElementById("labelNombreSubCatSelect");
    labelSubCatSelect.innerText = "Seleccione la ruta";
    divNombre.style.display = "none";
    selectSubCat.style.display = "block";
    updateSelect(selectedOption);
   }else if (selectedOption == 3){
    var selectSubCat = document.getElementById("divSubCat");
    selectSubCat.style.display = "block";
    var divNombre = document.getElementById("divNombre");
    divNombre.style.display = "none";
    var labelSubCatSelect = document.getElementById("labelNombreSubCatSelect");
    labelSubCatSelect.innerText = "Seleccione el mantenimiento";
    updateSelect(selectedOption);
   }else if (selectedOption == 4){
    var selectSubCat = document.getElementById("divSubCat");
    selectSubCat.style.display = "block";
    var divNombre = document.getElementById("divNombre");
    divNombre.style.display = "none";
    var labelSubCatSelect = document.getElementById("labelNombreSubCatSelect");
    labelSubCatSelect.innerText = "Seleccione la orden de servicio";
    updateSelect(selectedOption);
   }else if (selectedOption == 5){
      var divNombre = document.getElementById("divNombre");
      var selectSubCat = document.getElementById("divSubCat");
      divNombre.style.display = "block";
      selectSubCat.style.display = "none";
      var labelNombre = document.getElementById("labelNombre");
      labelNombre.innerText = "Personal al que se asignará";
   }
   
 });
 
  function updateSelect(option){
    if (option == 1){
      var catSelect = this;
      var catid = this.value;
      var subcatSelect = document.getElementById("subcatsSelect");
      subcatSelect.options.length = 0; 
      for(var i = 0; i < foliosTaller.length; i++){
        subcatSelect.options[i] = new Option(foliosTaller[i].nombre+" "+"("+"Marca:"+" "+foliosTaller[i].marca+" , "+"modelo:"+" "+
        foliosTaller[i].modelo+" )",foliosTaller[i].id);
      }
    }else if (option == 2){
      var catSelect = this;
      var catid = this.value;
      var subcatSelect = document.getElementById("subcatsSelect");
      subcatSelect.options.length = 0; 
      for(var i = 0; i < rutas.length; i++){
        subcatSelect.options[i] = new Option("Ruta #"+rutas[i].id+" "+rutas[i].responsable,rutas[i].id);
      }  
      }else if (option == 3){
      var catSelect = this;
      var catid = this.value;
      var subcatSelect = document.getElementById("subcatsSelect");
      subcatSelect.options.length = 0; 
      for(var i = 0; i < lugaresMantenimientos.length; i++){
        subcatSelect.options[i] = new Option(lugaresMantenimientos[i].nombre+" , "+lugaresMantenimientos[i].municipio+" ("+lugaresMantenimientos[i].descripcion+")",lugaresMantenimientos[i].id);
      }
    }else if (option == 4){
      var catSelect = this;
      var catid = this.value;
      var subcatSelect = document.getElementById("subcatsSelect");
      subcatSelect.options.length = 0; 
      for(var i = 0; i < lugaresMantenimientosOrdenes.length; i++){
        subcatSelect.options[i] = new Option(lugaresMantenimientosOrdenes[i].nombre+" , "+lugaresMantenimientosOrdenes[i].municipio+" ("+lugaresMantenimientosOrdenes[i].descripcion+")",lugaresMantenimientosOrdenes[i].id);
      }
    }
    
  } 
  

</script>
</head>
<main>
<body onload="buscar_pedidos();buscar_pedidos2();loadCategories()">
  <div class="container">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Nuevo Pedido</h3>
      <h5 class="hide-on-large-only">Nuevo Pedido</h5>
    </div>
    <div class="row">
      <div class="input-field col s12 m6 l6">
        <select id="selectTipo" class="browser-default">
          <option value="" disabled selected>Selecciona el tipo de pedido</option>
          <option value="1">Taller</option>
          <option value="2">Redes-Rutas diarias</option>
          <option value="3">Redes-Mantenimientos</option>
          <option value="4">Ordenes de servicio en general</option>
          <option value="5">Material o equipo para personal</option>
        </select>
        <label class="active">Tipo de pedido</label>
      </div>
      <div id="divSubCat" class="input-field col s12 m6 l6">
        <select id='subcatsSelect' class="browser-default">
          <option value="" disabled selected>Selecciona primero un tipo de pedido</option>
        </select>
        <label id="labelNombreSubCatSelect" class="active">Seleccione el servicio</label>
    </div>
        <div hidden id="divNombre" class="input-field col s12 m6 l6">
          <i class="material-icons prefix">people</i>
          <input type="text" id="nombre">
          <label for="nombre" id="labelNombre">Nombre del Cliente:</label>
        </div>
    </div>  
    <div class="row">
      
      <div class="col s12 m6 l6">
        <label for="fecha_req">Fecha Requerido (Puede ir vacio):</label>
        <input id="fecha_req" type="date" >
      </div>
      <div class="input-field col s12 m6 l6">
        <a onclick="insert_pedidos();" class="waves-effect waves-light btn pink left right">REGISTRAR PEDIDO<i class="material-icons center right">send</i></a>
      </div>
    </div>    
    
    <div id="resultado_pedido">
      <div class="row"> <br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Pedidos</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Pedidos</h5>          
      </div>
      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">No Autorizados</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">No Autorizados</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda2" name="busqueda2" type="text" class="validate" onkeyup="buscar_pedidos2();">
              <label for="busqueda2">Buscar No Autorizados (ej: #Folio, Nombre de Cliente, IdOrden)</label>
            </div>
          </div>
        </form>
      </div>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Estatus</th>
              <th>Folio</th>
              <th>Nombre (Cliente / Personal)</th>
              <th>Id operación</th>
              <th>Fecha Y Hora Creacion</th>
              <th>Cerrado</th>
              <th>Requerido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="pedidosNo">
            
          </tbody>
      </table><br><br>

      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">Autorizados</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">Autorizados</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_pedidos();">
              <label for="busqueda">Buscar Autorizados (ej: #Folio, Nombre de Cliente, IdOrden)</label>
            </div>
          </div>
        </form>
      </div>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Estatus</th>
              <th>Folio</th>
              <th>Nombre (Cliente / Personal)</th>
              <th>Id operación</th>
              <th>Fecha Y Hora Creacion</th>
              <th>Cerrado</th>
              <th>Autorizado</th>
              <th>Requerido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="PedidosALL">
          </tbody>
      </table><br><br>

      <h4 class="hide-on-med-and-down col s12 m6 l6">Surtidos</h4>
      <h6 class="hide-on-large-only col s12 m6 l6">Surtidos</h6>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Nombre (Cliente / Personal)</th>
              <th>Id operación</th>              
              <th>Fecha Y Hora Creacion</th>
              <th>Cerrado</th>
              <th>Autorizado</th>
              <th>Surtido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Entregar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $consulta = mysqli_query($conn,"SELECT * FROM pedidos WHERE estatus = 'Completo' ORDER BY folio DESC");

          if (mysqli_num_rows($consulta) <= 0) {
            echo '<h5 class = "center">No se encontraron pedidos (Completados)</h5>';
          }else{
            //La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
            while($pedido = mysqli_fetch_array($consulta)){
              $usuario = $pedido['usuario'];
              $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $usuario"));
              $folio = $pedido['folio'];
              $color = ($pedido['cerrado'] == 0)? 'red': 'green';
              if ($pedido['id_orden'] == 0){
                $numeroOrden = "N/A";
          
              }else{
                $numeroOrden = $pedido['id_orden'];
              }
          ?>
            <tr>
              <td><?php echo  $folio; ?></td>
              <td><?php echo  $pedido['nombre']; ?></td>
              <td><?php echo  $numeroOrden; ?></td>
              <td><?php echo  $pedido['fecha']; ?><?php echo  $pedido['hora']; ?></td>
              <td><?php echo  $pedido['fecha_cerrado']; ?></td>
              <td><?php echo  $pedido['fecha_autorizado']; ?></td>
              <td><?php echo  $pedido['fecha_completo']; ?></td>
              <td><?php echo  $datos['firstname']; ?></td>
              <td><a href = "../views/detalles_pedido.php?folio=<?php echo  $folio; ?>" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
              <td><a onclick="entregar(<?php echo  $folio; ?>)" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">exit_to_app</i></a></td>
            </tr>
          <?php 
            } //FIN WHILE 
          } // FIN ELSE
          ?>
          </tbody>
      </table><br><br>
    </div>
  </div>
</body>
</main>
</html>