<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
date_default_timezone_set('America/Mexico_City');

// OBTENEMOS LA INFORMACION DEL USUARIO PARA OBTENER EL DATO DEL ALMACEN
$user_id = $_SESSION['user_id'];
$id = $_GET['id']; // RECIBIMOS EL ID DEL PAGO
//SACAMOS LA INFORMACION DEL NOMBRE DEL ALMACEN
$pago = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `pagos_centrales` WHERE id=$id"));
$id_central = $pago['id_central'];
$central = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `centrales` WHERE id=$id_central"));

class PDF extends FPDF{
   //Cabecera de página
   function Header(){  }
   //Pie de pagina 
   function footer(){  }
}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','letter', true);
$pdf->SetAutoPageBreak(true, 35);
$pdf->AliasNbPages();
$pdf->SetMargins(15, 35, 10);
$pdf->setTitle(utf8_decode('SIC | PAGO CENTRAL '));// TITULO BARRA NAVEGACION
$pdf->AddPage('portrait', 'letter');

$pdf->SetFont('Helvetica','B', 12);
$pdf->Image('../img/logo.jpg', 35, 6, 23, 23, 'jpg'); /// LOGO SIC
/////   RECUADRO DERECHO  FECHA  //////
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetY($pdf->GetY()-25);
$pdf->SetX(120);
$pdf->Cell(70,4,utf8_decode(' '),1,0,'C',1);
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(120);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(35,8,utf8_decode('Fecha Impresión:'),1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(155);
$pdf->Cell(35,8,utf8_decode(date_format(new \DateTime(date('Y-m-d H:i')), "d/m/Y    H:i")),1,0,'C');
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(120);
$pdf->SetFont('Helvetica','B', 12);
$pdf->Cell(35,8,utf8_decode('RECIBO N°:'),1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(155);
$pdf->SetTextColor(255, 0, 0);
$folio = substr(str_repeat(0, 5).$id, - 6);
$pdf->Cell(35,8,$folio,1,0,'C');
$pdf->Ln();
/////   RECAUADRO AZUL DEL CENTRO   ////////
$pdf->SetY($pdf->GetY()+2);
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->MultiCell(0,6,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n"),0,'C',1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetY($pdf->GetY());
$pdf->MultiCell(0,5,utf8_decode('Tels: 433-93-562-86 y 433-93-562-88'),0,'C',1);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->MultiCell(0,6,utf8_decode('GABRIEL VALLES REYES                                                                                         RFC: VARG7511217E5'),0,'C',1);

////   TITULO ANTES DE CONTENIDO  ///////
$pdf->SetTextColor(28, 98, 163);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 13);
$pdf->MultiCell(0,9,utf8_decode('RECIBO DE PAGO'),1,'C',0);
$pdf->SetY($pdf->GetY()+1);
$pdf->SetX(128);
$pdf->SetTextColor(0, 0, 0);

/// CANTIDAD ////
$pdf->Cell(15,7,utf8_decode('POR:'),0,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(147);
$pdf->SetFont('Courier','B', 14);
$pdf->Cell(56,6,'$'.sprintf('%.2f',$pago['cantidad']),1,0,'C');
$pdf->Ln();

/// TABLA O CONTENIDO ///
$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->SetX(17);
$pdf->MultiCell(55,7,utf8_decode("\n".'    RECIBO DEL SR.(A):'."\n".'    POR CONCEPTO DE: '."\n".' '),1,'L',0);
$pdf->SetY($pdf->GetY()-28);
$pdf->SetX(72);
$pdf->SetFont('Courier','', 11);
$pdf->MultiCell(131,7,utf8_decode("\n".$central['nombre'].'    LA CANTIDAD DE: $'.sprintf('%.2f',$pago['cantidad'])."\n".'RENTA CORRESPONDIENTE ('.$pago['tipo'].') '.$pago['descripcion']."\n".' '),1,'L',0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetY($pdf->GetY()-27.8);
$pdf->SetX(70);
$pdf->Cell(3,27.5,utf8_decode(' '),0,0,'C',1);
$pdf->Ln();
$pdf->SetY($pdf->GetY()-18);
$pdf->SetX(70);
$pdf->Cell(130,7,'........................................................',0,0,'C');
$pdf->SetY($pdf->GetY()+7);
$pdf->SetX(70);
$pdf->Cell(130,7,'........................................................',0,0,'C');

///   FIRMAS  ////////
$pdf->Ln();
$pdf->SetY($pdf->GetY()+10);
$pdf->SetX(55);
$pdf->Cell(130,7,'__________________________           __________________________',0,0,'C');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(55);
$pdf->Cell(130,7,utf8_decode('    RECIBÍ CONFORME                                                   ENTREGUÉ CONFORME'),0,0,'C');

//// PIE DEL RECIBO /////
$pdf->Ln();
$pdf->SetY($pdf->GetY()+1);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(28, 98, 163);
$pdf->MultiCell(0,9,utf8_decode('Estamos ubicados en:                                                             Av. Hidalgo No. 508 C. P. 99100, Sombrerete, Zac.'),0,'C',1);



//////// SEGUNDO RECIBO O MEDIA HOJA ///////////
$pdf->Ln();
$pdf->SetFont('Helvetica','B', 12);
$pdf->Image('../img/logo.jpg', 35, 129, 23, 23, 'jpg'); /// LOGO SIC
/////   RECUADRO DERECHO  FECHA  //////
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetY($pdf->GetY()+2);
$pdf->SetX(120);
$pdf->Cell(70,4,utf8_decode(' '),1,0,'C',1);
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(120);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(35,8,utf8_decode('Fecha Impresión:'),1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(155);
$pdf->Cell(35,8,utf8_decode(date_format(new \DateTime(date('Y-m-d H:i')), "d/m/Y    H:i")),1,0,'C');
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(120);
$pdf->SetFont('Helvetica','B', 12);
$pdf->Cell(35,8,utf8_decode('RECIBO N°:'),1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(155);
$pdf->SetTextColor(255, 0, 0);
$folio = substr(str_repeat(0, 5).$id, - 6);
$pdf->Cell(35,8,$folio,1,0,'C');
$pdf->Ln();
/////   RECAUADRO AZUL DEL CENTRO   ////////
$pdf->SetY($pdf->GetY()+2);
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->MultiCell(0,6,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n"),0,'C',1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetY($pdf->GetY());
$pdf->MultiCell(0,5,utf8_decode('Tels: 433-93-562-86 y 433-93-562-88'),0,'C',1);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->MultiCell(0,6,utf8_decode('GABRIEL VALLES REYES                                                                                         RFC: VARG7511217E5'),0,'C',1);

////   TITULO ANTES DE CONTENIDO  ///////
$pdf->SetTextColor(28, 98, 163);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 13);
$pdf->MultiCell(0,9,utf8_decode('RECIBO DE PAGO'),1,'C',0);
$pdf->SetY($pdf->GetY()+1);
$pdf->SetX(128);
$pdf->SetTextColor(0, 0, 0);

/// CANTIDAD ////
$pdf->Cell(15,7,utf8_decode('POR:'),0,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(147);
$pdf->SetFont('Courier','B', 13);
$pdf->Cell(56,6,'$'.sprintf('%.2f',$pago['cantidad']),1,0,'C');
$pdf->Ln();

/// TABLA O CONTENIDO ///
$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->SetX(17);
$pdf->MultiCell(55,7,utf8_decode("\n".'    RECIBO DEL SR.(A):'."\n".'    POR CONCEPTO DE: '."\n".' '),1,'L',0);
$pdf->SetY($pdf->GetY()-28);
$pdf->SetX(72);
$pdf->SetFont('Courier','', 11);
$pdf->MultiCell(131,7,utf8_decode("\n".$central['nombre'].'    LA CANTIDAD DE: $'.sprintf('%.2f',$pago['cantidad'])."\n".'RENTA CORRESPONDIENTE ('.$pago['tipo'].') '.$pago['descripcion']."\n".' '),1,'L',0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetY($pdf->GetY()-27.8);
$pdf->SetX(70);
$pdf->Cell(3,27.5,utf8_decode(' '),0,0,'C',1);
$pdf->Ln();
$pdf->SetY($pdf->GetY()-18);
$pdf->SetX(70);
$pdf->Cell(130,7,'........................................................',0,0,'C');
$pdf->SetY($pdf->GetY()+7);
$pdf->SetX(70);
$pdf->Cell(130,7,'........................................................',0,0,'C');

///   FIRMAS  ////////
$pdf->Ln();
$pdf->SetY($pdf->GetY()+10);
$pdf->SetX(55);
$pdf->Cell(130,7,'__________________________           __________________________',0,0,'C');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(55);
$pdf->Cell(130,7,utf8_decode('    RECIBÍ CONFORME                                                   ENTREGUÉ CONFORME'),0,0,'C');

//// PIE DEL RECIBO /////
$pdf->Ln();
$pdf->SetY($pdf->GetY()+1);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(28, 98, 163);
$pdf->MultiCell(0,9,utf8_decode('Estamos ubicados en:                                                             Av. Hidalgo No. 508 C. P. 99100, Sombrerete, Zac.'),0,'C',1);
//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>