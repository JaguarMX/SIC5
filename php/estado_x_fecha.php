<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
//RECIBIMOS EL RANGO DE FECHAS Y EL ID DEL CLIENTE MEDIANTE MTODO POST
$valorID = $_GET['valorID'];
$ValorDe = $_GET['valorDe'];
$ValorA = $_GET['valorA'];

/// SACAMOS LA INFORMACION DEL CLIENTE Y LA LISTA CON EL RANGO DE FECHA DE LOS PAGOS (DETALLES DEUDAS Y ABONOS)
$cliente =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = '$valorID'"));
$deudas = mysqli_query($conn, "SELECT * FROM deudas WHERE fecha_deuda>='$ValorDe' AND fecha_deuda<='$ValorA' AND id_cliente = $valorID");
$abonos = mysqli_query($conn, "SELECT * FROM pagos WHERE  fecha>='$ValorDe' AND fecha<='$ValorA' AND id_cliente = $valorID AND tipo = 'Abono'");

class PDF extends FPDF{
   //Cabecera de página
   function Header(){ 
	   $this->SetFont('Arial','B', 12);
	   $this->Image('../img/logo.jpg', 185, 8, 20, 20, 'jpg');
		$this->SetY($this->GetY()-20);
	   $this->Cell(0,5,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'),0,0,'C');
	   $this->Ln(8);
	   $this->SetTextColor(40, 40, 135);
	   $this->Cell(0,5,utf8_decode('"Tecnología y comunicación a tu alcance"'),0,0,'C');
   }

    //Pie de pagina 
   function footer(){
	   $this->SetFont('Helvetica','', 10);
	   $this->SetFillColor(28, 98, 163);
		$this->SetDrawColor(28, 98, 163);
		$this->SetTextColor(255, 255, 255);
	   $this->SetY(-35);
		$this->SetX(0);
	   $this->SetFont('Helvetica', 'B', 13);
		$this->MultiCell(216,10,utf8_decode('    Siguenos en:                                                                                              Estamos ubicados en:'),0,'C',1);
		$this->SetX(0);
	   $this->MultiCell(15,15,utf8_decode(' '."\n".' '),1,'C',1);
	   $this->SetY(-25);
		$this->SetX(15);
	   $this->SetFont('Helvetica', '', 10);
		$this->Image('../img/icon-facebook.png', 5, 253, 9, 9, 'png'); /// LOGO FACEBOOK
		$this->Image('../img/icon-tiktok.png', 5, 261, 9, 9, 'png'); /// LOGO TIKTOK
		$this->Image('../img/icon-pagina.png', 5, 269, 9, 9, 'png'); /// LOGO PAGINA
	   $this->MultiCell(145,8,utf8_decode('Servicios Integrales De Computacion Sic'."\n".'sic.serviciosintegrales'."\n".'www.sicsom.com/ventas'."\n".' '),1,'L',1);
	   $this->SetY(-25);
	   $this->SetX(160);
	   $this->MultiCell(56,6,utf8_decode('Av. Hidalgo No. 508 C. P. 99100, Sombrerete, Zac.'."\n".' '),1,'L',1);
	   $this->SetY(-10);
	   $this->SetX(160);
	   $this->AliasNbPages('tpagina');
	   $this->Cell(56,10,utf8_decode($this->PageNo().'/tpagina'),1,0,'R',1);
   }
}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','letter', true);
$pdf->SetMargins(15, 35, 10);
$pdf->SetAutoPageBreak(true, 35);
$pdf->AliasNbPages();
$pdf->AddPage('portrait', 'letter');

$pdf->setTitle(utf8_decode('SIC | Estado Cuenta Cliente: ').$valorID);

$pdf->SetY($pdf->GetY()+15);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(185,10,utf8_decode('ESTADO DE CUENTA'),0,0,'C');
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetLineWidth(2);
$pdf->Line(60,$pdf->GetY()+9, 150, $pdf->GetY()+9);

/////   RECUADRO IZQUIERDO  ///////
$pdf->SetLineWidth(0);
$pdf->SetTextColor(0,0,0);
$pdf->Ln(10);
$pdf->SetY($pdf->GetY()+16);
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 11);
$CONTENIDO_1 ='N° Cliente: '.$valorID."\n".'Nombre    : '.$cliente['nombre'];
$pdf->MultiCell(90,8,utf8_decode($CONTENIDO_1),0,'L',0);

