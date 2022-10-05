<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
    include('../php/conexion.php');
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");
    #INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
    include('is_logged.php');

    $corte = $_GET['id'];//TOMAMOS EL ID DEL CORTE PREVIAMENTE CREADO PARA¨PODERLE ASIGNAR LOS PAGOS EN EL DETALLE
    #DEFINIMOS UNA ZONA HORARIA
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
    #TOMAMOS LA INFORMACION DEL CORTE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
    $Info_Corte =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM cortes WHERE id_corte = $corte")); 
    $id_user = $Info_Corte['usuario'];// ID DEL USUARIO AL QUE SE LE APLICO EL CORTE
    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
    #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
    $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
    if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $Deducir = $Deducible['cantidad'];
    }else{
        $Deducir = 0;
    }
    $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $corte AND cobrador = $id_user");
    if (mysqli_num_rows($sql_deuda) > 0) {
        $deuda = mysqli_fetch_array($sql_deuda);
        $DEUDA = $deuda['cantidad'];        
    }else{
        $DEUDA = 0;
    }
    /// INFO DE PAGO SAN
    $sql_corteSAN = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Corte SAN'");
    $pagoESAN = mysqli_fetch_array($sql_corteSAN);

    //CONTAMOS LOS PAGOS SEGUN SU TIPO
    $Mes_Internet = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Mensualidad'" ));
    $Otros = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Otros Pagos'" ));
    $AntInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Anticipo'" ));
    $AbonoInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Abono Instalacion'" ));
    $LiquidInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Liquidacion'" ));
    $Reporte = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Reporte'" ));
    $Telefono = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo IN ('Mes-Tel', 'Min-Extra')" ));
    $AntiDisp = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Dispositivo' AND pagos.descripcion = 'Anticipo'" ));
    $LiquidDisp = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Dispositivo' AND pagos.descripcion = 'Liquidacion'" ));
    $Orden = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Orden Servicio'"));
    $SAN = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo = 'Corte SAN'"));
