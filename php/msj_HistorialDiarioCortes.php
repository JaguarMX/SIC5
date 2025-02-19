<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS TODAS LAS LIBRERIAS  DE MAILER PARA PODER ENVIAR CORREOS DE ESTE ARCHIVO
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/vendor/autoload.php';


$Mensaje = '';//SE CREA LA VARIABLE MSH¡J VACIA PARA AGREGAR EL HISTORIAL DE CORTES MAS EL RESUMEN DE LA CAJA CHICA

#------------------------------------------------------------------------------------
#HAY QUE AGREGAR EL HISTORIAL DE CORTES AL MENSAJE CON LOS CORTES DIARIOS
#------------------------------------------------------------------------------------
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
#SELECCIONAMOS TODOS LOS CORTES REALIZADOS CON LA FECHA DE HOY
$sql_cortes = mysqli_query($conn, "SELECT * FROM cortes WHERE fecha = '$Fecha_hoy'");
#VERIFICAMOS SI SE ENCONTRARON CORTES
if(mysqli_num_rows($sql_cortes) > 0){
    #echo "ENTRE <br>";
    #INICIAMOS A CREAR EL MENSAJE A ENVIAR CABECERA
    $Mensaje .= "<b>HISTORIAL DIARIO DE CORTES:<br>
                  Fecha: ".$Fecha_hoy.".</b><br><br>";
    $Total_Credito = 0;
    $Total_Banco = 0;
    $Total_Efectivo = 0;
    #SI SE ENCONTRARON CORTES SE RECORE UNO POR UNO...
    while($Corte = mysqli_fetch_array($sql_cortes)){      
      $id_user = $Corte['usuario'];
      $corte = $Corte['id_corte'];
      #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
      $cobrador = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
      #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
      $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
      if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $Deducir = $Deducible['cantidad'];
      }else{
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
      $Efectivo = $Corte['cantidad']-$Deducir-$Adeudo;
      $Banco = $Corte['banco'];
      $Credito = $Corte['credito'];

      #LE SUMAMOS LA INFORMACION DEL CORTE EN TURNO AL MENSAJE
      $Mensaje .= "#-------------------------------------------------------------------------<br>
                    >>> Cobrador: <b>".$cobrador['firstname']."(".$cobrador['user_name'].")</b>, Folio: <b>".$corte."</b><br>
                    >>><b>Adeudo = $".$Adeudo." _ Deducibles = $".$Deducir.".<br>
                    ENTREGO:<br>
                    * * SubTotal_Credito = $".$Credito."<br>
                    * * SubTotal_Banco = $".$Banco."<br>
                    * * SubTotal_Efecivo = $".$Efectivo."<br></b>
                   #-------------------------------------------------------------------------<br>";
      #SUMAMOS LOS SUBTOTALES AL TOTAL
      $Total_Credito += $Credito;
      $Total_Banco += $Banco;
      $Total_Efectivo += $Efectivo;
    }// FIN DEL WHILE
    #CREAMOS EL PIE DEL MENSAJE MOSTRANDO LOS TOTALES
    $Mensaje .= "<br> ///////////////////////////////////////////////////////////<br>
                 <b> | > TOTAL CREDITO = $".$Total_Credito."<br>
                  | > TOTAL BANCO = $".$Total_Banco."<br>
                  | > TOTAL EFECTIVO = $".$Total_Efectivo."<br></b>
                 ///////////////////////////////////////////////////////////<br>";
    #echo $Mensaje;
}// FIN DEL IF CORTES

// SACAMOS EL RESUMEN DE LA CAJA CHICA DIARIO
#-----------------------------------------------------------------
# AGREGAMOS EL RESUMEN DE LA CAJA CHICA DE CORTES AL MENSAJE 
#-----------------------------------------------------------------
#INICIAMOS A CREAR LA CABECERA DEL RESUMEN CAJA
$Mensaje .= "<br><b>RESUMEN DIARIO DE CAJA CHICA:<br>
              Fecha: ".$Fecha_hoy.".</b><br><br>";
#SELECCIONAMOS TODOS LOS CORRTES REALIZADOS CON LA FECHA DE HOY
$sql_caja = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE fecha = '$Fecha_hoy'");
#VERIFICAMOS SI SE ENCONTRARON REGISTROS
if(mysqli_num_rows($sql_caja) > 0){
  $Total_Ingresos = 0;
  $Total_Egresos = 0;
  $ingresos = '';
  $egresos = '';
  #SI SE ENCONTRARON REGISTROS EN CAJA SE RECORE UNO POR UNO...
  while($registro = mysqli_fetch_array($sql_caja)){  
    $Tipo = $registro['tipo'];
    $id_user = $registro['usuario'];
    $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
    if ($Tipo == 'Ingreso') {
      $ingresos .= " ** Usuario: ".$user['user_name'].", Cantidad: $".$registro['cantidad']."<br>";
      $Total_Ingresos += $registro['cantidad'];
    }else{
      $egresos .= " ** Usuario: ".$user['user_name'].", Cantidad: $".$registro['cantidad']."<br>
                     .... Descripcion:".$registro['descripcion']."<br>";
      $Total_Egresos += $registro['cantidad'];
    }
  }// FIN WHLE
  $Total_Caja = $Total_Ingresos-$Total_Egresos;
  $Mensaje .= "#-------------------------------------------------------------------------<br>
               <b> > INGRESOS: <br>".$ingresos."</b>
               #-------------------------------------------------------------------------<br>
               <b> > EGRESOSO: <br>".$egresos."</b>
               #-------------------------------------------------------------------------<br>
               <b> > --- SubTotal Diario = + $".$Total_Caja."<br></b>";
}else{// FIN IF CAJA
  $Mensaje .= "-- Sin Movimientos--<br><br>";
}
// SACAMOS LA SUMA DE TODOS LOS EGRESOSO E INGRESOSO DE LA CAJA CHICA
$Suma_Ingresos = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Ingreso'"));
$Suma_Egresoso = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Egreso'"));
//SE HACE EL CALCULO DEL TOTAL DE LA CAJA CHICA
$Total = $Suma_Ingresos['suma']-$Suma_Egresoso['suma'];
$Mensaje.= "<br>/////////////////////////////////////////////////////////<br>
            <b> | > TOTAL CAJA CHICA = $".$Total."<br></b>
            /////////////////////////////////////////////////////////<br>";

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
        $correo->setFrom('no-replay@gmail.com', 'HISTORIAL CORTES DIARIO HOSTING');
        #DEFINIMOS A QUE CORREOS SERAN LOS DESTINATARIOS
        $correo->addAddress('ulises.dominguez@sicsom.com', 'Ulises');
        $correo->addAddress('gabriel.valles@sicsom.com', 'Gabriel');
        $correo->isHTML(true);
        $correo->Subject = 'Historial Fecha: '.$Fecha_hoy;// SE CREA EL ASUNTO DEL CORREO
        $correo->Body = $Mensaje;
        $correo->send();
        
        echo "CORREO ENVIADO CON EXITO !!!";
        echo $Mensaje;
    }catch(Exception $e){
        echo 'ERROR: '.$correo->ErrorInfo;
    }
}
