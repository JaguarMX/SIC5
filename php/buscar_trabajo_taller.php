<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$valorUsuario = $conn->real_escape_string($_POST['valorUsuario']);
$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$valorUsuario'"));
$DIA  = $ValorDe;
$user=$usuario['user_name'];
$id_user = $usuario['user_id'];

$Dispositivos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM actividades_taller WHERE  fecha >= '$ValorDe' AND fecha <= '$ValorA' AND  tecnico = $id_user")); 

$Ordenes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios WHERE (fecha_s >= '$ValorDe' AND fecha_s <= '$ValorA' AND  tecnicos_s LIKE '%$user%') OR (fecha_r >= '$ValorDe' AND fecha_r <= '$ValorA' AND  tecnicos_r LIKE '%$user%') AND estatus != 'Cancelada'")); 

$Reportes_Oficina = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE (fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 0 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user' OR apoyomas LIKE '%$user%')) OR (fecha_d >= '$ValorDe' AND fecha_d <= '$ValorA' AND tecnico_d = '$id_user')"));

$Reportes_Campo = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 1 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user' OR apoyomas LIKE '%$user%')"));
?>
<br><br>
<h4>Trabajo realizadó por: <?php echo $usuario['firstname']; ?></h4>
<h5 class="indigo-text">Dispositivos (<?php echo $Dispositivos['count(*)']; ?>) <--> Reportes Ofician (<?php echo $Reportes_Oficina['count(*)']; ?>) <--> Reportes Campo (<?php echo $Reportes_Campo['count(*)']; ?>) <--> Ordenes (<?php echo $Ordenes['count(*)']; ?>)</h5>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>Folio</th>
        <th>Tipo</th>
        <th>Cliente</th>
        <th>Fecha Termino</th>
        <th>Hora Termino</th>
        <th>Trabajo</th>
        <th>Estatus</th>
        <th>Técnicos</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($DIA <= $ValorA) {
      #BUSCAMOS ACTIVIDADES
      $sql_actividades = mysqli_query($conn, "SELECT * FROM actividades_taller WHERE fecha ='$DIA' AND tecnico = '$valorUsuario' ORDER BY fecha, hora");
      $actividades_contador = mysqli_num_rows($sql_actividades);
      if(mysqli_num_rows($sql_actividades)>0){
        #VARIABLES INICIAR PARA LLEVAR UN ORDEN Y NO REPETIR
        $iniciar_R = 0;
        $iniciar_O = 0;
        #SI ENCONTRAMOS ACTIVIDADES LAS RECORREMOS UNA A UNA
        while($actividad = mysqli_fetch_array($sql_actividades)){
          $actividades_contador --;
          $Hora_A = $actividad['hora'];
          #BUSCAMOS REPORTES MENORES A LA HORA DE LA ACTIVIDAD EN TURNO
          $sql_reporte = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1 AND hora_atendido < '$Hora_A' AND (tecnico = '$id_user' OR apoyo = '$id_user' OR apoyomas LIKE '%$user%')) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user'  AND hora_atendido < '$Hora_A') ORDER BY hora_atendido LIMIT $iniciar_R, 100");
          if(mysqli_num_rows($sql_reporte) > 0){ 
            $iniciar_R = $iniciar_R+mysqli_num_rows($sql_reporte);
            #SI ENCONTRAMOS REPORTES LOS RECORREMOS UNO A UNO
            while ($reporte = mysqli_fetch_array($sql_reporte)) {
              $Hora_R = $reporte['hora_atendido'];
              #BUSCAMOS ORDENES MENORES A LA HORA DEL REPORTE EN TURNO
              $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$Hora_R' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$Hora_R' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_O, 20");
              if(mysqli_num_rows($sql_orden) > 0){ 
                $iniciar_O = $iniciar_O+mysqli_num_rows($sql_orden);
                #SI ENCONTRAMOS ORDENES LOS RECORREMOS UNO A UNO
                while($orden = mysqli_fetch_array($sql_orden)){
                  #+++++++++++++++ IMPRIMIR ORDEN ++++++++++++++++++++++
                  $id_cliente_o = $orden['id_cliente'];
                  $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  
                  $cadena_de_texto = $orden['tecnicos_r'];
                  $cadena_buscada   = $user;
                  $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($coincidencia === false){
                    $tecnicos = $orden['tecnicos_s'];
                    $fecha = $orden['fecha_s'];
                    $hora = $orden['hora_s'];
                  } else {
                    $tecnicos = $orden['tecnicos_r'];
                    $fecha = $orden['fecha_r'];
                    $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $orden['id']; ?></td>
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $cliente_o['nombre']; ?></td> 
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td>Realizada</td>
                    <td><?php echo $tecnicos ?></td>
                  </tr>
                <?php
                }//FIN WHILE ORDENES
              }//FIN IF ORDENES
              #****************** IMPRIMIR REPORTE ********************
              $id_cliente_r = $reporte['id_cliente'];            
              $sql_cliente_r = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente_r");
              if (mysqli_num_rows($sql_cliente_r) == 0) {
                $sql_cliente_r = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_r");
              }
              $cliente_r = mysqli_fetch_array($sql_cliente_r);
              #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE
              if ($reporte['fecha_d']==$DIA AND $reporte['tecnico_d'] == $id_user) {
                $id_d =$reporte['tecnico_d'];
                if ($id_d != '') {
                  $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
                }
                ?>
                <tr>
                  <td><?php echo $reporte['id_reporte']; ?></td>            
                  <td><b>Reporte</b></td>            
                  <td><?php echo $cliente_r['nombre']; ?></td>       
                  <td><?php echo $reporte['fecha_d']; ?></td>
                  <td><?php echo $reporte['hora_d']; ?></td>
                  <td><b>Reporte: </b> <?php echo $reporte['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte['falla']; ?>.</td>
                  <td>Diagnostico</td>
                  <td><?php echo $tecnico_d['firstname']; ?></td>
                </tr>
                <?php
              }//FIN IF ES DIAGNOSTICO REPORTE
              $mystring = $reporte['apoyomas'];
              $findme   = $user;
              $pos = strpos($mystring, $findme);
              // El operador !== también puede ser usado. Puesto que != no funcionará como se espera
              // porque la posición de 'a' es 0. La declaración (0 != false) se evalúa a 
              // false.
              #VEMOS SI ES UN TERMINO DE REPORTE (SOLUCION)
              if ($reporte['fecha_solucion']==$DIA AND $reporte['atendido'] == 1 AND ($reporte['tecnico'] == $id_user OR $reporte['apoyo'] == $id_user OR $pos !== false)) {
                $id_tec = $reporte['tecnico'];
                $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
                if ($reporte['apoyo'] != 0) {
                  $id_apoyo = $reporte['apoyo'];
                  $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
                  $Apoyo = ', Apoyo: '.$A['firstname'];
                }elseif($reporte['apoyomas'] != ''){
                  $Apoyo = ', Apoyo: '.$reporte['apoyomas'];
                }else{ $Apoyo = ''; }
                ?>
                <tr>
                  <td><?php echo $reporte['id_reporte']; ?></td>            
                  <td><b>Reporte</b></td>            
                  <td><?php echo $cliente_r['nombre']; ?></td>  
                  <td><?php echo $reporte['fecha_solucion']; ?></td>
                  <td><?php echo $reporte['hora_atendido']; ?></td>
                  <td><b>Reporte: </b> <?php echo $reporte['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte['falla']; ?>.</td>
                  <td><?php echo $reporte['solucion']; ?></td>
                  <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
                </tr>
                <?php
              }//FIN IF ES UNA SOLUCION REPORTE
            }//FIN WHILE REPORTES
          }//FIN IF REPORTES
          #BUSCAMOS ORDENES MENORES A LA HORA DE LA ACTIVIDAD EN TURNO
          $sql_orden2 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$Hora_A' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$Hora_A' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_O, 20");
          if(mysqli_num_rows($sql_orden2) > 0){
            $iniciar_O = $iniciar_O+mysqli_num_rows($sql_orden2);
            #SI ENCONTRAMOS ORDENES LOS RECORREMOS UNO A UNO
            while($orden2 = mysqli_fetch_array($sql_orden2)){
              #+++++++++++++++ IMPRIMIR ORDEN 2++++++++++++++++++++++
              $id_cliente_o = $orden2['id_cliente'];
              $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  
              $cadena_de_texto = $orden2['tecnicos_r'];
              $cadena_buscada   = $user;
              $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
              //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
              if ($coincidencia === false){
                $tecnicos = $orden2['tecnicos_s'];
                $fecha = $orden2['fecha_s'];
                $hora = $orden2['hora_s'];
              } else {
                $tecnicos = $orden2['tecnicos_r'];
                $fecha = $orden2['fecha_r'];
                $hora = $orden2['hora_r'];
              }
              ?>
              <tr>
                <td><?php echo $orden2['id']; ?></td>
                <td><b>Orden de Servicio</b></td>            
                <td><?php echo $cliente_o['nombre']; ?></td> 
                <td><?php echo $fecha ?></td>
                <td><?php echo $hora ?></td>
                <td><?php echo $orden2['trabajo'] ?></td>
                <td>Realizada</td>
                <td><?php echo $tecnicos ?></td>
              </tr>
              <?php
            }//FIN WHILE ORDENES2
          }//FIN IF ORDENES2
          #---------------------- IMPRIMIR ACTIVIDAD -----------------
          $id_dispositivio =  $actividad['dispositivo'];
          $dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$id_dispositivio'"));
          $disp = $dispositivo['tipo'].' '.$dispositivo['marca'];
          $id_tec_a = $actividad['tecnico'];
          $tecnico_a = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec_a"));
          ?>
          <tr> 
            <td><?php echo $id_dispositivio;?></td>
            <td><b>Dispositivo:</b> <?php echo $disp;?></td>
            <td><?php echo $dispositivo['nombre'];?></td>
            <td><?php echo $actividad['fecha'];?></td>
            <td><?php echo $actividad['hora'];?></td>
            <td><?php echo $dispositivo['observaciones'];?></td>
            <td><?php echo $actividad['accion'];?></td>
            <td><?php echo $tecnico_a['firstname']; ?></td>
          </tr>
          <?php
          if ($actividades_contador == 0) {//SI actividades_contador == 0 ES LA ULTIMA ACTIVIDAD
            #BUSACAR REPORTES MAYOR A LA HORA DE LA ULTIMA ACTIVIDAD  
            $sql_reportes2 = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1 AND hora_atendido > '$Hora_A' AND (tecnico = '$id_user' OR apoyo = '$id_user' OR apoyomas LIKE '%$user%')) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user' AND hora_atendido > '$Hora_A')  ORDER BY hora_atendido");
            $contador_reportes = mysqli_num_rows($sql_reportes2);
            if($contador_reportes > 0){ 
              while ($reporte2 = mysqli_fetch_array($sql_reportes2)) {
                $contador_reportes --;
                $Hora_R_M = $reporte2['hora_atendido'];
                #BUSCAMOS ORDENES MENORES A LA HORA DEL REPORTE EN TURNO
                $sql_orden3 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$Hora_R_M' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$Hora_R_M' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_O, 20");
                if(mysqli_num_rows($sql_orden3) > 0){ 
                  $iniciar_O = $iniciar_O+mysqli_num_rows($sql_orden3);
                  #SI ENCONTRAMOS ORDENES LOS RECORREMOS UNO A UNO
                  while($orden3 = mysqli_fetch_array($sql_orden3)){
                    #+++++++++++++++ IMPRIMIR ORDEN 3 ++++++++++++++++++++++
                    $id_cliente_o = $orden3['id_cliente'];
                    $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                    
                    $cadena_de_texto = $orden3['tecnicos_r'];
                    $cadena_buscada   = $user;
                    $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
                    //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                    if ($coincidencia === false){
                      $tecnicos = $orden3['tecnicos_s'];
                      $fecha = $orden3['fecha_s'];
                      $hora = $orden3['hora_s'];
                    } else {
                      $tecnicos = $orden3['tecnicos_r'];
                      $fecha = $orden3['fecha_r'];
                      $hora = $orden3['hora_r'];
                    }
                    ?>
                    <tr>
                      <td><?php echo $orden3['id']; ?></td>
                      <td><b>Orden de Servicio</b></td>            
                      <td><?php echo $cliente_o['nombre']; ?></td> 
                      <td><?php echo $fecha ?></td>
                      <td><?php echo $hora ?></td>
                      <td><?php echo $orden3['trabajo'] ?></td>
                      <td>Realizada</td>
                      <td><?php echo $tecnicos ?></td>
                    </tr>
                  <?php
                  }//FIN WHILE ORDENES 3
                }//FIN IF ORDENES 3
                #****************** IMPRIMIR REPORTE ********************
                $id_cliente_r = $reporte2['id_cliente'];            
                $sql_cliente_r = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente_r");
                if (mysqli_num_rows($sql_cliente_r) == 0) {
                  $sql_cliente_r = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_r");
                }
                $cliente_r = mysqli_fetch_array($sql_cliente_r);
                #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE
                if ($reporte2['fecha_d']==$DIA AND $reporte2['tecnico_d'] == $id_user) {
                  $id_d =$reporte2['tecnico_d'];
                  if ($id_d != '') {
                    $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
                  }
                  ?>
                  <tr>
                    <td><?php echo $reporte2['id_reporte']; ?></td>            
                    <td><b>Reporte</b></td>            
                    <td><?php echo $cliente_r['nombre']; ?></td>       
                    <td><?php echo $reporte2['fecha_d']; ?></td>
                    <td><?php echo $reporte2['hora_d']; ?></td>
                    <td><b>Reporte: </b> <?php echo $reporte2['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte2['falla']; ?>.</td>
                    <td>Diagnostico</td>
                    <td><?php echo $tecnico_d['firstname']; ?></td>
                  </tr>
                  <?php
                }//FIN IF ES DIAGNOSTICO REPORTE
                $mystring = $reporte2['apoyomas'];
                $findme   = $user;
                $pos = strpos($mystring, $findme);
                // El operador !== también puede ser usado. Puesto que != no funcionará como se espera
                // porque la posición de 'a' es 0. La declaración (0 != false) se evalúa a 
                // false.
                #VEMOS SI ES UN TERMINO DE REPORTE (SOLUCION)
                if ($reporte2['fecha_solucion']==$DIA AND $reporte2['atendido'] == 1 AND ($reporte2['tecnico'] == $id_user OR $reporte2['apoyo'] == $id_user OR $pos !== false)) {
                  $id_tec = $reporte2['tecnico'];
                  $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
                  if ($reporte2['apoyo'] != 0) {
                    $id_apoyo = $reporte2['apoyo'];
                    $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
                    $Apoyo = ', Apoyo: '.$A['firstname'];
                  }elseif($reporte2['apoyomas'] != ''){
                    $Apoyo = ', Apoyo: '.$reporte2['apoyomas'];
                  }else{ $Apoyo = ''; }
                  ?>
                  <tr>
                    <td><?php echo $reporte2['id_reporte']; ?></td>            
                    <td><b>Reporte</b></td>            
                    <td><?php echo $cliente_r['nombre']; ?></td>  
                    <td><?php echo $reporte['fecha_solucion']; ?></td>
                    <td><?php echo $reporte['hora_atendido']; ?></td>
                    <td><b>Reporte: </b> <?php echo $reporte2['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte2['falla']; ?>.</td>
                    <td><?php echo $reporte2['solucion']; ?></td>
                    <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
                  </tr>
                  <?php
                }//FIN IF ES UNA SOLUCION REPORTE
                if ($contador_reportes == 0) {//SI contador_reportes == 0 ES EL ULTIMO REPORTE
                  #BUSACAR ORDENES MAYOR A LA HORA DEL ULTIMO REPORTE
                  $sql_orden4 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$Hora_R_M' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$Hora_R_M' AND tecnicos_s LIKE '%$user%' )");
                  if(mysqli_num_rows($sql_orden4) > 0){ 
                    #SI ENCONTRAMOS REPORTES LOS RECORREMOS UNO A UNO
                    while($orden4 = mysqli_fetch_array($sql_orden4)){
                      #+++++++++++++++ IMPRIMIR ORDEN 4 ++++++++++++++++++++++
                      $id_cliente_o = $orden4['id_cliente'];
                      $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                      
                      $cadena_de_texto = $orden4['tecnicos_r'];
                      $cadena_buscada   = $user;
                      $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
                      //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                      if ($coincidencia === false){
                        $tecnicos = $orden4['tecnicos_s'];
                        $fecha = $orden4['fecha_s'];
                        $hora = $orden4['hora_s'];
                      } else {
                        $tecnicos = $orden4['tecnicos_r'];
                        $fecha = $orden4['fecha_r'];
                        $hora = $orden4['hora_r'];
                      }
                      ?>
                      <tr>
                        <td><?php echo $orden4['id']; ?></td>
                        <td><b>Orden de Servicio</b></td>            
                        <td><?php echo $cliente_o['nombre']; ?></td> 
                        <td><?php echo $fecha ?></td>
                        <td><?php echo $hora ?></td>
                        <td><?php echo $orden4['trabajo'] ?></td>
                        <td>Realizada</td>
                        <td><?php echo $tecnicos ?></td>
                      </tr>
                    <?php
                    }//FIN WHILE ORDENES 4
                  }//FIN IF ORDENES 4
                }// FIN IF $contador_reportes == 0
              }//FIN WHILE REPORTES 2
            }//FIN IF REPORTES 2
            #BUSACAR ORDENES MAYOR A LA HORA DE LA ULTIMA ACTIVIDAD  
            $sql_orden5 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$Hora_A' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$Hora_A' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_O, 20");
            if(mysqli_num_rows($sql_orden5) > 0){ 
              $iniciar_O = $iniciar_O+mysqli_num_rows($sql_orden5);
              #SI ENCONTRAMOS ORDENES LOS RECORREMOS UNO A UNO
              while($orden5 = mysqli_fetch_array($sql_orden5)){
                #+++++++++++++++ IMPRIMIR ORDEN 5 ++++++++++++++++++++++
                $id_cliente_o = $orden5['id_cliente'];
                $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                    
                $cadena_de_texto = $orden5['tecnicos_r'];
                $cadena_buscada   = $user;
                $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
                //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                if ($coincidencia === false){
                  $tecnicos = $orden5['tecnicos_s'];
                  $fecha = $orden5['fecha_s'];
                  $hora = $orden5['hora_s'];
                } else {
                  $tecnicos = $orden5['tecnicos_r'];
                  $fecha = $orden5['fecha_r'];
                  $hora = $orden5['hora_r'];
                }
                ?>
                <tr>
                  <td><?php echo $orden5['id']; ?></td>
                  <td><b>Orden de Servicio</b></td>            
                  <td><?php echo $cliente_o['nombre']; ?></td> 
                  <td><?php echo $fecha ?></td>
                  <td><?php echo $hora ?></td>
                  <td><?php echo $orden5['trabajo'] ?></td>
                  <td>Realizada</td>
                  <td><?php echo $tecnicos ?></td>
                </tr>
              <?php
              }//FIN WHILE ORDENES 5
            }//FIN IF ORDENES 5
          }//FIN IF actividades_contador       
        }//FIN WHILE ACTIVIDADES
      }else{
        #NO SE ENCUENTRAN ACTIVIDADES EL DIA EN TURNO BUSCAR REPORTES
        $sql_reporte3 = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1  AND (tecnico = '$id_user' OR apoyo = '$id_user' OR apoyomas LIKE '%$user%')) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user') ORDER BY hora_atendido");
        $contador_reportes2 = mysqli_num_rows($sql_reporte3);
        if(mysqli_num_rows($sql_reporte3) > 0){
          $iniciar_O2 = 0; 
          #SI ENCONTRAMOS REPORTES LOS RECORREMOS UNO A UNO
          while ($reporte3 = mysqli_fetch_array($sql_reporte3)) {
            $contador_reportes2 --;
            $Hora_R_SIN = $reporte3['hora_atendido'];
            #BUSCAMOS ORDENES MENORES A LA HORA DEL REPORTE EN TURNO
            $sql_orden6 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$Hora_R_SIN' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$Hora_R_SIN' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_O2, 20");
            if(mysqli_num_rows($sql_orden6) > 0){ 
              $iniciar_O2 = $iniciar_O2+mysqli_num_rows($sql_orden6);
              #SI ENCONTRAMOS ORDENES LOS RECORREMOS UNO A UNO
              while($orden6 = mysqli_fetch_array($sql_orden6)){
                #+++++++++++++++ IMPRIMIR ORDEN 6 ++++++++++++++++++++++
                $id_cliente_o = $orden6['id_cliente'];
                $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  
                $cadena_de_texto = $orden6['tecnicos_r'];
                $cadena_buscada   = $user;
                $coincidencia = strpos($cadena_de_texto, $cadena_buscada);                     
                //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                if ($coincidencia === false){
                  $tecnicos = $orden6['tecnicos_s'];
                  $fecha = $orden6['fecha_s'];
                  $hora = $orden6['hora_s'];
                } else {
                  $tecnicos = $orden6['tecnicos_r'];
                  $fecha = $orden6['fecha_r'];
                  $hora = $orden6['hora_r'];
                }
                ?>
                <tr>
                  <td><?php echo $orden6['id']; ?></td>
                  <td><b>Orden de Servicio</b></td>            
                  <td><?php echo $cliente_o['nombre']; ?></td> 
                  <td><?php echo $fecha ?></td>
                  <td><?php echo $hora ?></td>
                  <td><?php echo $orden6['trabajo'] ?></td>
                  <td>Realizada</td>
                  <td><?php echo $tecnicos ?></td>
                </tr>
              <?php
              }//FIN WHILE ORDENES 6
            }//FIN IF ORDENES 6
            #****************** IMPRIMIR REPORTE 3 ********************
              $id_cliente_r = $reporte3['id_cliente'];            
              $sql_cliente_r = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente_r");
              if (mysqli_num_rows($sql_cliente_r) == 0) {
                $sql_cliente_r = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_r");
              }
              $cliente_r = mysqli_fetch_array($sql_cliente_r);
              #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE3
              if ($reporte3['fecha_d']==$DIA AND $reporte3['tecnico_d'] == $id_user) {
                $id_d =$reporte3['tecnico_d'];
                if ($id_d != '') {
                  $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
                }
                ?>
                <tr>
                  <td><?php echo $reporte3['id_reporte']; ?></td>            
                  <td><b>Reporte</b></td>            
                  <td><?php echo $cliente_r['nombre']; ?></td>       
                  <td><?php echo $reporte3['fecha_d']; ?></td>
                  <td><?php echo $reporte3['hora_d']; ?></td>
                  <td><b>Reporte: </b> <?php echo $reporte3['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte3['falla']; ?>.</td>
                  <td>Diagnostico</td>
                  <td><?php echo $tecnico_d['firstname']; ?></td>
                </tr>
                <?php
              }//FIN IF ES DIAGNOSTICO REPORTE 3
              $mystring = $reporte3['apoyomas'];
              $findme   = $user;
              $pos = strpos($mystring, $findme);
              // El operador !== también puede ser usado. Puesto que != no funcionará como se espera
              // porque la posición de 'a' es 0. La declaración (0 != false) se evalúa a 
              // false.
              #VEMOS SI ES UN TERMINO DE REPORTE3 (SOLUCION)
              if ($reporte3['fecha_solucion']==$DIA AND $reporte3['atendido'] == 1 AND ($reporte3['tecnico'] == $id_user OR $reporte3['apoyo'] == $id_user OR $pos !== false)) {
                $id_tec = $reporte3['tecnico'];
                $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
                if ($reporte3['apoyo'] != 0) {
                  $id_apoyo = $reporte3['apoyo'];
                  $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
                  $Apoyo = ', Apoyo: '.$A['firstname'];
                }elseif($reporte3['apoyomas'] != ''){
                  $Apoyo = ', Apoyo: '.$reporte3['apoyomas'];
                }else{ $Apoyo = ''; }
                ?>
                <tr>
                  <td><?php echo $reporte3['id_reporte']; ?></td>            
                  <td><b>Reporte</b></td>            
                  <td><?php echo $cliente_r['nombre']; ?></td>  
                  <td><?php echo $reporte3['fecha_solucion']; ?></td>
                  <td><?php echo $reporte3['hora_atendido']; ?></td>
                  <td><b>Reporte: </b> <?php echo $reporte3['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $reporte3['falla']; ?>.</td>
                  <td><?php echo $reporte3['solucion']; ?></td>
                  <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
                </tr>
                <?php
              }//FIN IF ES UNA SOLUCION REPORTE 3
            if ($contador_reportes2 == 0) {//SI contador_reportes2 == 0 ES EL ULTIMO REPORTE
              #BUSACAR ORDENES MAYOR A LA HORA DEL ULTIMO REPORTE
              $sql_orden7 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$Hora_R_M' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$Hora_R_M' AND tecnicos_s LIKE '%$user%' )");
              if(mysqli_num_rows($sql_orden7) > 0){ 
                #SI ENCONTRAMOS REPORTES LOS RECORREMOS UNO A UNO
                while($orden7 = mysqli_fetch_array($sql_orden7)){
                  #+++++++++++++++ IMPRIMIR ORDEN 7 ++++++++++++++++++++++
                  $id_cliente_o = $orden7['id_cliente'];
                  $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                      
                  $cadena_de_texto = $orden7['tecnicos_r'];
                  $cadena_buscada   = $user;
                  $coincidencia = strpos($cadena_de_texto, $cadena_buscada);              //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($coincidencia === false){
                    $tecnicos = $orden7['tecnicos_s'];
                    $fecha = $orden7['fecha_s'];
                    $hora = $orden7['hora_s'];
                  } else {
                    $tecnicos = $orden7['tecnicos_r'];
                    $fecha = $orden7['fecha_r'];
                    $hora = $orden7['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $orden7['id']; ?></td>
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $cliente_o['nombre']; ?></td> 
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden7['trabajo'] ?></td>
                    <td>Realizada</td>
                    <td><?php echo $tecnicos ?></td>
                  </tr>
                  <?php
                }//FIN WHILE ORDENES 7
              }//FIN IF ORDENES 7
            }//FIN IF $contador_reportes2
          }// FIN WHILE REPORTE 3
        }else{// FIN IF REPORTE 3
          #SI NO HAY ACTIVIDADES NI REPORTES BUSCAMOS ORDENES
          $sql_orden8 = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE (fecha_r = '$DIA' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND tecnicos_s LIKE '%$user%')");
          if(mysqli_num_rows($sql_orden8) > 0){ 
            #+++++++++++++++++ IMPRIMIR ORDEN 8 +++++++++++++++++++++++++
            while($orden8 = mysqli_fetch_array($sql_orden8)){
              $id_cliente_o = $orden8['id_cliente'];
              $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                      
              $cadena_de_texto = $orden8['tecnicos_r'];
              $cadena_buscada   = $user;
              $coincidencia = strpos($cadena_de_texto, $cadena_buscada);              //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
              if ($coincidencia === false){
                $tecnicos = $orden8['tecnicos_s'];
                $fecha = $orden8['fecha_s'];
                $hora = $orden8['hora_s'];
              } else {
                $tecnicos = $orden8['tecnicos_r'];
                $fecha = $orden8['fecha_r'];
                $hora = $orden8['hora_r'];
              }
              ?>
              <tr>
                <td><?php echo $orden8['id']; ?></td>
                <td><b>Orden de Servicio</b></td>            
                <td><?php echo $cliente_o['nombre']; ?></td> 
                <td><?php echo $fecha ?></td>
                <td><?php echo $hora ?></td>
                <td><?php echo $orden8['trabajo'] ?></td>
                <td>Realizada</td>
                <td><?php echo $tecnicos ?></td>
              </tr>
              <?php
            }//FIN WHILE ORDENES 8
          }//FIN IF ORDENES 8
        }//FIN ELSE REPORTE
      }//FIN ELSE ACTIVIDAD
      $nuevafecha = strtotime('+1 day', strtotime($DIA));
      $DIA = date('Y-m-d', $nuevafecha);
    }//FIN WHILE $DIA
?>
<?php 
?>        
    </tbody>
</table><br><br>
