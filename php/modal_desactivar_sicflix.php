<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

//OBTENEMOS LAS VARIABLES DEL ARCHIVO reportes_sicflix.php
$valorIDReporte = $conn->real_escape_string($_POST['valorIDReporte']);
$reporte=mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `reporte_sicflix` WHERE id = $valorIDReporte"));
#sacar info de cliente
$ID_Cliente=$reporte['cliente'];
$Cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `clientes` WHERE id_cliente = $ID_Cliente"));
?>
<script>
    $(document).ready(function(){
        $('#mostrarmodal').modal();
        $('#mostrarmodal').modal('open'); 
    });
// MANDAMOS LAS VARIABLES AL ARCHIVO desactivar_sicflix.php
    function atender_reporte(){
        var textoid_reporte = $("input#id_reporte").val();
        var textoid_cliente = $("input#id_cliente").val();
        $.post("../php/desactivar_sicflix.php", {
            valorid_reporte: textoid_reporte,
            valorid_cliente: textoid_cliente
        },function(mensaje) {
            $("#mostrar_pagos").html(mensaje);
        });
    };
</script>
<!-- Modal Structure -->
  <div id="mostrarmodal" class="modal">
    <div class="modal-content">
      <h5 class="red-text center">! Advertencia !</h5>
      <p>
        <table class="bordered highlight responsive-table" id="mostrar_pagos">
          <thead>
          </thead>
          <tbody>     
          </tbody>
        </table>
        <h6  class="blue-text"><b>Atender reporte de desactivación <b class="red-text">DESACTIVACIÓN SICFLIX</b></h6>
        <table class="bordered highlight responsive-table">
          <thead>
          </thead>
          <tbody>      
        </tbody>
      </table>
      <h6 class="blue-text"><b>¿DESEA ATENDER ESTE REPORTE?</b></h6>
      <table class="bordered highlight responsive-table ">
          <thead>
          </thead>
          <tbody>
            <tr>
              <td width="22%"><b>Fecha: </b><?php echo date('Y-m-d'); ?></td>
              <td width="22%"><b>Cliente: </b><?php echo $Cliente['nombre']; ?></td>
            </tr>
          </tbody>
      </table>
      </p>
    </div>
    <div class="modal-footer">
      <form name="formMensualidad">
        <input id="id_reporte" name="id_reporte" type="hidden" value="<?php echo $valorIDReporte ?>">
        <input id="id_cliente" name="id_cliente" type="hidden" value="<?php echo $ID_Cliente ?>">
      </form>
      <a onclick="atender_reporte();" class="modal-close waves-effect waves-light btn green accent-4 "><b>Continuar</b></a>

      <form method="post" action="../views/reportes_sicflix.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $ID_Cliente ?>"><button class="btn waves-effect red accent-4 waves-light" type="submit" name="action">
      <b>Cancelar</b>
      </button></form>
    </div>
  </div>