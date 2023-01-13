<?php
//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include('is_logged.php');
include('conexion.php');
$id = $_GET['id'];;//TOMAMOS EL ID DEl PAGO
$listado = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente='$id'");
$fila = mysqli_fetch_array($listado);
$id_comunidad = $fila['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
//Incluimos el archivo de conexion a la base de datos
class PDF extends FPDF{
    function folioCliente()
    {
       
    }
}
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('INSTALACION');
    $pdf->AddPage();

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
    $folio = substr(str_repeat(0, 5).$id, - 6);
    $pdf->MultiCell(70,4,utf8_decode(date_format(new \DateTime($fila['fecha_registro']), "d/m/Y" ).'              N° CLIENTE: '.$folio),0,'C',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(70,4,utf8_decode('INSTALACION'),0,'C',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0); 
    /// INFORMACION DEL CLIENTE
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Courier','B', 9);
    $pdf->MultiCell(70,4,utf8_decode('NOMBRE: '.$fila['nombre']."\n".'TELEFONO: '.$fila['telefono']."\n".'REFERENCIA: '.$fila['referencia']),0,'L',0);

    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);

    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->MultiCell(30,4,utf8_decode('COSTO TOTAL:'."\n".'DEJO:'."\n".'RESTA:'),0,'R',0);    
    $pdf->SetY($pdf->GetY()-12);
    $pdf->SetX(35);
    $pdf->MultiCell(40,4,utf8_decode('$'.sprintf('%.2f',$fila['total'])."\n".'$'.sprintf('%.2f',$fila['dejo'])."\n".'$'.sprintf('%.2f',$fila['total']-$fila['dejo'])),0,'R',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $sql_pago = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id");
    if (mysqli_num_rows($sql_pago)>0) {
        $pago = mysqli_fetch_array($sql_pago);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','B', 11);
        $pdf->MultiCell(70,4,utf8_decode('PAGO REALIZADO:'),0,'C',0);

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

        
        $id_pago = $pago['id_pago'];
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
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(5);
        $pdf->SetFont('Helvetica','', 8);
        $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    }
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10);   
    $firstname = $fila['registro'];// ID DEL USUARIO AL QUE SE LE APLICO EL REGISTRO   
    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE firstname LIKE '%$firstname%'"));  
    $pdf->MultiCell(70,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(70,6,utf8_decode("\n"."\n".'__________________________________'."\n".'Nombre y Firma (Cliente)'),1,'C',0);
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(5);    
    $pdf->SetFont('Helvetica','B', 10);      
    $pdf->MultiCell(70,4,utf8_decode('¡GRACIAS POR TU PAGO!'."\n".'TODO LO QUE QUIERES ESTA EN SIC'),0,'C',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(5);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->Output('INSTALACION','I');
?>