<script>
    function subir() {  
      $.post("modal_catalogo_subir.php", {
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        });
  };
</script>
<html>
<head>
    <title>SIC | Catalogo</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
$id_user = $_SESSION['user_id'];
$Documento=mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM catalogo ORDER BY id DESC LIMIT 1"));

  ?>

<body>

    <div class="container">
            <div class="row">
            <h3 class="hide-on-med-and-down">Catalogo de Productos:</h3>
            <h5 class="hide-on-large-only">Catalogo de Productos:</h5>
            </div>
    <div class="row">
            <ul class="collection">
                <li class="collection-item avatar">
                <img src="../img/libro_icono.png" alt="" class="circle">
                <span class="title"><b>Folio: </span>
                <p>                 
                    <b>Documento: </b><?php echo $Documento['nombre']?> <a class = "btn" href = "../files/catalogo_imagen/<?php echo $Documento['nombre']?>" target = "blank"> Descargar <i class="material-icons">archive</i></a><br>  
                    <div class="row col s10"><br>
                        <div class="right">
                            <!-- Modal Trigger -->
                            <a class="pink waves-effect waves-light btn modal-trigger right" href="#EditarCatalogo"><i class="material-icons ">edit</i></a>
                            <a class="green waves-effect waves-light btn modal-trigger rigth" href="#SubirCatalogo1"><i class="material-icons ">file_upload</i></a>
                        </div>
                    </div><br><br><br>
                </p>
                </li>
            </ul>
    </div>
    </body>
</html>
