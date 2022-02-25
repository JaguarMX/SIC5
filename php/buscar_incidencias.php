<?php
include('../php/conexion.php');

$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);

#CONTAMOS EL TOTAL DE LAS INCIDENCIAS 
$N_Total_Inc = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM incidencias WHERE fecha_td >= '$ValorDe' AND fecha_td <= '$ValorA'"));
$N_Total_Inc_R = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM incidencias WHERE fecha_td >= '$ValorDe' AND fecha_td <= '$ValorA' AND estatus = 1 AND observacion != 'Mantenimiento'")) ;
$N_Total_Mto = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM incidencias WHERE fecha_td >= '$ValorDe' AND fecha_td <= '$ValorA' AND estatus = 1 AND observacion = 'Mantenimiento'"));
$Tiempo_deteccion= 0;
if ($N_Total_Inc['count(*)'] > 0) {
  $sql =mysqli_query($conn,"SELECT * FROM incidencias WHERE  fecha_td >= '$ValorDe' AND fecha_td <= '$ValorA'");
  while($incidencia = mysqli_fetch_array($sql)){
    # TOMAMOS LAS FECHAS DE LA INCIDENCIA EN TURNO
    $date1 = new DateTime($incidencia['fecha_td'].' '.$incidencia['hora_td']); // FECHA TD
    $date2 = new DateTime($incidencia['fecha_to'].' '.$incidencia['hora_to']);// FECHA TO
    #CALCULAMOS LA DIFERENCIA ENTRE LAS DOS FECHAS DE LA INCIDENCIA EN TURNO
    $diff = $date1->diff($date2);
    ##CONVERTIMOS LA DIFERENCIA A HORAS
    #echo  $incidencia['fecha_td'].' '.$incidencia['hora_td'].' TD<br>';
    #echo  $incidencia['fecha_to'].' '.$incidencia['hora_to']. ' TO <br>';
    #echo  ($diff->days * 24 ) +  ( $diff->h ) + ( $diff->i / 60 ) . ' Horas <br>';
    $TIME_D =($diff->days * 24 ) +  ( $diff->h ) + ( $diff->i / 60 );
    $Tiempo_deteccion += $TIME_D;
  }
}
$Tiempo_solucion= 0;
if ($N_Total_Inc_R['count(*)'] > 0) {
  $sql_R =mysqli_query($conn,"SELECT * FROM incidencias WHERE fecha_td >= '$ValorDe' AND fecha_td <= '$ValorA' AND estatus = 1 AND observacion != 'Mantenimiento'");
  while($incidencia_R = mysqli_fetch_array($sql_R)){
    # TOMAMOS LAS FECHAS DE LA INCIDENCIA EN TURNO
    $date1 = new DateTime($incidencia_R['fecha_ts'].' '.$incidencia_R['hora_ts']); // FECHA TD
    $date2 = new DateTime($incidencia_R['fecha_td'].' '.$incidencia_R['hora_td']);// FECHA TO
    #CALCULAMOS LA DIFERENCIA ENTRE LAS DOS FECHAS DE LA INCIDENCIA EN TURNO
    $diff = $date1->diff($date2);
    ##CONVERTIMOS LA DIFERENCIA A HORAS
    $TIME_S =($diff->days * 24 ) +  ( $diff->h ) + ( $diff->i / 60 );
    $Tiempo_solucion += $TIME_S;
  }
}
#((Tiempo total = 24*'Días seleccionados')) que es el 100%  ((Falla = Tiempo de solución*100 '%' / Tiempo total))  entonces ((MTBF = 100 '%' - Falla))
#CALCULAMOS LOS DIAS SELECCIONADOS EN EL RANGO DE FECHAS
$date1 = new DateTime($ValorA);
$date2 = new DateTime($ValorDe);
$diff = $date1->diff($date2);
#LO CONVERTIMOS EN HORAS PARA EL TIMEPO TOTAL
$Tiempo_total = 24*($diff->days+1);// 100%
#Hacemos una regla de 3 para calcular EL TIEMPO DE FALLA DEL SERVICIO EN %
$Falla = ($Tiempo_solucion*100)/$Tiempo_total;
#CALCULAMOS MTBF EFICIENCIA
$MTBF = 100-$Falla; // EN %
if ($N_Total_Inc['count(*)'] == 0) {
  echo '<br><hr><h4 class="red-text center"> No se encontraron incidencia en este rango de fechas</h4></h5><hr>';
}else{
?>
  <h3 class="center">Estadisticas: </h3><br><hr>
  <h5 class="indigo-text">Total de Incidencias = <?php echo $N_Total_Inc['count(*)']; ?></h5>
  <h5 class="indigo-text">Total de Incidencias Resueltas = <?php echo $N_Total_Inc_R['count(*)']; ?></h5>
  <h5 class="indigo-text">Total de Incidencias a Mantenimientos = <?php echo $N_Total_Mto['count(*)']; ?></h5><hr>

  <h5 class="indigo-text">Tiempo de deteccion (TD-TO) = <?php echo $Tiempo_deteccion; ?> HORAS</h5>
  <h5 class="indigo-text">Tiempo de solucion (TS-TD) = <?php echo $Tiempo_solucion; ?> HORAS</h5><hr>

  <h5 class="indigo-text">MTTD = <?php echo $Tiempo_deteccion/$N_Total_Inc['count(*)']; ?> HORAS</h5>
  <h5 class="indigo-text">MTTR = <?php echo $Tiempo_solucion/$N_Total_Inc_R['count(*)']; ?> HORAS</h5><hr>

  <h4 class="green-text center">MTBF = <?php echo $MTBF; ?> %</h4><hr><br>
<?php
}
?>