<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
include("../fpdf/fpdf.php");
#------------------------------------------------------------------------------------
#HAY QUE AGREGAR EL HISTORIAL DE CORTES AL MENSAJE CON LOS CORTES DIARIOS
#------------------------------------------------------------------------------------
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
#SELECCIONAMOS TODOS LOS CORTES REALIZADOS CON LA FECHA DE HOY
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
class PDF extends FPDF{

}

$pdf = new PDF('P', 'mm', array(80,400));
$pdf->setTitle(utf8_decode('SIC | CORTE DE CORTES '));// TITULO BARRA NAVEGACION
$pdf->AddPage();
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(6);
$pdf->SetFont('Helvetica','B', 10);
$pdf->MultiCell(69,4,utf8_decode('====== CORTE DE CORTES ====='),0,'C',0);  
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(6);
$pdf->SetFont('Helvetica','B', 10);
$pdf->MultiCell(69,4,utf8_decode('FECHA:'. " ". $Fecha_hoy),0,'C',0); 
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(6);
$pdf->SetFont('Helvetica','B', 10);
$pdf->MultiCell(69,4,utf8_decode('PERÍODO:'. " ".$ValorDe. " ". "AL". " " .$ValorA ),0,'C',0); 
$pdf->SetX(6);
$pdf->SetFont('Helvetica','', 10);
$pdf->MultiCell(69,4,utf8_decode('**************************************'),0,'C',0);    

$sql_cortes = mysqli_query($conn, "SELECT * FROM cortes WHERE fecha>='$ValorDe' 
AND fecha<='$ValorA' ORDER BY fecha ASC");
#VERIFICAMOS SI SE ENCONTRARON CORTES
if(mysqli_num_rows($sql_cortes) > 0){
    $Total_Credito = 0;
    $Total_Banco = 0;
    $Total_Efectivo = 0;
    $TotalDeducible = 0;
    #SI SE ENCONTRARON CORTES SE RECORE UNO POR UNO...
    while($Corte = mysqli_fetch_array($sql_cortes)){      
      $id_user = $Corte['usuario'];
      $corte = $Corte['id_corte'];
      $fechaCorte = $Corte['fecha'];
      $horaCorte = $Corte['hora'];
      #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
      $cobrador = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
      #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
      $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
      if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $Deducir = $Deducible['cantidad'];
      }else{
        $Deducir = 0;
      }
      $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $corte");
      if (mysqli_num_rows($sql_deuda) > 0) {
        $Deuda = mysqli_fetch_array($sql_deuda);
        $Adeudo = $Deuda['cantidad'];
      }else{
        $Adeudo = 0;
      }
      #GUARDAMOS LOS TOTALES DE CADA TIPO DE PAGO EN UNA RESPETIVA VARIABLE         
      $Efectivo = $Corte['cantidad']-$Deducir-$Adeudo;
      $Banco = $Corte['banco'];
      $Credito = $Corte['credito'];

      
      #SUMAMOS LOS SUBTOTALES AL TOTAL
      $Total_Credito += $Credito;
      $Total_Banco += $Banco;
      $Total_Efectivo += $Efectivo;
      $TotalDeducible += $Deducir;

    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $folio = substr(str_repeat(0, 5).$corte, - 6);
    $pdf->MultiCell(69,4,utf8_decode(date_format(new \DateTime($Corte['fecha'].' '.$Corte['hora']), "d/m/Y H:i" )),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(69,4,utf8_decode('REALIZÓ: '.$cobrador['firstname']."\n".'RECIBIÓ: '.$Corte['realizo']),0,'C',0); 
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(35,4,utf8_decode('EN EFECTIVO'."\n".'DEDUCIBLE'."\n".'A BANCO'."\n".'A CREDITO'),0,'L',0);    
    $pdf->SetY($pdf->GetY()-16);
    $pdf->SetX(41);
    $pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Corte['cantidad'])."\n".'-$'.sprintf('%.2f', $Deducir)."\n".'$'.sprintf('%.2f', $Corte['banco'])."\n".'($'.sprintf('%.2f', $Corte['credito']).')'),0,'R',0);
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 10);
    $pdf->MultiCell(69,4,utf8_decode('==============================='),0,'C',0);  
    
  }// FIN DEL WHILE
  
}// FIN DEL IF CORTES

