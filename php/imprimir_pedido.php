<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
$folio = $_GET['folio'];
/// SACAMOS LA INFORMACION DEL PEDIDO Y LA LISTA DE LOS MATERIALES DEL PEDIDO (DETALLES)
$Pedido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pedidos WHERE folio = $folio"));
$detalles_pedido = mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio");

// VEMOS EL ESTADO DEL PEDIDO 
if ($Pedido['cerrado'] == 0 AND $Pedido['estatus'] == 'No Autorizado') {
   $Estatus = 'Pendiente';
}else if ($Pedido['cerrado'] == 1 AND $Pedido['estatus'] == 'No Autorizado') {
   $Estatus = 'Cerrado';
}else{
   $Estatus = $Pedido['estatus'];
}

class PDF extends FPDF{
   //Cabecera de página
   function Header(){ 
	   $this->SetFont('Arial','B', 12);
	   $this->Image('../img/logo_ticket.jpg', 185, 8, 20, 20, 'jpg');
		$this->SetY($this->GetY()-20);
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
$pdf->SetMargins(15, 35, 10);
$pdf->SetAutoPageBreak(true, 35);
$pdf->AliasNbPages();
$pdf->AddPage('portrait', 'letter');
$pdf->SetMargins(15, 45, 10);

$pdf->setTitle(utf8_decode('SIC | PEDIDO N° 000').$folio);
/////   RECUADRO IZQUIERDO  ///////
$pdf->SetTextColor(0,0,0);
$pdf->Ln(10);
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(14);
$pdf->SetFont('Arial', 'B', 11);
$CONTENIDO_1 ='Nombre: '.$Pedido['nombre'].''."\n".'Orden: '.$Pedido['id_orden'].''."\n".'Estatus : '.$Estatus;
$pdf->MultiCell(85,10,utf8_decode($CONTENIDO_1),1,'L',0);

/////   RECUADRO DERECHO    //////
$pdf->SetY($pdf->GetY()-30);
$pdf->SetX(104);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(90,5,utf8_decode('Pedido:'),0,0,'C');
$pdf->SetDrawColor(30, 40, 125);
$pdf->SetLineWidth(2);
$pdf->Line(105,$pdf->GetY()+6, 198, $pdf->GetY()+6);
$pdf->SetY($pdf->GetY()+10);
$pdf->SetX(104);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20,5,utf8_decode('Fecha Creación:').$Pedido['fecha'],0,0,'');
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(104);
$pdf->Cell(20,5,utf8_decode('Hora Creación:').$Pedido['hora'],0,0,'');
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(104);
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 40, 125);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0);
$pdf->Cell(94,8,utf8_decode('Pedido Número: 000').$folio,1,0,'');
$pdf->Ln(10);

//////   RECUADRO CENTRO   //////
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial', '', 8);
$CONTENIDO_1 ='Fecha Cerrado: '.$Pedido['fecha_cerrado'].''."\n".'Fecha Autorizado: '.$Pedido['fecha_autorizado'].''."\n".'Fecha Completado : '.$Pedido['fecha_completo'];
$pdf->SetY($pdf->GetY()+1);
$pdf->SetX(50);
$pdf->MultiCell(110,4,utf8_decode($CONTENIDO_1),1,'L',0);

$pdf->SetY($pdf->GetY()+6);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(180,10,utf8_decode('MATERIAL: '),0,0,'C');
$pdf->Ln();

/////   TABLA A MOSTRAR    //////
$pdf->Cell(8,10,utf8_decode('N°'),0,0,'C');
$pdf->Cell(112,10,utf8_decode('Descripción'),0,0,'C');
$pdf->Cell(25,10,utf8_decode('Proveedor'),0,0,'C');
$pdf->Cell(45,10,utf8_decode('Observación'),0,0,'C');
$pdf->SetDrawColor(30, 40, 125);
$pdf->SetLineWidth(1);
$pdf->Line(15,$pdf->GetY()+9, 200, $pdf->GetY()+9);
////   CONTENIDO DE LA TABLA    /////
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln();
$aux = 1;

while($material = mysqli_fetch_array($detalles_pedido)){ 
	$material['proveedor'] =(strlen ($material['proveedor'])>22)?'Link':$material['proveedor'];
	if (strlen ($material['proveedor'])>10 OR strlen ($material['descripcion'])>60 OR strlen ($material['observacion'])>17) {
		// Doble columna
		$Y = 12;	$extra = ''."\n".' ';
	}else{
		// SENCILLA
		$Y =6; $extra = '';
	}
	$pdf->SetX(15);
   $pdf->MultiCell(8,6,utf8_decode($aux.$extra),1,'C',1);
   $pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(23);
	$pdf->MultiCell(112,6,utf8_decode((strlen ($material['descripcion'])>60)?$material['descripcion']:$material['descripcion'].$extra),1,'C',1);
	$pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(135);
	$pdf->MultiCell(25,6,utf8_decode((strlen ($material['proveedor'])>10)?$material['proveedor']:$material['proveedor'].$extra),1,'C',1);
	$pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(160);
	$pdf->MultiCell(40,6,utf8_decode((strlen ($material['observacion'])>17)?$material['observacion']:$material['observacion'].$extra),1,'C',1);

	$aux ++;
}

//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>