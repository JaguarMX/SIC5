<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
    include('../php/conexion.php');
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");
    #INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
    include('is_logged.php');

    $id_pago = $_GET['IdPago'];;//TOMAMOS EL ID DEl PAGO
    $pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago"));

    #DEFINIMOS UNA ZONA HORARIA
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
   
class PDF extends FPDF{

    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->setTitle(utf8_decode('SIC | TICKET PAGO'));// TITULO BARRA NAVEGACION
    $pdf->AddPage();

    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','B', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'RFC: VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
    if ($pago['fecha'] < $Fecha_hoy) {
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(5);
        $pdf->MultiCell(70,3,utf8_decode('Fecha Reimprieción: '.date_format(new \DateTime($Fecha_hoy), "d/m/Y" )),0,'R',0);
    }
    /// INFORMACION DEL PAGO
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10);    
    $folio = substr(str_repeat(0, 5).$id_pago, - 6);
    $pdf->MultiCell(70,4,utf8_decode(date_format(new \DateTime($pago['fecha'].' '.$pago['hora']), "d/m/Y H:i" ).'             FOLIO: '.$folio),0,'C',0);

    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('TICKET PAGO'),0,'C',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0); 
    /// INFORMACION DEL CLIENTE
    $id_cliente = $pago['id_cliente'];
    if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
    }else{
        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
    }     
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Courier','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('N° CLIENTE: '.$id_cliente."\n".'NOMBRE:  '.$cliente['nombre']."\n".'TELEFONO:  '.$cliente['telefono']),0,'L',0);

    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    // INFORMACION DEL PAGO
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('TIPO: '.$pago['tipo']),0,'C',0);
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
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->MultiCell(35,3,utf8_decode($pago['descripcion']),0,'L',0);
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(40);
    $pdf->MultiCell(14,3,utf8_decode($pago['tipo_cambio']),0,'R',0);    
    $pdf->SetY($pdf->GetY()-3);
    $pdf->SetX(55);
    $pdf->MultiCell(20,3,utf8_decode('$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);
    $pdf->SetFont('Helvetica','', 8);
    if ($pago['tipo_cambio'] == 'Banco') {
        $referencia = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM referencias WHERE id_pago = $id_pago")); 
        $ReferenciaB = $referencia['descripcion'];
        $pdf->SetY($pdf->GetY()+1);
        $pdf->SetX(25);
        $pdf->MultiCell(35,3,utf8_decode($ReferenciaB),0,'R',0);
    }
    $id_user = $pago['id_user'];// ID DEL USUARIO AL QUE SE LE APLICO EL CORTE
    if ((in_array($id_user, array(47, 42, 31, 52, 67, 57, 63, 24, 55, 29, 64, 93, 99, 115))) AND $pago['tipo'] != 'Otros Pagos') {
        $pdf->SetY($pdf->GetY()+1);
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 9);
        $pdf->MultiCell(35,3,utf8_decode('Comisión de cobrador'),0,'L',0);
        $pdf->SetY($pdf->GetY()-3);
        $pdf->SetX(40);
        $pdf->MultiCell(14,3,utf8_decode($pago['tipo_cambio']),0,'R',0);    
        $pdf->SetY($pdf->GetY()-3);
        $pdf->SetX(55);
        $pdf->MultiCell(20,3,utf8_decode('$'.sprintf('%.2f',10)),0,'R',0);
        $pago['cantidad'] += 10;
    }
    
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
   
    if ($pago['tipo'] == 'Abono' AND $pago['descripcion'] != 'Abono de instalacion') {
        // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
        $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $id_cliente"));
        $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Abono'"));
        //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACIOS::
        if ($deuda['suma'] == "") {
                    $deuda['suma'] = 0;
        }elseif ($abono['suma'] == "") {
                    $abono['suma'] = 0;
        }
        //SE RESTAN DEUDAS DE ABONOS 
        $Saldo = $abono['suma']-$deuda['suma'];
        $msj = 'SALDO A FAVOR: ';
        if ($Saldo < 0) {
            $Saldo = $Saldo*(-1);
            $msj = 'SALDO A PAGAR: ';
        }
        $pdf->SetFont('Helvetica','', 9);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->MultiCell(30,4,utf8_decode('IVA:'."\n".'SUBTOTAL:'."\n".'TOTAL:'."\n"."\n".$msj),0,'R',0);    
        $pdf->SetY($pdf->GetY()-20);
        $pdf->SetX(35);
        $pdf->MultiCell(40,4,utf8_decode('$'.sprintf('%.2f',$pago['cantidad']*0.16)."\n".'$'.sprintf('%.2f',$pago['cantidad']-($pago['cantidad']*0.16))."\n".'$'.sprintf('%.2f',$pago['cantidad'])."\n"."\n".'$'.sprintf('%.2f',$Saldo)),0,'R',0);
    }else{
        $pdf->SetFont('Helvetica','', 9);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->MultiCell(30,4,utf8_decode('IVA:'."\n".'SUBTOTAL:'."\n".'TOTAL:'),0,'R',0);    
        $pdf->SetY($pdf->GetY()-12);
        $pdf->SetX(35);
        $pdf->MultiCell(40,4,utf8_decode('$'.sprintf('%.2f',$pago['cantidad']*0.16)."\n".'$'.sprintf('%.2f',$pago['cantidad']-($pago['cantidad']*0.16))."\n".'$'.sprintf('%.2f',$pago['cantidad'])),0,'R',0);
    }

    $pdf->SetY($pdf->GetY()+6);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10);      
    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));  
    $pdf->MultiCell(70,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);

    if ($pago['tipo_cambio'] == 'Credito') {
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 10);
        $pdf->MultiCell(70,5,utf8_decode("\n"."\n"."\n".'__________________________________'."\n".'Nombre y Firma (Cliente)'),1,'C',0);
    }

    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);    
    $pdf->SetFont('Helvetica','B', 10);      
    $pdf->MultiCell(70,4,utf8_decode('¡GRACIAS POR TU PAGO!'."\n".'TODO LO QUE QUIERES ESTA EN SIC'),0,'C',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $msj = '';
    if ($pago['tipo'] == 'Mensualidad') {
        $msj = 'RECOMENDACIONES:'."\n"."\n".'1. DEBE REALIZAR SU PROXIMO PAGO ANTES         DEL:  '.date_format(new \DateTime($cliente['fecha_corte']), "d/m/Y")."\n".'2. CONTAR CON LINEA REGULADA                              (REGULADOR DE CORRIENRE).'."\n".'3. NO MODIFICAR EL ORDEN DEL CABLEADO.'."\n".'4. NO PRESIONAR BOTONES DEL MODEM.'."\n".'5. EN CASO DE ALGUNA "FALLA"                                COMUNICARSE AL 433 935 6286.';
    }elseif ($pago['tipo'] == 'Reporte' OR $pago['tipo'] == 'Liquidacion'){
        $msj = 'RECOMENDACIONES:'."\n"."\n".'1. CONTAR CON LINEA REGULADA                              (REGULADOR DE CORRIENRE).'."\n".'2. NO MODIFICAR EL ORDEN DEL CABLEADO.'."\n".'3. NO PRESIONAR BOTONES DEL MODEM.'."\n".'4. EN CASO DE ALGUNA "FALLA"                                COMUNICARSE AL 433 935 6286.';
    }
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 8);
    $pdf->MultiCell(70,5,utf8_decode($msj),1,'L',0);

    $pdf->Output('VENTA','I');
?>