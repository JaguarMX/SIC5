<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$Servidor = 11;
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
        #LISTA DE IPS O CLIENTES ARRAY
        $CLIENTE_S = array('172.16.169.107','172.16.44.85','172.16.45.95');
        #SI HAY MAS DE 0 CLIENTES LOS RECORREMOS UNO POR UNO CON UN WHILE
        foreach ($CLIENTE_S as &$valor) {
            $MSJ = '';
            $IP_S = trim($valor);// ip del cliente en turno
            echo 'IP TURNO: '.$IP_S.'<br>';
            #RECORREMOS CON UN CICLO FOR HASTA QUE ENCUENTRE LA IP EN LA adress-list SI LO ENCUENTRA ROMPER CON BREAK
            for ($x = 1; $x < 10; $x++) {
                #BUSCAMOS LA IP EN 'MOROSOS'
                $API->write("/ip/firewall/address-list/getall",false);
                $API->write('?address='.$IP_S,false);
                $API->write('?list=MOROSOS',true);       
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ); // busco si ya existe
                if (count($ARRAY)>0) {
                    #SI ENCUENTRA LA IP MODIFICAMOS EL ESTAUS DE CLIENTE cortado = 1 y en cuantas veces = $x
                    #mysqli_query($conn, "UPDATE tmp_cortes SET cortado = 1, veces = '$x' WHERE id = '$Id' AND ip = '$IP_S'");
                    #SI LO ENCUENTRA ROPEMOS EL CICLO CON BREAK ANTES DE HACER LAS 9 ITERACIONES
                    $MSj = $ARRAY;
                    break;
                }//FIN IF (ARRAY)
            }//FIN CICLO FOR (BUSCAR)
            echo $MSJ;
        }// FIN WHILE
    $API->disconnect();
}else{
    echo 'ERROR DE CONEXION...'
}