class PDF extends FPDF{

    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->setTitle(utf8_decode('SIC | CONFIRMAR CORTE '));// TITULO BARRA NAVEGACION
    $pdf->AddPage();

    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(6);
    $pdf->MultiCell(69,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
    /// INFORMACION DEL CORTE
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(69,4,utf8_decode(date_format(new \DateTime($Info_Corte['fecha'].' '.$Info_Corte['hora']), "d/m/Y H:i" ).'              FOLIO:0'.$corte),0,'C',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(69,4,utf8_decode('CORTE DE CAJA'."\n".'USUARIO: '.$usuario['firstname']."\n".'REALIZO: '.$Info_Corte['realizo']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
     //////////////         TOTALES DE CANTIDADES       ////////////////
    $TotalPagos = mysqli_num_rows(mysqli_query($conn,"SELECT *FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte" ));
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(69,4,utf8_decode($TotalPagos.' PAGOS TOTALES'),0,'C',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 12);
    $pdf->MultiCell(69,4,utf8_decode('======== VENTAS ========'),0,'C',0);
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(35,4,utf8_decode('EN EFECTIVO'."\n".'DEDUCIBLE'."\n".'A BANCO'."\n".'A CREDITO'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-16);
    $pdf->SetX(41);
    $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Info_Corte['cantidad'])."\n".'-$'.sprintf('%.2f', $Deducir)."\n".'$'.sprintf('%.2f', $Info_Corte['banco'])."\n".'$'.sprintf('%.2f', $Info_Corte['credito'])),0,'R',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(35,4,utf8_decode('TOTAL VENTAS'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-4);
    $pdf->SetX(41);
    $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Info_Corte['cantidad']+$Info_Corte['banco']+$Info_Corte['credito']-$Deducir)),0,'R',0);

    ///////      DESGOSE DE PAGOS         //////////
    $pdf->SetY($pdf->GetY()+6);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 12);
    $pdf->MultiCell(69,4,utf8_decode('==== DESGLOSE PAGOS ===='),0,'C',0);
    $CONTENIDO = '';
    $CONTENIDO .= ($Mes_Internet>0) ?'MENSUALIDADES                               '.$Mes_Internet."\n":'';
    $CONTENIDO .= ($Otros>0)?'OTROS PAGOS                                   '.$Otros."\n":'';
    $CONTENIDO .= ($AntInst>0)?'ANTICIPO INSTALACION                    '.$AntInst."\n":'';
    $CONTENIDO .= ($AbonoInst>0)?'ABONO INSTALACION                       '.$AbonoInst."\n":'';
    $CONTENIDO .= ($LiquidInst>0)?'LIQUIDACION INSTALACION             '.$LiquidInst."\n":'';
    $CONTENIDO .= ($Reporte>0)?'REPORTE                                            '.$Reporte."\n":'';
    $CONTENIDO .= ($Telefono>0)?'TELEFONIA                                         '.$Telefono."\n":'';
    $CONTENIDO .= ($AntiDisp>0)?'ANTICIPO DISPOSITIVO                    '.$AntiDisp."\n":'';
    $CONTENIDO .= ($LiquidDisp>0)?'LIQUIDACION DISPOSITIVO              '.$LiquidDisp."\n":'';
    $CONTENIDO .= ($Orden>0)?'ORDEN SERVICIO                              '.$Orden."\n":'';
    $CONTENIDO .= ($SAN>0)?'CORTE SAN                                        '.$SAN."\n":'';
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(10);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(65,4,utf8_decode($CONTENIDO),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+6);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(49,4,utf8_decode('CORTE EN EFECTIVO SIC'."\n".'CORTE EN EFECTIVO SAN'."\n".'DEDUCIBLE'."\n".'DEUDA (Saldo Pendiente)'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-16);
    $pdf->SetX(55);
    $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $Info_Corte['cantidad']-$pagoESAN['cantidad'])."\n".'$'.sprintf('%.2f', $pagoESAN['cantidad'])."\n".'-$'.sprintf('%.2f', $Deducir)."\n".'-$'.sprintf('%.2f', $DEUDA)),0,'R',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);

     //////////   PAGO DEL SAN CORTE    /////////
    if (mysqli_num_rows($sql_corteSAN) > 0) {
        $pdf->SetFont('Helvetica','B', 9);
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Corte SAN>>'),0,'C',0);//// >>>>>>>>>>>>>>>>>>>>>
        $pdf->SetY($pdf->GetY()+2);
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','', 9);
        $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);// ***********************
        $pdf->SetY($pdf->GetY()+1);
        $pdf->SetX(4);
        $pdf->MultiCell(50,4,utf8_decode(' --'.$pagoESAN['tipo'].'; '.$pagoESAN['descripcion']),0,'L',0);
        $pdf->SetY($pdf->GetY()-4);
        $pdf->SetX(54);
        $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoESAN['cantidad'])),0,'R',0);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    }// FIN IF SAN

    //////////   DEDUCIBLES APLICADOS    /////////
    $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
    if (mysqli_num_rows($sql_Deducible) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Deducibles>>'),0,'C',0);//// >>>>>>>>>>>>>>>>>>>>>
        $pdf->SetY($pdf->GetY()+2);
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','', 9);
        $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);// ***********************
        $pdf->SetY($pdf->GetY()+1);
        $Total_dedicible = 0;
        while($pagoED = mysqli_fetch_array($sql_Deducible)){
            $pdf->SetX(4);
            $pdf->MultiCell(50,4,utf8_decode(' -- N°Corte: '.$pagoED['id_corte'].'; '.$pagoED['descripcion']),0,'L',0);
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(54);
            $pdf->MultiCell(20,4,utf8_decode('-$'.sprintf('%.2f', $pagoED['cantidad'])),0,'R',0);
            $Total_dedicible += $pagoED['cantidad'];
        }//FIN WHILE
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','B', 9);
        $pdf->MultiCell(35,4,utf8_decode('TOTAL EFECTIVO'),0,'L',0);    
        $pdf->SetY($pdf->GetY()-4);
        $pdf->SetX(41);
        $pdf->MultiCell(34,4,utf8_decode('-$'.sprintf('%.2f', $Total_dedicible)),0,'R',0);
    }// FIN IF DEDUCIBLE

    $pdf->SetY($pdf->GetY()+6);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(49,4,utf8_decode('>EFECTIVO ENTREGADO'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-4);
    $pdf->SetX(55);
    $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $Info_Corte['cantidad']-$Deducir-$DEUDA)),0,'R',0);
    $pdf->SetY($pdf->GetY()+5);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(69,5,utf8_decode("\n"."\n"."\n".'__________________________________'."\n".'Firma de Conformidad'),1,'C',0);
    $pdf->Ln(3);


    $pdf->Output('CORTE','I');
?>