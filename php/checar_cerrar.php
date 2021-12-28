<?php
$Fecha = date('Y-m-d');//Fecha actual
echo 'Fecha: '.$Fecha;
$Dia_num = date('N', strtotime($Fecha));//COMBIERTE FECHA EN NUMERO : Domingo(7), Lunes(1), Martes(2), Miercoles(3), Jueves(4) ,Viernes(5) ,Sabado(6)

if($Dia_num == 5){
	$Cierra = date("d-m-Y",strtotime($Fecha."+ 4 days")); 
	echo '<br>Cerrar el martes (2)'.$Cierra;
}else{
	$Cierra = date("d-m-Y",strtotime($Fecha."+ 2 days")); 
	echo '<br> Cerrar dos dias despues '.$Cierra;
}
