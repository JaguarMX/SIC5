<!DOCTYPE html>
<html>
<head>
	<title>SIC | Credito de Cliente</title>
<?php
include('fredyNav.php');
?>
<script>
  function estado_X_fecha(id_cliente){
    var textoDe = $("input#fecha_de_estado").val();
    var textoA = $("input#fecha_a_estado").val();
    if (textoDe == "" || textoA == ""){
      M.toast({html:"Ingrese un rango de fechas.", classes: "rounded"});
    }else {
      var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/estado_x_fecha.php?valorID="+id_cliente+"&valorDe="+textoDe+"&valorA="+textoA;
      a.click();
    }
  };
  function insert_abono(){    
    var textoCantidad = $("input#cantidad").val();
    var textoDescripcion = $("input#descripcion").val();
    var textoIdCliente = $("input#id_cliente").val();

    if(document.getElementById('banco').checked==true){
      textoTipo_Campio = "Banco";
    }else if(document.getElementById('SAN').checked==true){
      textoTipo_Campio = "SAN";
    }else{
      textoTipo_Campio = "Efectivo";
    }

    if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html:"El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
    }else{
      $.post("../php/insert_abono.php", { 
          valorTipo_Campio: textoTipo_Campio,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente,
      }, function(mensaje) {
          $("#mostrar_abonos").html(mensaje);   
      });
    }
  }
</script>
</head>
<?php
if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a clientes.", classes: "rounded"})
    setTimeout("location.href='clientes.php'", 8000);
  </script>
  <?php
}else{
$no_cliente = $_POST['no_cliente'];
$user_id = $_SESSION['user_id'];
?>
<body>
	<div class="container" id="mostrar_abonos">
  <?php 
  $sql = mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$no_cliente");
  if (mysqli_num_rows($sql)<=0) {
    $sql = mysqli_query($conn,"SELECT * FROM especiales WHERE id_cliente=$no_cliente");
  } 
  $datos = mysqli_fetch_array($sql);
  $id_comunidad = $datos['lugar'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));

  // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
  $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $no_cliente"));
  $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $no_cliente AND tipo = 'Abono'"));
  //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
  if ($deuda['suma'] == "") {
    $deuda['suma'] = 0;
  }elseif ($abono['suma'] == "") {
    $abono['suma'] = 0;
  }
  //SE RESTAN DEUDAS DE ABONOS Y SI EL SALDO ES NEGATIVO SE CAMBIA DE COLOR
