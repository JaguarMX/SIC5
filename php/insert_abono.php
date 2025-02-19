<?php 
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND cantidad='$Cantidad' AND fecha='$Fecha_hoy'"))>0){
	echo '<script>M.toast({html:"Ya se encuentra un abono registrado con los mismos valores el día de hoy.", classes: "rounded"})</script>';
}else{ 
	$sql = "INSERT INTO pagos (id_cliente, cantidad, fecha, hora, descripcion , tipo_cambio, id_user, tipo, corte, corteP) VALUES ($IdCliente, '$Cantidad', '$Fecha_hoy', '$Hora', '$Descripcion', '$Tipo_Campio', '$id_user', 'Abono', 0, 0)";
	if(mysqli_query($conn, $sql)){          
    $Deuda_check = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas WHERE id_cliente = $IdCliente AND liquidada=0 limit 1"));
     
    // SACAMOS LA SUMA DE TODAS LAS DEUDAS QUE ESTAN LIQUIDADDAS Y TODOS LOS ABONOS ....
    $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $IdCliente AND liquidada = 1"));
    $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'Abono'"));
    $deuda['suma'] = ($deuda['suma'] == "")? 0 : $deuda['suma'];
    $abono['suma'] = ($abono['suma'] == "")? 0 : $abono['suma'];
    
    $Resta = $abono['suma']-$deuda['suma'];// SACAMO LA DIFERENCIA PARA VER CUANTAS DEUDAS ALCANZA A LIQUIDAR CON ESTA CANTIDAD

    $ENTRA = False;// SI ENTRA = false no ejecuta el while y no cambia nunguna deuda a liquidada = 1
    //VERIFICAMOS QUE LA DEUDA A COMPRAR TENGA ALGUN VALOR MAYOR A 0
    if ($Deuda_check['cantidad'] <=0) {
      $Entra = False;
    }else if ($Deuda_check['cantidad'] <= $Resta) {//AHORA VERIFICAMOS SI LA DEUDA SELECCIONADA ES MENOR O IGUAL A LA CANTIDAD DE RESTA
      // SI ES MENOS O IGUAL QUIERE DECIR QUE LA DEUDA ALCANZA A SER LIQUIDADA Y LE DAMOS ENTRADA = true PARA QUE ENTRE AL WHILE
      $Entra = True;  
    }
    $id_deuda = $Deuda_check['id_deuda'];// SACAMOS EL ID DE LA DEUDA SELECCIONADA
    while ($Entra) {
      //VERIFICAMOS QUE SE CAMBIE EL ESTATUS DE LA DEUDA
      if (mysqli_query($conn, "UPDATE deudas SET liquidada = 1 WHERE id_deuda = $id_deuda")) {
        echo '<script>M.toast({html:"Deuda liquidada.", classes: "rounded"})</script>';
      }  
      // COMO CAMBIAMOS EL ESTATUS DE UNA DEUDA VOLVEMOS A SACAR LA SUMA DE LAS DEUDAS LIQUIDADAS PARA MODIFICAR LA CANTIDAD $Resta
      $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $IdCliente AND liquidada = 1"));
      $deuda['suma'] = ($deuda['suma'] == "")? 0 : $deuda['suma'];

      $Resta = $abono['suma']-$deuda['suma'];
      //SELECCIONAMOS OTRA DEUDA
      $Deuda_check = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas WHERE id_cliente = $IdCliente AND liquidada=0 limit 1"));

      $Entra = False;// SI EL VALOR ES FALSE EL WHILE TERMINA 
      //VERIFICAMOS QUE LA DEUDA A COMPRAR TENGA ALGUN VALOR MAYOR A 0
      if ($Deuda_check['cantidad'] <=0) {
        $Entra = False;
      }else if ($Deuda_check['cantidad'] <= $Resta) {//AHORA VERIFICAMOS SI LA DEUDA SELECCIONADA ES MENOR O IGUAL A LA CANTIDAD DE RESTA
        // SI ES MENOS O IGUAL QUIERE DECIR QUE LA DEUDA ALCANZA A SER LIQUIDADA Y LE DAMOS ENTRADA = true PARA QUE CONTINUE EL WHILE
        $Entra = True;  
      } 
      $id_deuda = $Deuda_check['id_deuda'];// SACAMOS EL ID DE LA OTRA DEUDA SELECCIONADA
    }//FIN WHILE
		echo '<script>M.toast({html:"El abono se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
	  ?>
	  <script>
	    var a = document.createElement("a");
	      a.target = "_blank";
	      a.href = "../php/pago_cliente.php";
	      a.click();
	  </script>
	  <?php   
	}else{
		echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	  }
}
?>
  <?php 
  $no_cliente = $IdCliente;
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

($Saldo < 0)? $color = 'red darken-2':  $color = 'green';

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
        $Ser = (in_array($id_user, array(10, 102, 101, 49, 88, 38, 84, 90, 91)))? '': 'disabled="disabled"';
        $Ser2 = (in_array($id_user, array(10, 102, 101)))? '': 'disabled="disabled"';   
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
        <table id="mostrar_abonos">
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
<?php 
mysqli_close($conn);
?>
