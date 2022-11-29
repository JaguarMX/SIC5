<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>HOLA Y ADIOS MI VIDA HERMOSA</h1>
</body>
    <?php
        include('../php/conexion.php');
        include('../php/cobrador.php');
        date_default_timezone_set('America/Mexico_City');
        $Fecha_hoy = date('Y-m-d');

        $sql = "SELECT * FROM reporte_sicflix";
        $consulta = mysqli_query($conn, $sql);
        //Obtiene la cantidad de filas que hay en la consulta
         
        //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
        while($resultados = mysqli_fetch_array($consulta)) {
            $id_cliente = $resultados['cliente'];
            //EL COLOR DEPENDE DEL ESTATUS DEL REPORTE
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
            $reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE cliente=$id_cliente"));
        }
    ?>
  <!-- REDIRECCIONAMOS PRIMERAMENTE AL ARCHIVO PARA COMPROBAR LA FECHA DE CORTE SICFLIX -->
  <meta http-equiv="refresh" content="0;url=../views/reportes_sicflix.php">
</html>