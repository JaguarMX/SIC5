<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");


class PDF extends FPDF{
   //Cabecera de página
   function Header(){ 
	   $this->SetFont('Arial','B', 12);
	   $this->Image('../img/logo_ticket.jpg', 185, 8, 20, 20, 'jpg');
	   $this->Cell(0,5,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'),0,0,'C');
	   $this->Ln(8);
	   $this->SetTextColor(40, 40, 135);
	   $this->Cell(0,5,utf8_decode('"Tecnología y comunicación a tu alcance"'),0,0,'C');
   }

   //Pie de pagina 
   function footer(){
	   $this->SetFont('Arial','', 10);
	   $this->SetY(-33);
	   $this->Write(5, 'facebook.com/SIC.SOMBRERETE');
	   $this->Ln();
	   $this->Write(5, 'www.sicsom.com');
	   $this->SetY(-33);
	   $this->SetX(-60);
	   $this->Write(5, 'Avenida Hidalgo No. 508');
	   $this->SetY(-28);
	   $this->SetX(-69);
	   $this->Write(5, 'C.P. 99100    Sombrerete, Zac.');
	   $this->SetY(-23);
	   $this->SetX(-79);
	   $this->Write(5, 'Tels. 433 9 35 62 86 y 433 935 62 88');
	   $this->SetY(-12);
	   $this->SetX(-30);
	   $this->AliasNbPages('tpagina');
	   $this->Write(5, $this->PageNo().'/tpagina');
   }
}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','letter', true);
$pdf->AddPage('portrait', 'letter');
$pdf->setTitle('SIC | ESTADO DE CUENTA');
$pdf->SetFont('Arial', 'BU', 12);
$pdf->SetY(30);
$pdf->SetTextColor(16,87,97);
$pdf->Cell(0,5,'TITULO DEL CONTENIDO',0,0,'C');
$pdf->SetTextColor(0,0,0);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$TEXTO ='
Nombre: SIC-FREDY
Orden: N/A
Estatus : Autorizado';
$pdf->Multiell(20,5,utf8_decode('N°'),1,0,'C');

$pdf->Cell(20,5,utf8_decode('N°'),1,0,'C');
//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>