/////   RECUADRO DERECHO    //////
$pdf->SetFillColor(200, 200, 200);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetY($pdf->GetY()-18);
$pdf->SetX(130);
$pdf->Cell(30,8,utf8_decode('Desde'),1,0,'C',1);
$pdf->SetY($pdf->GetY());
$pdf->SetX(160);
$pdf->Cell(30,8,utf8_decode('Hasta'),1,0,'C',1);
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(130);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30,8,$ValorDe,1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(160);
$pdf->Cell(30,8,$ValorA,1,0,'C');
$pdf->Ln();

////   TITULO ANTES DE TABLA  ///////
$pdf->SetY($pdf->GetY()+15);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(180,10,utf8_decode('Historial: '),0,0,'C');
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetLineWidth(1);
$pdf->Line(83,$pdf->GetY()+8, 123, $pdf->GetY()+8);
$pdf->Ln(12);

/////   TABLA A MOSTRAR    //////
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(11,10,utf8_decode('Folio'),0,0,'C');
$pdf->Cell(20,10,utf8_decode('Fecha'),0,0,'C');
$pdf->Cell(72,10,utf8_decode('Concepto'),0,0,'C');
$pdf->Cell(19,10,utf8_decode('Realizo'),0,0,'C');
$pdf->Cell(18,10,utf8_decode('S. Anterior'),0,0,'C');
$pdf->Cell(15,10,utf8_decode('Cargo'),0,0,'C');
$pdf->Cell(16,10,utf8_decode('Abono'),0,0,'C');
$pdf->Cell(15,10,utf8_decode('S. Nuevo'),0,0,'C');
$pdf->Line(15,$pdf->GetY()+9, 200, $pdf->GetY()+9);
////   CONTENIDO DE LA TABLA    /////
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(233, 233, 233);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln();
$aux = 1;

