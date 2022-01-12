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

#SELECCIONAMOS LOS CLIENTES DE LA TABLA tmp_cortes del servidor elegido = $Servidor Y NO SE HAN CORTADO cortado = 0
$Tmp = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 0");
#verificamos que alla clientes
if (mysqli_num_rows($Tmp)>0) {
    #CONEXION A MICROTICK DEL SERVIDOR EN TURNO
    if ($API->connect($ServerList, $Username, $Pass, $Port)){
        $id_user = $_SESSION['user_id'];
        $usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = $id_user"));         
        #SI HAY MAS DE 0 CLIENTES LOS RECORREMOS UNO POR UNO CON UN WHILE
        while ($CLIENTE_S = mysqli_fetch_array($Tmp)) {
            $IP_S = trim($CLIENTE_S['ip']);// ip del cliente en turno
            $Id = $CLIENTE_S['id'];// id del cliente en turno
            $comment = "CORTADO POR MOROSO, cortado por: ".$usuario['firstname']. ' Cliente: '.$Id;// Comentario
            #-------------------------------------------------------------------------------------------------
            #AQUI PONDERMOS EL MISMO PROCESO QUE EL DE APAGAR SOLO SE REPETIRA CADA PROCESO 5 VECES Y SE ROMPE CON BRAKE
            #-------------------------------------------------------------------------------------------------
            #RECORREMOS CON UN CICLO HASTA QUE AGREGE LA IP EN LA adress-list SI LA AGREGA ROMPER CON BREAK
            for ($i = 1; $i < 6; $i++) {
                $Encontro = false;
                #AGREGAMOS LA IP A LA adress-list 'MOROSOS'
                $API->write("/ip/firewall/address-list/add",false);
                $API->write('=address='.$IP_S,false);   // IP
                $API->write('=list=MOROSOS',false);       // lista
                $API->write('=comment='.$comment,true);  // comentario
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ);
                #RECORREMOS CON UN CICLO HASTA QUE ENCUENTRE LA IP EN LA adress-list SI LA ENCUENTRA ROMPER CON BREAK
                for ($j = 1; $j < 5; $j++) {
                    #BUSCAMOS LA IP EN 'MOROSOS'
                    $API->write("/ip/firewall/address-list/getall",false);
                    $API->write('?address='.$IP_S,false);
                    $API->write('?list=MOROSOS',true);       
                    $READ = $API->read(false);
                    $ARRAY = $API->parse_response($READ); // busco si ya existe
                    if (count($ARRAY)>0) {
                        if ($ARRAY[0]['address'] == $IP_S AND $ARRAY[0]['list'] == 'MOROSOS') {
                            #DECIMOS QUE YA LA ENCONTRO
                            $Encontro = true;
                            #SI LO ENCUENTRA ROPEMOS EL CICLO CON BREAK ANTES DE HACER LAS 4 ITERACIONES
                            break;
                        }
                    }
                }//FIN CICLO FOR (BUSCAR)
                if ($Encontro) {
                    #SI ENCUENTRA LA IP MODIFICAMOS EL ESTAUS DE CLIENTE cortado = 2 y en cuantas veces = $i
                    mysqli_query($conn, "UPDATE tmp_cortes SET cortado = 2, veces = '$i' WHERE id = '$Id' AND ip = '$IP_S'");
                    #SI LA ENCONTRO CON EL ANERIOR CICLO ROMPEMOS EL CICLO DE AGREGAR CON BREAK
                    break;
                }//FIN IF (Encontro)
            }//FIN CICLO FOR (AGERGAR)
        }// FIN WHILE (RECORRER CLIENTES)
        $API->disconnect();
    }else{//FIN IF (SI CONECTA)
        echo 'ERROR DE CONEXION CON MIKROTIK';
    } //FIN ELSE API
}else{//FIN IF (SI HAY CLIENTES)
    echo 'NO SE ENCONTRARON CLINTES';
}//FIN ELSE CLIENTES

$Tmp = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor'");
$Total = mysqli_num_rows($Tmp);
$Tmp_list = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 1");
$EnList = mysqli_num_rows($Tmp_list);
$Tmp_add = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 2");
$Agregados = mysqli_num_rows($Tmp_add);
$Tmp_no = mysqli_query($conn, "SELECT * FROM tmp_cortes WHERE servidor = '$Servidor' AND cortado = 0");
$SinCorte = mysqli_num_rows($Tmp_no);
$Son = $EnList+$Agregados;
?>
<div><br>
    <h3>Historial de Cortes (<?php echo $serv['nombre']; ?>):</h3>
    <h4 class="indigo-text center">En Lista =  <?php echo $EnList; ?> cliente(s)</h4>
    <h4 class="green-text center">Agregados =  <?php echo $Agregados; ?> cliente(s)</h4>
    <h4 class="red-text center">No Cortados =  <?php echo $SinCorte; ?> cliente(s)</h4><hr>
    <h3 class="center">TOTAL =  <?php echo $Son; ?> / <?php echo $Total; ?> Cortados</h3>
</div>
