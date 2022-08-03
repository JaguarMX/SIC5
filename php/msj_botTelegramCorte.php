<?php
#Falla
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS TODAS LAS LIBRERIAS  DE MAILER PARA PODER ENVIAR CORREOS DE ESTE ARCHIVO
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/vendor/autoload.php';

#INCLUIMOS EL ARCHIVO CON LA INFORMACION DE LOS CHATS BOT
#include('../php/infoBots.php');

#FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT CORTES
#function sendMessage($id, $msj, $website){
    #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
    #$url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
   # file_get_contents($url);
#}          
#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERROR CON LOS SERVIDORES DE MIKROTIK
#-------------------------------------------------------------------
#BUSCAMOS ERRORES CON ESTATUS  Mikrotik y msj_error en 0
$sql_corte = mysqli_query($conn, "SELECT * FROM cortes WHERE msj = 0");
if(mysqli_num_rows($sql_corte) > 0){
  echo "ENTRE <br>";
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($Corte = mysqli_fetch_array($sql_corte)){
      #DEFINIMOS UNA ZONA HORARIA
      date_default_timezone_set('America/Mexico_City');
      $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
      $id_user = $Corte['usuario'];
      $corte = $Corte['id_corte'];
      #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
      $cobrador = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
      #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
      $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
      if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $descripcion_v = $Deducible['descripcion'].".<br>";
        $Deducir = $Deducible['cantidad'];
      }else{
        $descripcion_v = '';
        $Deducir = 0;
      }
      $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $corte");
      if (mysqli_num_rows($sql_deuda) > 0) {
        $Deuda = mysqli_fetch_array($sql_deuda);
        $Adeudo = $Deuda['cantidad'];
      }else{
        $Adeudo = 0;
      }
      #GUARDAMOS LOS TOTALES DE CADA TIPO DE PAGO EN UNA RESPETIVA VARIABLE         
      $cantidad = $Corte['cantidad']-$Deducir-$Adeudo;
      $banco = $Corte['banco'];
      $credito = $Corte['credito'];
      $Mensaje = '';
      echo $cantidad.' - '.$banco.' - '.$credito;
      #VERIFICAMOS SI EN EL CORTE ECHO NO ESTEN TODAS LAS CANTIDADES VACIAS
      if ($cantidad >= 0 OR $banco > 0 OR $credito > 0) {
          #CREAMOS EL MENSAJE CON LA INFORMACION QUE HAY QUE ENVIAR POR TELEGRAM
          $Mensaje = "Corte del Dia: ".$Corte['fecha'].", Hora: ".$Corte['hora'].". <br>Con folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")'.</b> <br>  <b> -Adeudo = $".$Adeudo.". <br>  -Deducibles = $".$Deducir.".<br>   -</b>".$descripcion_v." <br><b>ENTREGO:<br>  *Banco = $".$banco.". <br>  *Efectivo = $".$cantidad.". <br>  *Credito = $".$credito.". <br> <br> Relizado por: ".$Corte['realizo'];
          $Aviso = "Corte del Dia: ".$Corte['fecha'].", Hora: ".$Corte['hora'].". <br>Con folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")'.</b> ";
      }
      #if( !sendMessage($id_Chat_Fredy, $Aviso, $website_Aviso)  AND !sendMessage($id_Chat_Rocio, $Aviso, $website_Aviso) AND !sendMessage($id_Chat_Fredy, $Mensaje, $website_Corte) AND !sendMessage($id_Chat_Gabriel, $Mensaje, $website_Corte)){
        #Si se ENVIA el mensaje modificar msj a 1 para comprobar que se envio el msj
   			#mysqli_query($conn, "UPDATE cortes SET msj = 1 WHERE id_corte = '$corte'");
      #}
      #--------------------------------------------------------------------
      #CORREO DEL CORTE....
      #--------------------------------------------------------------------
      #CORTE

      #-----------------------------------------------------------------
      # VERIFICAMOS QUE EL $Mensaje NO ESTE VACIO Y ENVIAMOS EL CORREO 
      #-----------------------------------------------------------------
      if ($Mensaje != '') {
          $correo = new PHPMailer(true);
          try{
              #$correo->SMTPDebug = SMTP::DEBUG_SERVER;
              $correo->isSMTP();
              $correo->Host = 'sicsom.com';
              $correo->SMTPAuth = true;
              $correo->Username = 'cortes@sicsom.com';
              $correo->Password = '3.NiOYNE(Txj';
              $correo->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
              $correo->Port = 465;
              #COLOCAMOS UN TITULO AL CORREO  COMO REMITENTE
              $correo->setFrom('no-replay@gmail.com', 'Cortes SIC');
              #DEFINIMOS A QUE CORREOS SERAN LOS DESTINATARIOS
              $correo->addAddress('alfredo.martinez@sicsom.com', 'Alfredo');
              $correo->addAddress('gabriel.valles@sicsom.com', 'Gabriel');
              $correo->isHTML(true);
              $correo->Subject = 'Corte No.'.$corte;// SE CREA EL ASUNTO DEL CORREO
              $correo->Body = $Mensaje;
              $correo->send();
              
              echo "CORREO ENVIADO CON EXITO !!!";
              echo $Mensaje;
              mysqli_query($conn, "UPDATE cortes SET msj = 1 WHERE id_corte = '$corte'");
          }catch(Exception $e){
              echo 'ERROR: '.$correo->ErrorInfo;
          }
      }

      #AVISO
      if ($Aviso != '') {
          $correo = new PHPMailer(true);
          try{
              #$correo->SMTPDebug = SMTP::DEBUG_SERVER;
              $correo->isSMTP();
              $correo->Host = 'sicsom.com';
              $correo->SMTPAuth = true;
              $correo->Username = 'cortes@sicsom.com';
              $correo->Password = '3.NiOYNE(Txj';
              $correo->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
              $correo->Port = 465;
              #COLOCAMOS UN TITULO AL CORREO  COMO REMITENTE
              $correo->setFrom('no-replay@gmail.com', 'Aviso Cortes SIC');
              #DEFINIMOS A QUE CORREOS SERAN LOS DESTINATARIOS
              $correo->addAddress('jonatan.madrid@sicsom.com', 'Jonatan');
              $correo->isHTML(true);
              $correo->Subject = 'Aviso de: Corte No.'.$corte;// SE CREA EL ASUNTO DEL CORREO
              $correo->Body = $Aviso;
              $correo->send();
              echo "CORREO ENVIADO CON EXITO !!!";
              echo $Aviso;
          }catch(Exception $e){
              echo 'ERROR: '.$correo->ErrorInfo;
          }
    }
  }
}