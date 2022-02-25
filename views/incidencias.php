<!DOCTYPE html>
<html lang="en">
<head>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include('fredyNav.php');
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#INCLUIMOS UN ARCHIVO QUE PROHIBE EL ACCESO A ESTA VISTA A LOS USUARIOS CON EL ROL DE COBRADOR 
include('../php/cobrador.php');
?>
<title>SIC | Incidencias</title>
</head>
<script>
  function borrar_incidencia(id){
    $.post("../php/borrar_incidencia.php", {
            valorId: id,
    }, function(mensaje) {
      $("#delete").html(mensaje);
    }); 
  };
</script>
<main>
<body>
	<div class="container">
    <div><br><br><br>
      <h3 class="row"><b>Incidencias Pendientes:</b></h3><br>
      <a class="waves-effect waves-light btn indigo right" href="estadistica_incidencias.php">Estadisticas<i class="material-icons right">network_check</i></a>
      <a class="waves-effect waves-light btn pink" href="form_incidencia.php">Agregar<i class="material-icons right">add</i></a>
      <div class="row" id="delete">
      <?php
      #SELECCIONAMOS TODAS LAS UNIDADES
      $Incidencias = mysqli_query($conn, "SELECT * FROM incidencias WHERE estatus = 0 ORDER BY prioridad");
      #VERIFICAMOS SI ENCONTRAMOS MAS DE UNA UNIDAD
      if (mysqli_num_rows($Incidencias) > 0) {
        #SI ENCONTRAMOS Incidencias CREAMOS UNA TABLA CON ESTOS MISMOS
        ?>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>Prio.</th>
            <th>#</th>
            <th>Comunidad</th>
            <th>Descripcion</th>
            <th>Fecha TO</th>
            <th>Hora TO</th>
            <th>Fecha TD</th>
            <th>Hora TD</th>
            <th>Atender</th>
            <th>Editar</th>
            <th>Borrar</th>
          </thead>
          <tbody>
          <?php
          #RECORREMOS UNO POR UNO LAS Incidencias PARA MOSTRAR SU INFORMACION Y ACCEDER A CADA UNO DE ELLOS
          while($incidencia = mysqli_fetch_array($Incidencias)){
            $id_comunidad = $incidencia['comunidad'];
            $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
            $color = 'yellow darken-3';
            if ($incidencia['prioridad'] == 'Alta') {
              $color = 'red darken-4';
            }elseif ($incidencia['prioridad'] == 'Media') {
              $color = 'orange darken-4';
            }
            ?>
            <tr>
              <td><span class="new badge <?php echo $color; ?>" data-badge-caption="<?php echo $incidencia['prioridad']; ?>"></span></td>
              <td><?php echo $incidencia['id']; ?></td>
              <td><?php echo $comunidad['nombre'].', '.$comunidad['municipio']; ?></td>
              <td><?php echo $incidencia['descripcion']; ?></td>
              <td><?php echo $incidencia['fecha_to']; ?></td>
              <td><?php echo $incidencia['hora_to']; ?></td>
              <td><?php echo $incidencia['fecha_td']; ?></td>
              <td><?php echo $incidencia['hora_td']; ?></td>
              <td><form action="atender_incidencia.php" method="post"><input type="hidden" name="id" value="<?php echo $incidencia['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
              <td><form action="editar_incidencia.php" method="post"><input type="hidden" name="id" value="<?php echo $incidencia['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
              <td><a onclick="borrar_incidencia(<?php echo $incidencia['id']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
          <?php
          }//FIN WHILE
          ?> 
          </tbody>
        </table>
        </div>
      <?php
      }else{//FIN DEL IF
        echo '<h4>No se encontraron Incidencias</h4>';
      }
      ?>
    </div>
  </div><br><br>
<?php mysqli_close($conn);?>
</body>
</main>
</html>