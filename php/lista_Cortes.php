<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha = date('Y-m-d');

$Servidor = $conn->real_escape_string($_POST['valorServidor']);
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

#SELECCIONAMOS TODOS LOS CLIENTES QUE TENGA DE FECHA DE CORTE MENOR A HOY QUE PERTENEZCAN AL SERVIDOR SELECCIONADO.
$ARRAYCORTADOS = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.fecha_corte < '$Fecha' AND clientes.instalacion = 1 AND (clientes.servicio = 'Internet' OR clientes.servicio = 'Internet y Telefonia') AND comunidades.servidor = $Servidor");
#CONTAMOS CUANTOS CLIENTES SON
$Morosos = mysqli_num_rows($ARRAYCORTADOS);
#VERIFICAMOS SI EL CONTADOR DE CLEINTES MOROSOS ES MAYOR A 0
if ($Morosos > 0) {
	#ELIMINAMOS TDODOS LOS REGISTROS ANTERIORES
	$sql_delete = "DELETE FROM tmp_cortes";
	#VERIFICAMOS QUE SE HALLAN ELIMINADO
	if(mysqli_query($conn, $sql_delete)){
	    echo '<script>M.toast({html:"******  >>>  LISTA VACIA!!!  <<<   ******", classes: "rounded"})</script>';
	}
	$cont1 = 0; $cont2 = 0;
	#RECORREMOS UNO POR UNO A LOS CLIENTES POR CORTAR
	while ($cliente = mysqli_fetch_array($ARRAYCORTADOS)) {
		$IP = $cliente['ip'];// IP DEL CLIENTE EN TURNO
		$id_cliente = $cliente['id_cliente'];// ID DEL CLIENTE EN TIRNO
		#VERIFICAMOS SI LOS DATOS DEL CLIENTE NO FUERON YA REGISTRADIOS EN LA TABLA tmp_cortes
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE id_cliente = $id_cliente AND ip = '$IP'"))>0){
		    $cont1 ++;
		}else{
			$cont2 ++;
			#SI AUN NO SON REGISTRADOS HACEMOS LA INSERCION
			#SI NO EXISTE CREAMOS EL CORTE.....
            mysqli_query($conn,"INSERT INTO tmp_cortes (ip, id_cliente, servidor) VALUES ('$IP', '$id_cliente', '$Servidor')"); 
		}
	}
	echo '<script>M.toast({html:"***Repetidos: '.$cont1.'***,  ***Agregados: '.$cont2.'***", classes: "rounded"})</script>';
}
#SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes CON ESTATUS cortado = 1
$Tmp_list = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 1");
#CONTAMOS CUANTOS CLIENTES SON
$EnList = mysqli_num_rows($Tmp_list);
#SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes CON ESTATUS cortado = 0
$Tmp_list_no = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 0");
#CONTAMOS CUANTOS CLIENTES SON
$NoList = mysqli_num_rows($Tmp_list_no);

#SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes
$Tmp = mysqli_query($conn, "SELECT * FROM tmp_cortes");
#CONTAMOS CUANTOS CLIENTES SON
$PorCortar = 260;
$Botones = ceil($PorCortar/80);
?>
<div><br>
	<div id="clock" class="right" style="display: none;"></div> 
	<h3>Clientes por cortar (<?php echo $serv['nombre']; ?>):</h3>
	<h3 class="indigo-text center">TOTAL =  <?php echo $PorCortar; ?> cliente(s)</h3>
	<div class="right">
	<?php
	 for ($j = 0; $j < $Botones; $j++) {
	 	$seconds = ($j+1 == $Botones)?($PorCortar-($j*80))*2:80*2;
	?>
		<button class="btn waves-light waves-effect pink" onclick="verificar(<?php echo $Servidor; ?>, <?php echo $j; ?>);tempShow($seconds);"><i class="material-icons prefix right">playlist_add_check</i>Verificar (<?php echo $j+1; ?>)</button>
	<?php
     }
	?>
	</div>	
    <div class="row" id="verificar"><h5>Verificación No. 0 (80 x btn)</h5> </div> <br>
</div>

<div><br><br><hr>  
    <h3>En adress-list 'MOROSOS' (<?php echo $serv['nombre']; ?>): </h3>
    <h3 class="indigo-text center">TOTAL =  <?php echo $EnList; ?> cliente(s)</h3>

    <h3>Clientes por cortar (agregar adress-list 'MOROSOS'): </h3>
    <h3 class="indigo-text center">TOTAL =  <?php echo $NoList; ?> cliente(s)</h3>

    <button class="btn waves-light waves-effect right pink" onclick="iniciarCorte(<?php echo $Servidor; ?>);"><i class="material-icons prefix right">signal_wifi_off</i>Iniciar</button><br>
</div>