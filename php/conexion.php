<?php
	/* Declaración de variables*/
	$serverName = "localhost";
	$userName = "sicsomco_root";
	$password = "sicsomco_root";
	$databaseName = "bds";

	/* Flag para reporte de errores*/
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	/*Se utiliza un try-catch para mejor limpieza de la instrucción, se utiliza el charset utf-8 para poder 
	utilizar caracteres propios del español*/
	try {
  		$conn = new mysqli($serverName, $userName, $password, $databaseName);
  		$conn->set_charset("utf8");
	} catch(Exception $e) {
  		error_log($e->getMessage());
  		exit('Error de conexión con la base de datos');
	}
?>