$Saldo = $abono['suma']-$deuda['suma'];
$color = 'green';
if ($Saldo < 0) {
  $color = 'red darken-2';
}
  ?>
		<div class="row">
			<h2 class="hide-on-med-and-down">Credito de Cliente:</h2>
 			<h4 class="hide-on-large-only">Credito de Cliente:</h4>
		</div>
		<div class="row">
			<ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Cliente: </b><?php echo $no_cliente; ?></span>
              <p><b>Nombre(s): </b><?php echo $datos['nombre']; ?><br>
                 <b>Telefono: </b><?php echo $datos['telefono']; ?><br>
                 <b>Comunidad: </b><?php echo $comunidad['nombre']; ?><br>
                 <b>Dirección: </b><?php echo $datos['direccion']; ?><br>
                 <b>Referencia: </b><?php echo $datos['referencia']; ?><br>
                 <b>IP: </b><a href="http://<?php echo $datos['ip']; ?>"><?php echo $datos['ip']; ?></a>
                 <br><br><hr>
                 <b>SALDO: </b> <span class="new badge <?php echo $color ?>" id="mostrar_deuda" data-badge-caption="">$<?php echo $Saldo; ?><br>
              </p>
            </li>
        </ul>		
		</div>
    <div class="row">
      <h3 class="hide-on-med-and-down">Abonar:</h3>
      <h5 class="hide-on-large-only">Abonar:</h5>
    </div>
    <div class="row">
      <form class="col s12">        
        <div class="row col s12 m5 l3">
          <div class="input-field">
            <i class="material-icons prefix">payment</i>
            <input id="cantidad" type="number" class="validate" data-length="6" value="0" required>
            <label for="cantidad">Cantidad:</label>
          </div>
        </div>
        <div class="row col s12 m7 l6">
          <div class="input-field">
            <i class="material-icons prefix">description</i>
            <input id="descripcion" type="text" class="validate" data-length="100" required>
            <label for="descripcion">Descripción: </label>
          </div>
        </div>
        <?php 
        $Ser = (in_array($user_id, array(10, 101, 49, 105, 107, 84)))? '': 'disabled="disabled"';
        $Ser2 = (in_array($user_id, array(10, 101, 49, 107, 84)))? '': 'disabled="disabled"';   
        ?>
        <div class="col s6 m3 l1">
          <p>
            <br>
            <input type="checkbox" id="banco" <?php echo $Ser;?>/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <div class="col s6 m3 l1">
          <p>
            <br>
            <input type="checkbox" id="SAN" <?php echo $Ser2;?>/>
            <label for="SAN">SAN</label>
          </p>
        </div>
        <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
      </form>
      <a onclick="insert_abono();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Abono</a>
      <br>
    </div>
    <div class="row">
      <div class="col s12 m6 l6">
        <h4>Deudas: </h4>
        <table>
          <thead>
            <tr>
              <th>Id Deuda</th>
              <th>Cantidad</th>
              <th>Fecha</th>
              <th>Descripcion</th>
              <th>Usuario</th>
              <th>Liquid.</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $deudas = mysqli_query($conn, "SELECT * FROM deudas WHERE id_cliente = $no_cliente");
              $aux = mysqli_num_rows($deudas);
              if ($aux > 0) {
                while ($resultados = mysqli_fetch_array($deudas)) {
                  $id_user = $resultados['usuario'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
            ?>
            <tr>
              <td><b><?php echo $resultados['id_deuda'];?></b></td>         
              <td>$<?php echo $resultados['cantidad'];?></td>
              <td><?php echo $resultados['fecha_deuda'];?></td>
              <td><?php echo $resultados['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo ($resultados['liquidada'] == 1)?'<span class="new badge green" data-badge-caption=""></span>':'<span class="new badge red" data-badge-caption=""></span>';?></td>
            </tr>
            <?php 
             }//fin while
            }else{
              echo "<center><b><h3>Este cliente aún no ha registrado Deudas</h3></b></center>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="col s12 m6 l6">
        <h4>Abonos: </h4>
        <table >
          <thead>
            <tr>
              <th>Id Abono</th>
              <th>Cantidad</th>
              <th>Fecha</th>
              <th>Descripcion</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody>
           <?php
              $abonos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $no_cliente AND tipo = 'Abono'");
              $aux = mysqli_num_rows($abonos);
              if ($aux > 0) {
                while ($resultados = mysqli_fetch_array($abonos)) {
                  $id_user = $resultados['id_user'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
            ?>
            <tr>
              <td><b><?php echo $resultados['id_pago'];?></b></td>         
              <td>$<?php echo $resultados['cantidad'];?></td>
              <td><?php echo $resultados['fecha'];?></td>
              <td><?php echo $resultados['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
            </tr>
            <?php 
             }//fin while
            }else{
              echo "<center><b><h3>Este cliente aún no ha registrado Abonos</h3></b></center>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <h3 class="hide-on-med-and-down">ESTADO DE CUENTA:</h3>
      <h5 class="hide-on-large-only">ESTADO DE CUENTA:</h5>
    </div>
    <div class="row">
        <div class="col s12 l4 m4">
            <label for="fecha_de_estado">De:</label>
            <input id="fecha_de_estado" type="date" >    
        </div>
        <div class="col s12 l4 m4">
            <label for="fecha_a_estado">A:</label>
            <input id="fecha_a_estado" type="date" >
        </div>  
        <div><br>
          <button class="btn waves-light waves-effect right pink" onclick="estado_X_fecha(<?php echo $no_cliente; ?>);">ESTADO CUENTA<i class="material-icons prefix right">print</i></button>
        </div>
    </div><br><br><br><br>
    <div id="mostrar_resultado"></div>
	</div>
</body>
<?php 
}
?>
</html>