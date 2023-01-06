<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
include('../php/conexion.php');
#INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
include("../fpdf/fpdf.php");
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$id_dispositivo =$_GET['id'];
$fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo"));

#CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
class PDF extends FPDF{
}

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Folio_'.$id_dispositivo.'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color']);
    $pdf->AddPage();// PRIMERA HOJA DEL TICKET PARA LOS CLIENTES
    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','B', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'RFC: VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
  
    /// INFORMACION DEL PAGO
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10);    
    $folio = substr(str_repeat(0, 5).$id_dispositivo, - 6);
    $pdf->MultiCell(70,4,utf8_decode(date_format(new \DateTime($fila['fecha']), "d/m/Y" ).'                FOLIO: '.$folio),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('TICKET SALIDA'),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0); 
    /// INFORMACION DEL CLIENTE
    $id_User = $fila['recibe'];
    $register = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_User'"));   
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Courier','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('CLIENTE: '.$fila['nombre']."\n".'REGISTRO: '.$register['firstname'].' '.$register['lastname']),0,'L',0);
        // INFORMACION DEL DISPOSITIVO TABLA
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 9);    
    $pdf->MultiCell(70,4,utf8_decode('DISPOSITIVO            MODELO           EXTRAS'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->MultiCell(32,3,utf8_decode($fila['tipo'].' '.$fila['marca']),0,'L',0);
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(37);
    $pdf->MultiCell(16,3,utf8_decode($fila['modelo']),0,'R',0);    
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(53);
    $pdf->MultiCell(22,3,utf8_decode($fila['extras']),0,'R',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
        /// FALLA, ESTATUS, OBSERVACION
    $id_User = $fila['recibe'];
    $register = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_User'"));   
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']."\n".'OBSERVACIONES: '.$fila['observaciones']."\n".'ESTATUS:  '.$fila['estatus']),0,'L',0);
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
        // LISTA DE REFACCIONES USADAS
    $SqlRefacciones = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$id_dispositivo'");
    $ref = mysqli_num_rows($SqlRefacciones);
    if ($ref > 0) {
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','B', 9);    
        $pdf->MultiCell(70,4,utf8_decode('      REFACCION                                PRECIO '),0,'L',0);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);     
        $pdf->SetFont('Helvetica','', 9);   
        while($refaccion = mysqli_fetch_array($SqlRefacciones)){
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->MultiCell(36,4,utf8_decode($refaccion['descripcion']),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(42);
            $pdf->MultiCell(32,4,utf8_decode('$'.sprintf('%.2f',$refaccion['cantidad'])),0,'R',0);
        }
         $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    }
    // RESUMEN DE SUMA DE TOTALES Y ANTICIPOS
    $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
    $Total_anti = 0;
    if (mysqli_num_rows($sql)>0) {                            
        while ($anticipo = mysqli_fetch_array($sql)) {
            $Total_anti += $anticipo['cantidad'];
        }
    }
    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->MultiCell(32,4,utf8_decode('MATERIAL(REFA.):'."\n".'MANO OBRA:'."\n".'ANTICIPO(S):'),0,'R',0);    
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(37);
    $pdf->MultiCell(37,4,utf8_decode('$'.sprintf('%.2f',$fila['t_refacciones'])."\n".'$'.sprintf('%.2f',$fila['mano_obra'])."\n".'-$'.sprintf('%.2f',$Total_anti)),0,'R',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('PAGO REALIZADO'),0,'C',0);/// DEGLOSE DEL PAGO QUE SE REALIZO
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);       
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 9);    
    $pdf->MultiCell(70,4,utf8_decode(' DESCRIPCION             T.CAMBIO      TOTAL'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id_dispositivo AND tipo = 'Dispositivo'"));
    $id_pago = $pago['id_pago'];
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(7);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(32,3,utf8_decode($pago['descripcion']),0,'L',0);
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(39);
    $pdf->MultiCell(14,3,utf8_decode($pago['tipo_cambio']),0,'R',0);    
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(54);
    $pdf->MultiCell(21,3,utf8_decode('$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);
    $pdf->SetFont('Helvetica','', 8);
    if ($pago['tipo_cambio'] == 'Banco') {
        $referencia = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM referencias WHERE id_pago = $id_pago")); 
        $ReferenciaB = $referencia['descripcion'];
        $pdf->SetY($pdf->GetY()+1);
        $pdf->SetX(25);
        $pdf->MultiCell(34,3,utf8_decode($ReferenciaB),0,'R',0);
    }
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->MultiCell(30,4,utf8_decode('IVA:'."\n".'SUBTOTAL:'."\n".'TOTAL:'),0,'R',0);    
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(35);
    $pdf->MultiCell(40,4,utf8_decode('$'.sprintf('%.2f',$pago['cantidad']*0.16)."\n".'$'.sprintf('%.2f',$pago['cantidad']-($pago['cantidad']*0.16))."\n".'$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);

    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LA SALIDA
    $id_user = $_SESSION['user_id'];
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));  
    $pdf->SetFont('Helvetica','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(70,6,utf8_decode("\n"."\n".'__________________________________'."\n".'Nombre y Firma (Resposable)'),1,'C',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5); 
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);   
    $pdf->SetFont('Helvetica','B', 9); 
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);     
    $pdf->MultiCell(70,4,utf8_decode('¡GRACIAS POR TU PAGO!'."\n".'TODO LO QUE QUIERES ESTA EN SIC'),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);    
    $pdf->SetFont('Helvetica','B', 8);      
    $pdf->MultiCell(70,4,utf8_decode('ADVERTENCIA:'."\n".'1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS.'."\n".'2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.'."\n".'3.- SIN ESTE TICKET NO SE ACEPTAN RECLAMACIONES'),1,'C',0);

#----------------------------------------------------------------------------------------------------------------------
    $pdf->AddPage();// NUEVA PAGINA TICKET DE SE QUEDA SIC------
    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','B', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'RFC: VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
  
    /// INFORMACION DEL PAGO
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10);    
    $pdf->MultiCell(70,4,utf8_decode(date_format(new \DateTime($fila['fecha']), "d/m/Y" ).'                FOLIO: '.$folio),0,'C',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('TICKET SALIDA'),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0); 
    /// INFORMACION DEL CLIENTE   
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Courier','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('CLIENTE: '.$fila['nombre']."\n".'REGISTRO: '.$register['firstname'].' '.$register['lastname']),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    //INROMACION DEL DISPOSITIVO TABLA
    $pdf->SetFont('Helvetica','B', 9);    
    $pdf->MultiCell(70,4,utf8_decode('DISPOSITIVO            MODELO           EXTRAS'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->MultiCell(32,3,utf8_decode($fila['tipo'].' '.$fila['marca']),0,'L',0);
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(37);
    $pdf->MultiCell(16,3,utf8_decode($fila['modelo']),0,'R',0);    
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(53);
    $pdf->MultiCell(22,3,utf8_decode($fila['extras']),0,'R',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
        /// FALLA ESTATUS OBSERVACION  
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']."\n".'OBSERVACIONES: '.$fila['observaciones']."\n".'ESTATUS:  '.$fila['estatus']),0,'L',0);
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
        // REFACCIONES LISTADO
    $SqlRefacciones = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$id_dispositivo'");
    $ref = mysqli_num_rows($SqlRefacciones);
    if ($ref > 0) {
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','B', 9);    
        $pdf->MultiCell(70,4,utf8_decode('      REFACCION                                PRECIO '),0,'L',0);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);     
        $pdf->SetFont('Helvetica','', 9);   
        while($refaccion = mysqli_fetch_array($SqlRefacciones)){
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->MultiCell(36,4,utf8_decode($refaccion['descripcion']),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(42);
            $pdf->MultiCell(32,4,utf8_decode('$'.sprintf('%.2f',$refaccion['cantidad'])),0,'R',0);
        }
         $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    }
        //MOSTRAR TOTALES 
    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->MultiCell(32,4,utf8_decode('MATERIAL(REFA.):'."\n".'MANO OBRA:'."\n".'ANTICIPO(S):'),0,'R',0);    
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(37);
    $pdf->MultiCell(37,4,utf8_decode('$'.sprintf('%.2f',$fila['t_refacciones'])."\n".'$'.sprintf('%.2f',$fila['mano_obra'])."\n".'-$'.sprintf('%.2f',$Total_anti)),0,'R',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('PAGO REALIZADO'),0,'C',0);// MUESTRA LA DESCRIPCION DEL PAGO Y EL TIPO TOTAL
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);       
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 9);    
    $pdf->MultiCell(70,4,utf8_decode(' DESCRIPCION             T.CAMBIO      TOTAL'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(7);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(32,3,utf8_decode($pago['descripcion']),0,'L',0);
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(39);
    $pdf->MultiCell(14,3,utf8_decode($pago['tipo_cambio']),0,'R',0);    
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(54);
    $pdf->MultiCell(21,3,utf8_decode('$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);
    $pdf->SetFont('Helvetica','', 8);
    if ($pago['tipo_cambio'] == 'Banco') {
        $referencia = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM referencias WHERE id_pago = $id_pago")); 
        $ReferenciaB = $referencia['descripcion'];
        $pdf->SetY($pdf->GetY()+1);
        $pdf->SetX(25);
        $pdf->MultiCell(34,3,utf8_decode($ReferenciaB),0,'R',0);
    }
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->MultiCell(30,4,utf8_decode('IVA:'."\n".'SUBTOTAL:'."\n".'TOTAL:'),0,'R',0);  /// TOTALES Y DESGLOSE DE IVA  
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(35);
    $pdf->MultiCell(40,4,utf8_decode('$'.sprintf('%.2f',$pago['cantidad']*0.16)."\n".'$'.sprintf('%.2f',$pago['cantidad']-($pago['cantidad']*0.16))."\n".'$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);

    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LA SALIDA
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);  
    $pdf->SetFont('Helvetica','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(70,6,utf8_decode("\n"."\n".'__________________________________'."\n".'Nombre y Firma (Conformidad)'),1,'C',0);

    $pdf->Output('Folio_'.$id_dispositivo.'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color'],'I');

    date_default_timezone_set('America/Mexico_City');
    $FechaSalida = date('Y-m-d');
    mysqli_query ($conn, "UPDATE dispositivos SET  estatus='Entregado', fecha_salida='$FechaSalida' WHERE id_dispositivo='$id_dispositivo'");
?> 