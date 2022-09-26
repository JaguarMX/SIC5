<?php
    #INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
    include('../php/conexion.php');
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");

    $corte = $_GET['id'];//TOMAMOS EL ID DEL CORTE PREVIAMENTE CREADO PARA¨PODERLE ASIGNAR LOS PAGOS EN EL DETALLE
    #DEFINIMOS UNA ZONA HORARIA
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
    #TOMAMOS LA INFORMACION DEL CORTE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
    $Info_Corte =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM cortes WHERE id_corte = $corte")); 
    $id_user = $Info_Corte['usuario'];// ID DEL USUARIO QUE HIZO EL CORTE
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
    $pdf->setTitle(utf8_decode('SIC | CORTE PAGOS '));// TITULO BARRA NAVEGACION
    $pdf->AddPage();

    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(6);
    $pdf->MultiCell(69,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
    /// INFORMACION DEL CORTE
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(69,4,utf8_decode(date_format(new \DateTime($Info_Corte['fecha'].' '.$Info_Corte['hora']), "d/m/Y H:i" ).'              FOLIO:0'.$corte),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(69,4,utf8_decode('CORTE DE CAJA'."\n".'USUARIO: '.$usuario['firstname']),0,'C',0);
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

    $pdf->SetY($pdf->GetY()+5);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 12);
    $pdf->MultiCell(69,4,utf8_decode('==== VENTAS SERVICIOS ===='),0,'C',0);

    //////////      DESGOSE DE PAGOS DE INTERNET      /////////////
    $sql_efectivo_int = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");
    $sql_banco_int = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");
    $sql_credito_int = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Credito' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");

    if (mysqli_num_rows($sql_efectivo_int) > 0 OR mysqli_num_rows($sql_banco_int) > 0 OR mysqli_num_rows($sql_credito_int) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','B', 10);
        $pdf->MultiCell(69,4,utf8_decode('<<Internet>>'),0,'C',0);///   >>>>>>>>>>>>>>>>>>>>>>>>
        if (mysqli_num_rows($sql_efectivo_int) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);// ***********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_Efectivo_int = 0;
            while($pagoE = mysqli_fetch_array($sql_efectivo_int)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoE['id_cliente'].'; '.$pagoE['tipo']."\n".$pagoE['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoE['cantidad'])),0,'R',0);
                $Total_Efectivo_int += $pagoE['cantidad'];
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
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_Efectivo_int)),0,'R',0);
        }// FIN IF EFECTIVO
        if (mysqli_num_rows($sql_banco_int) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A BANCO :::'),0,'L',0);//   **********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_banco_int = 0;
            while($pagoB = mysqli_fetch_array($sql_banco_int)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoB['id_cliente'].'; '.$pagoB['tipo']."\n".$pagoB['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoB['cantidad'])),0,'R',0);
                $Total_banco_int += $pagoB['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A BANCO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_banco_int)),0,'R',0);
        }/// FIN IF BANCO
        if (mysqli_num_rows($sql_credito_int) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A CREDITO :::'),0,'L',0);//    *******************
            $pdf->SetY($pdf->GetY()+1);
            $Total_credito_int = 0;
            while($pagoC = mysqli_fetch_array($sql_credito_int)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoC['id_cliente'].'; '.$pagoC['tipo']."\n".$pagoC['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoC['cantidad'])),0,'R',0);
                $Total_credito_int += $pagoC['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A CREDITO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_credito_int)),0,'R',0);
        }// FIN IF CREDITO
    }//FIN IF INTERNET

    //////////      DESGOSE DE PAGOS DE TELEFONIA      /////////////
    $sql_efectivo_tel = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo IN ('Mes-Tel', 'Min-Extra')");
    $sql_banco_tel = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo IN ('Mes-Tel', 'Min-Extra')");
    $sql_credito_tel = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Credito' AND pagos.tipo IN ('Mes-Tel', 'Min-Extra')");

    if (mysqli_num_rows($sql_efectivo_tel) > 0 OR mysqli_num_rows($sql_banco_tel) > 0 OR mysqli_num_rows($sql_credito_tel) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Telefonía>>'),0,'C',0);/// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if (mysqli_num_rows($sql_efectivo_tel) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);//// *****************************
            $pdf->SetY($pdf->GetY()+1);
            $Total_Efectivo_tel = 0;
            while($pagoET = mysqli_fetch_array($sql_efectivo_tel)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoET['id_cliente'].'; '.$pagoET['tipo']."\n".$pagoET['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoET['cantidad'])),0,'R',0);
                $Total_Efectivo_tel += $pagoET['cantidad'];
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
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_Efectivo_tel)),0,'R',0);
        }//FIN IF EFECTIVO
        if (mysqli_num_rows($sql_banco_tel) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A BANCO :::'),0,'L',0);/// ***********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_banco_tel = 0;
            while($pagoBT = mysqli_fetch_array($sql_banco_tel)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoBT['id_cliente'].'; '.$pagoBT['tipo']."\n".$pagoBT['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoBT['cantidad'])),0,'R',0);
                $Total_banco_tel += $pagoBT['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A BANCO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_banco_tel)),0,'R',0);
        }//FIN IF BANCO
        if (mysqli_num_rows($sql_credito_tel) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A CREDITO :::'),0,'L',0);//// ********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_credito_tel = 0;
            while($pagoCT = mysqli_fetch_array($sql_credito_tel)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoCT['id_cliente'].'; '.$pagoCT['tipo']."\n".$pagoCT['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoCT['cantidad'])),0,'R',0);
                $Total_credito_tel += $pagoCT['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A CREDITO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_credito_tel)),0,'R',0);
        }//FIN IF CREDITO
    }//FIN IF TELEFONIA

    //////////      DESGOSE DE PAGOS DE ORDENES DE SERVICIO      /////////////
    $sql_efectivo_Orden = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Orden Servicio'");
    $sql_banco_Orden = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo = 'Orden Servicio'");
    $sql_credito_Orden = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Credito' AND pagos.tipo = 'Orden Servicio'");

    if (mysqli_num_rows($sql_efectivo_Orden) > 0 OR mysqli_num_rows($sql_banco_Orden) > 0 OR mysqli_num_rows($sql_credito_Orden) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Orden Servicio>>'),0,'C',0); ////// >>>>>>>>>>>>>>>>>>>>>>>
        if (mysqli_num_rows($sql_efectivo_Orden) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);///   ****************************
            $pdf->SetY($pdf->GetY()+1);
            $Total_Efectivo_Orden = 0;
            while($pagoEO = mysqli_fetch_array($sql_efectivo_Orden)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoEO['id_cliente'].'; '.$pagoEO['tipo']."\n".$pagoEO['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoEO['cantidad'])),0,'R',0);
                $Total_Efectivo_Orden += $pagoEO['cantidad'];
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
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_Efectivo_Orden)),0,'R',0);
        }//FIN IF EFECTIVO
        if (mysqli_num_rows($sql_banco_Orden) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A BANCO :::'),0,'L',0);///  ********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_banco_Orden = 0;
            while($pagoBO = mysqli_fetch_array($sql_banco_Orden)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoBO['id_cliente'].'; '.$pagoBO['tipo']."\n".$pagoBO['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoBO['cantidad'])),0,'R',0);
                $Total_banco_Orden += $pagoBO['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A BANCO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_banco_Orden)),0,'R',0);
        }//IF IF BANCO
        if (mysqli_num_rows($sql_credito_Orden) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A CREDITO :::'),0,'L',0);//  ***************
            $pdf->SetY($pdf->GetY()+1);
            $Total_credito_Orden = 0;
            while($pagoCO = mysqli_fetch_array($sql_credito_Orden)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoCO['id_cliente'].'; '.$pagoCO['tipo']."\n".$pagoCO['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoCO['cantidad'])),0,'R',0);
                $Total_credito_Orden += $pagoCO['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A CREDITO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_credito_Orden)),0,'R',0);
        }// FIN IF CREDITO
    }//FIN IF OREDEN SERVICIO

    //////////      DESGOSE DE PAGOS DE SERVICIO TECNICO       /////////////
    $sql_efectivo_SevT = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Dispositivo'");
    $sql_banco_SevT = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo = 'Dispositivo'");
    $sql_credito_SevT = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Credito' AND pagos.tipo = 'Dispositivo'");

    if (mysqli_num_rows($sql_efectivo_SevT) > 0 OR mysqli_num_rows($sql_banco_SevT) > 0 OR mysqli_num_rows($sql_credito_SevT) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Servicio Técnico>>'),0,'C',0);// >>>>>>>>>>>>>>>>>>>>>>>>>
        if (mysqli_num_rows($sql_efectivo_SevT) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);// ***********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_Efectivo_SevT = 0;
            while($pagoES = mysqli_fetch_array($sql_efectivo_SevT)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoES['id_cliente'].'; '.$pagoES['tipo']."\n".$pagoES['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoES['cantidad'])),0,'R',0);
                $Total_Efectivo_SevT += $pagoES['cantidad'];
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
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_Efectivo_SevT)),0,'R',0);
        }//FIN IF EFECTIVO
        if ( mysqli_num_rows($sql_banco_SevT) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A BANCO :::'),0,'L',0);// *********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_banco_SevT = 0;
            while($pagoBS = mysqli_fetch_array($sql_banco_SevT)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoBS['id_cliente'].'; '.$pagoBS['tipo']."\n".$pagoBS['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoBS['cantidad'])),0,'R',0);
                $Total_banco_SevT += $pagoBS['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A BANCO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_banco_SevT)),0,'R',0);
        }//FIN IF BANCO
        if (mysqli_num_rows($sql_credito_SevT) > 0) {
            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A CREDITO :::'),0,'L',0);///**********************
            $pdf->SetY($pdf->GetY()+1);
            $Total_credito_SevT = 0;
            while($pagoCS = mysqli_fetch_array($sql_credito_SevT)){
                $pdf->SetX(4);
                $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoCS['id_cliente'].'; '.$pagoCS['tipo']."\n".$pagoCS['descripcion']),0,'L',0);
                $pdf->SetY($pdf->GetY()-4);
                $pdf->SetX(54);
                $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoCS['cantidad'])),0,'R',0);
                $Total_credito_SevT += $pagoCS['cantidad'];
            }//FIN WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A CREDITO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_credito_SevT)),0,'R',0);
        }//FIN IF CREDITO
    }// IFN IF SERVICIO TECNICO 

    //////////      DESGOSE DE PAGOS DE SICFLIX      /////////////
    #$sql_efectivo_sicflix = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");
    #$sql_banco_sicflix = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");
    #$sql_credito_sicflix = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Credito' AND pagos.tipo NOT IN ('Mes-Tel', 'Min-Extra', 'Orden Servicio', 'Corte SAN', 'Dispositivo')");

    if (false) { #mysqli_num_rows($sql_efectivo_sicflix) > 0 OR mysqli_num_rows($sql_banco_sicflix) > 0 OR mysqli_num_rows($sql_credito_sicflix) > 0
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Sicflix>>'),0,'C',0);//// >>>>>>>>>>>>>>>>>>>>>

            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);
            #WHILE
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
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', 100000)),0,'R',0);

            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A BANCO :::'),0,'L',0);
            #WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A BANCO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', 34252)),0,'R',0);

            $pdf->SetY($pdf->GetY()+2);
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(69,4,utf8_decode('::: A CREDITO :::'),0,'L',0);
            #WHILE
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','', 8);
            $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
            $pdf->SetY($pdf->GetY());
            $pdf->SetX(6);
            $pdf->SetFont('Helvetica','B', 9);
            $pdf->MultiCell(35,4,utf8_decode('TOTAL A CREDITO'),0,'L',0);    
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(41);
            $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', 34252)),0,'R',0);
    }//FIN IF SICFLIX

    //////////   PAGO DEL SAN CORTE    /////////
    $sql_corteSAN = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Corte SAN'");
    if (mysqli_num_rows($sql_corteSAN) > 0) {
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
        $pdf->MultiCell(69,4,utf8_decode('<<Corte SAN>>'),0,'C',0);//// >>>>>>>>>>>>>>>>>>>>>
        $pdf->SetY($pdf->GetY()+2);
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','', 9);
        $pdf->MultiCell(69,4,utf8_decode('::: EN EFECTIVO :::'),0,'L',0);// ***********************
        $pdf->SetY($pdf->GetY()+1);
        $Total_SAN_corte = 0;
        while($pagoESAN = mysqli_fetch_array($sql_corteSAN)){
            $pdf->SetX(4);
            $pdf->MultiCell(50,4,utf8_decode(' -- N°Clte: '.$pagoESAN['id_cliente'].'; '.$pagoESAN['tipo']."\n".$pagoESAN['descripcion']),0,'L',0);
            $pdf->SetY($pdf->GetY()-4);
            $pdf->SetX(54);
            $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $pagoESAN['cantidad'])),0,'R',0);
            $Total_SAN_corte += $pagoESAN['cantidad'];
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
        $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_SAN_corte)),0,'R',0);
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

    $pdf->Output('CORTE','I');