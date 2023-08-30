<html>
  <head>
    <title>SIC | Archivos central</title>
  </head>
  <?php 
    include('fredyNav.php');
    include('../php/cobrador.php');
    if (isset($_POST['id_central']) == false) {
      ?>
      <script>    
        M.toast({html: "Regresando a centrales.", classes: "rounded"});
        setTimeout("location.href='centrales.php'", 800);
        // Store the file name into variable
      </script>
      
      <?php
    }else{
      $id_central = $_POST['id_central'];
      $sql = "SELECT * FROM centrales WHERE id=$id_central";
      $datos = mysqli_fetch_array(mysqli_query($conn, $sql));
      $id_comunidad = $datos['comunidad'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
      $archivo = "../files/centrales/".$id_central."_central.pdf";
      $documentoSubido = "";
      if($datos['documentoPdf'] == ""){
        $documentoSubido = "No se ha subido el documento";
      }else{
        $documentoSubido = $datos['documentoPdf'];
      }
  ?>
  <script>
    function subir(id) {  
      $.post("modal_doc_central.php", {
          valorId: id,
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        });
    };

    function actualizar(id) {  
      $.post("editar_doc_central.php", {
          valorId: id,
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        });
    };
  </script>
  <style>
  
  </style>
  <body>
    <div class="container">
        <div class="row" >
            <h3 class="hide-on-med-and-down">Archivos central</h3>
            <h5 class="hide-on-large-only">Archivos central</h5>
            <div id="Continuar"></div> 
        <div class="col s12 m12 l12">
            <ul class="collection">
              <li class="collection-item avatar">
                <img src="../img/cliente.png" alt="" class="circle">
                <span class="title"><b>No. Central: </b><?php echo $datos['id'];?></span>
                <p><b>Encargado: </b><?php echo $datos['nombre'];?><br>
                  <b>Telefono: </b><?php echo $datos['telefono'];?><br>
                  <b>Comunidad: </b><?php echo $comunidad['nombre'];?>, <b>Municipio:</b> <?php echo $comunidad['municipio'];?><br>
                  <b>Dirección: </b><?php echo $datos['direccion'];?><br>
                  <b>------------------------------------ Info documento subido----------------------------</b><br> 
                  <b><?php echo $documentoSubido ?></b><br><br>
                  <a onclick="subir(<?php echo $id_central;?>)" class="waves-effect waves-light green btn" <?php echo ($datos['documentoPdf'] != '')? 'disabled': ''; ?>><i class="material-icons left">file_upload</i>SUBIR</a>
                  <a onclick="actualizar(<?php echo $id_central;?>)" class="waves-effect waves-light pink btn" <?php echo ($datos['documentoPdf'] == '')? 'disabled': ''; ?>><i class="material-icons left">edit</i>ACTUALIZAR</a>
                </p>
              </li>
            </ul> 
        </div>
        <div class="col s12 m12 l12" ><br>
        <embed src="<?php echo $archivo;?>" type="application/pdf" width="100%" height="100%" />
        </div>
      </div>
    </div>
  </body> 
  <?php } ?>
</html>
