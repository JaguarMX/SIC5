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
    $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $corte AND cobrador = $id_user");
    if (mysqli_num_rows($sql_deuda) > 0) {
        $deuda = mysqli_fetch_array($sql_deuda);
        $DEUDA = $deuda['cantidad'];        
    }else{
        $DEUDA = 0;
    }

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
    $pdf->MultiCell(69,4,utf8_decode('CORTE DE CAJA'."\n".'USUARIO: '.$usuario['firstname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+6);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(49,4,utf8_decode('CORTE EN EFECTIVO'."\n".'DEDUCIBLE'."\n".'DEUDA (Saldo Pendiente)'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(55);
    $pdf->MultiCell(20,4,utf8_decode('$'.sprintf('%.2f', $Info_Corte['cantidad'])."\n".'-$'.sprintf('%.2f', $Deducir)."\n".'-$'.sprintf('%.2f', $DEUDA)),0,'R',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(49,4,utf8_decode('EFECTIVO ENTREGADO'),0,'L',0);    
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