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
                <p>                 
                    <b>Documento: </b><?php echo $Documento['nombre']?> <a href = "../files/catalogo_imagen/<?php echo $Documento['nombre'];?>" class="btn-small waves-effect waves-light" target = "blank"><i class="material-icons">file_download</i></a>   
                    <div class="row col s10"><br>
                        <div class="right">
                            <?php $id_user = $_SESSION['user_id'];
                            $info_usuario=mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));?>
                            <a href="#EditarCatalogo" class="btn-small modal-trigger pink waves-effect waves-light <?php echo ($Documento['nombre'] == '' OR $info_usuario['area']!='Administrador')? 'disabled': ''; ?> rigth"><i class="material-icons">edit</i></a>
                            <a href="#SubirCatalogo1" class="btn-small modal-trigger green waves-effect waves-light <?php echo ($Documento['nombre'] != '')? 'disabled': ''; ?> rigth"><i class="material-icons">file_upload</i></a>
                        </div>
                            
                    </div><br><br><br>
                </p>
                </li>
            </ul>
    </div>
    </body>
</html>