// SACAMOS EL RESUMEN DE LA CAJA CHICA DIARIO
#-----------------------------------------------------------------
# AGREGAMOS EL RESUMEN DE LA CAJA CHICA DE CORTES AL MENSAJE 
#-----------------------------------------------------------------
#INICIAMOS A CREAR LA CABECERA DEL RESUMEN CAJA

#SELECCIONAMOS TODOS LOS CORRTES REALIZADOS CON LA FECHA DE HOY
$sql_caja = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE fecha>='$ValorDe' 
AND fecha<='$ValorA' ORDER BY fecha ASC");
#VERIFICAMOS SI SE ENCONTRARON REGISTROS
if(mysqli_num_rows($sql_caja) > 0){
  $Total_Ingresos = 0;
  $Total_Egresos = 0;
  $ingresos = '';
  $egresos = '';
  #SI SE ENCONTRARON REGISTROS EN CAJA SE RECORE UNO POR UNO...
  while($registro = mysqli_fetch_array($sql_caja)){  
    $Tipo = $registro['tipo'];
    $id_user = $registro['usuario'];
    $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
    if ($Tipo == 'Ingreso') {
      $ingresos .= " ** Usuario: ".$user['user_name'].", Cantidad: $".$registro['cantidad']."<br>";
      $Total_Ingresos += $registro['cantidad'];
    }else{
      $egresos .= " ** Usuario: ".$user['user_name'].", Cantidad: $".$registro['cantidad']."<br>
                     .... Descripcion:".$registro['descripcion']."<br>";
      $Total_Egresos += $registro['cantidad'];
    }
  }// FIN WHLE
  $Total_Caja = $Total_Ingresos-$Total_Egresos;
  $pdf->SetY($pdf->GetY()+3);
  $pdf->SetX(6);
  $pdf->SetFont('Helvetica','B', 10);
  $pdf->MultiCell(69,4,utf8_decode('========  CAJA CHICA  ======='),0,'C',0); 
  $pdf->SetY($pdf->GetY()+3);
  $pdf->SetX(6);
  $pdf->SetFont('Helvetica','', 10);
  $pdf->MultiCell(35,4,utf8_decode('INGRESOS'."\n".'EGRESOS'."\n".'TOTAL'."\n"."\n"),0,'L',0);    
  $pdf->SetY($pdf->GetY()-16);
  $pdf->SetX(41);
  $pdf->MultiCell(35,4,utf8_decode('$'.sprintf('%.2f', $Total_Ingresos)."\n".'-$'.sprintf('%.2f', $Total_Egresos)."\n".
  '$'.sprintf('%.2f', $Total_Caja)."\n"),0,'R',0);

}else{// FIN IF CAJA
  $pdf->SetY($pdf->GetY()+3);
  $pdf->SetX(6);
  $pdf->SetFont('Helvetica','B', 10);
  $pdf->MultiCell(69,4,utf8_decode('========  CAJA CHICA  ======='),0,'C',0); 
  $pdf->SetY($pdf->GetY()+3);
  $pdf->SetX(6);
  $pdf->SetFont('Helvetica','', 10);
  $pdf->MultiCell(69,4,utf8_decode('== SIN MOVIMIENTOS =='),0,'C',0); 
}

$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(6);
$pdf->SetFont('Helvetica','B', 10);
$pdf->MultiCell(69,4,utf8_decode('========  TOTALES  ======='),0,'C',0); 
$pdf->SetY($pdf->GetY()+3);
$pdf->SetX(6);
$pdf->SetFont('Helvetica','B', 10);
$pdf->MultiCell(35,4,utf8_decode('EN EFECTIVO'."\n".'DEDUCIBLE'."\n".'A BANCO'."\n".'A CREDITO'),0,'L',0);    
$pdf->SetY($pdf->GetY()-16);
$pdf->SetX(41);
$pdf->MultiCell(34,4,utf8_decode('$'.sprintf('%.2f', $Total_Efectivo)."\n".'-$'.sprintf('%.2f', $TotalDeducible)."\n".'$'.sprintf('%.2f', $Total_Banco)."\n".'($'.sprintf('%.2f', $Total_Credito).')'),0,'R',0);

header('Content-type: application/pdf');
$pdf->Output('F','../files/cortes/cortedeCortes.pdf');
