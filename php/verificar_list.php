<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$Servidor = $conn->real_escape_string($_POST['valorServidor']);
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

//////// INFORMACION DEL SERVIDOR //////////
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;

#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
if ($API->connect($ServerList, $Username, $Pass, $Port)){
    $Inicia = $conn->real_escape_string($_POST['valorInicia']);
    $iniciar = 115*($Inicia);
    #SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes del servidor elegido = $Servidor
    $Tmp = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 0 LIMIT $iniciar, 115");
    #verificamos que alla clientes
    if (mysqli_num_rows($Tmp)>0) {
        #SI HAY MAS DE 0 CLIENTES LOS RECORREMOS UNO POR UNO CON UN WHILE
        while ($CLIENTE_S = mysqli_fetch_array($Tmp)) {
            $IP_S = trim($CLIENTE_S['ip']);// ip del cliente en turno
            $Id = $CLIENTE_S['id'];// id del cliente en turno
            #RECORREMOS CON UN CICLO FOR HASTA QUE ENCUENTRE LA IP EN LA adress-list SI LO ENCUENTRA ROMPER CON BREAK
            for ($x = 1; $x < 9; $x++) {
                #BUSCAMOS LA IP EN 'MOROSOS'
                $API->write("/ip/firewall/address-list/getall",false);
                $API->write('?address='.$IP_S,false);
                $API->write('?list=MOROSOS',true);       
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ); // busco si ya existe
                if (count($ARRAY)>0) {
                    if ($ARRAY[0]['address'] == $IP_S AND $ARRAY[0]['list'] == 'MOROSOS') {
                        #SI ENCUENTRA LA IP MODIFICAMOS EL ESTAUS DE CLIENTE cortado = 1 y en cuantas veces = $x
                        mysqli_query($conn, "UPDATE tmp_cortes SET cortado = 1, veces = '$x' WHERE id = '$Id' AND ip = '$IP_S'");
                        #SI LO ENCUENTRA ROPEMOS EL CICLO CON BREAK ANTES DE HACER LAS 8 ITERACIONES
                        break;
                    }
                }//FIN IF (ARRAY)
            }//FIN CICLO FOR (BUSCAR)
        }// FIN WHILE
    }//FIN IF (SI HAY CLIENTES)
    $API->disconnect();
}//FIN IF (SI CONECTA)
#SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes CON ESTATUS cortado = 1
$Tmp_list = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 1");
#CONTAMOS CUANTOS CLIENTES SON
$EnList = mysqli_num_rows($Tmp_list);
#SELECCIONAMOS TODOS LOS CLIENTES QUE SE AGREGARON A LA TABLA tmp_cortes CON ESTATUS cortado = 0
$Tmp_list_no = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 0");
#CONTAMOS CUANTOS CLIENTES SON
$NoList = mysqli_num_rows($Tmp_list_no);
?>
<div><br><br><br><hr>
    <h5>Verificación No. <?php echo $Inicia+1; ?></h5>
    <h3>En adress-list 'MOROSOS' (<?php echo $serv['nombre']; ?>): </h3>
    <h3 class="indigo-text center">TOTAL =  <?php echo $EnList; ?> cliente(s)</h3>

    <h3>Clientes por cortar (agregar adress-list 'MOROSOS'): </h3>
    <h3 class="indigo-text center">TOTAL =  <?php echo $NoList; ?> cliente(s)</h3>

    <button class="btn waves-light waves-effect right pink" onclick="iniciarCorte(<?php echo $Servidor; ?>);"><i class="material-icons prefix right">signal_wifi_off</i>Iniciar</button><br>
</div>