if (mysqli_num_rows($deudas) == 0 AND mysqli_num_rows($abonos) == 0) {
   $pdf->SetTextColor(255, 0, 0);
	$pdf->Cell(185,10,utf8_decode('NO SE ENCONTRARON REGISTROS'),0,0,'C');
}else{
	$SaldoAnterior = 0;
	$SaldoNuevo = 0;
	$aux = mysqli_num_rows($deudas);
	if ($aux > 0) {
		$iniciar = 0;
		while ($deuda = mysqli_fetch_array($deudas)){
			$aux --;
			//Sacar fecha de la deuda
			$Fecha_Deduda = $deuda['fecha_deuda'];
			//BUSCAMOS ABONOS ANTERIORES A LA FECHA DE LA DEUDA
			$abonos = mysqli_query($conn, "SELECT * FROM pagos WHERE (fecha>='$ValorDe' AND fecha<='$ValorA') AND fecha < '$Fecha_Deduda' AND id_cliente = $valorID AND tipo = 'Abono' LIMIT $iniciar, 100");
			if (mysqli_num_rows($abonos) > 0) {
				$iniciar = $iniciar+mysqli_num_rows($abonos);//SI ENCUENTRA ABONOS A $iniciar LO INCREMENTAMOS LA FILAS QUE SE ENCONTRARON
				while($abono = mysqli_fetch_array($abonos)){
					/////   IMPRIMIR EL ABONO EN TURNO  YA QUE NO HAY DEUDAS   /////
					$id_user = $abono['id_user'];
					$user = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
					$SaldoAnterior = $SaldoNuevo;
					$SaldoNuevo -= $abono['cantidad'];
					$pdf->SetX(14);
					$pdf->Cell(13,7,utf8_decode($abono['id_pago']),1,0,'C',1);
					$pdf->Cell(20,7,utf8_decode($abono['fecha']),1,0,'C',1);
					$pdf->Cell(70,7,utf8_decode($abono['descripcion']),1,0,'L',1);
					$pdf->Cell(20,7,utf8_decode($user['firstname']),1,0,'C',1);
					$pdf->Cell(18,7,utf8_decode('$').$SaldoAnterior,1,0,'R',1);
					$pdf->Cell(16,7,utf8_decode('$0.00'),1,0,'R',1);
					$pdf->Cell(15,7,utf8_decode('$'.$abono['cantidad']),1,0,'R',1);
					$pdf->Cell(15,7,utf8_decode('$').$SaldoNuevo,1,0,'R',1);
					$pdf->Ln();
				}
			}
			//////   IMPRIMIR LA DEUDA EN TURNO YA QUE NO HAY ABONOS   //////
			$id_user = $deuda['usuario'];
			$user_d = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
			$SaldoAnterior = $SaldoNuevo;
			$SaldoNuevo += $deuda['cantidad'];
			$pdf->SetX(14);
			$pdf->Cell(13,7,utf8_decode($deuda['id_deuda']),1,0,'C',1);
			$pdf->Cell(20,7,utf8_decode($deuda['fecha_deuda']),1,0,'C',1);
			$pdf->Cell(70,7,utf8_decode($deuda['descripcion']),1,0,'L',1);
			$pdf->Cell(20,7,utf8_decode($user_d['firstname']),1,0,'C',1);
			$pdf->Cell(18,7,utf8_decode('$ ').$SaldoAnterior,1,0,'R',1);
			$pdf->Cell(16,7,utf8_decode('$ '.$deuda['cantidad']),1,0,'R',1);
			$pdf->Cell(15,7,utf8_decode('$ 0.00'),1,0,'R',1);
			$pdf->Cell(15,7,utf8_decode('$').$SaldoNuevo,1,0,'R',1);
			$pdf->Ln();
			if ($aux == 0) {
				//BUSCAMOS ABONOS POSTERIORES O IGUAL A LA FECHA DE LA ULTIMA DEUDA
				$abonos = mysqli_query($conn, "SELECT * FROM pagos WHERE (fecha>='$ValorDe' AND fecha<='$ValorA') AND fecha >= '$Fecha_Deduda' AND id_cliente = $valorID AND tipo = 'Abono'");
				if (mysqli_num_rows($abonos) > 0) {
					while($abono = mysqli_fetch_array($abonos)){
						/////   IMPRIMIR EL ABONO EN TURNO  YA QUE NO HAY DEUDAS   /////
						$id_user = $abono['id_user'];
						$user = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
						$SaldoAnterior = $SaldoNuevo;
						$SaldoNuevo -= $abono['cantidad'];
						$pdf->SetX(14);
						$pdf->Cell(13,7,utf8_decode($abono['id_pago']),1,0,'C',1);
						$pdf->Cell(20,7,utf8_decode($abono['fecha']),1,0,'C',1);
						$pdf->Cell(70,7,utf8_decode($abono['descripcion']),1,0,'L',1);
						$pdf->Cell(20,7,utf8_decode($user['firstname']),1,0,'C',1);
						$pdf->Cell(18,7,utf8_decode('$ ').$SaldoAnterior,1,0,'R',1);
						$pdf->Cell(16,7,utf8_decode('$0.00'),1,0,'R',1);
						$pdf->Cell(15,7,utf8_decode('$'.$abono['cantidad']),1,0,'R',1);
						$pdf->Cell(15,7,utf8_decode('$').$SaldoNuevo,1,0,'R',1);
						$pdf->Ln();
					}
				}
			}
		}
	}else{
		//RECORRER CON WHILE TODOS LOS ABONOS
		while($abono = mysqli_fetch_array($abonos)){
			/////   IMPRIMIR EL ABONO EN TURNO  YA QUE NO HAY DEUDAS   /////
			$id_user = $abono['id_user'];
			$user = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
			$SaldoAnterior = $SaldoNuevo;
			$SaldoNuevo -= $abono['cantidad'];
			$pdf->SetX(14);
			$pdf->Cell(13,7,utf8_decode($abono['id_pago']),1,0,'C',1);
			$pdf->Cell(20,7,utf8_decode($abono['fecha']),1,0,'C',1);
			$pdf->Cell(70,7,utf8_decode($abono['descripcion']),1,0,'L',1);
			$pdf->Cell(20,7,utf8_decode($user['firstname']),1,0,'C',1);
			$pdf->Cell(18,7,utf8_decode('$ ').$SaldoAnterior,1,0,'R',1);
			$pdf->Cell(16,7,utf8_decode('$ 0.00'),1,0,'R',1);
			$pdf->Cell(15,7,utf8_decode('$ '.$abono['cantidad']),1,0,'R',1);
			$pdf->Cell(15,7,utf8_decode('$').$SaldoNuevo,1,0,'R',1);
			$pdf->Ln();
		}
	}
}


$pdf->SetTextColor(0, 0, 0);
//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>
