<!DOCTYPE html>
<html>
<head>
	<title>SIC | Perfil Usuario</title>
<?php
include('fredyNav.php');
?>
</head>
<?php
if ($_SESSION['user_id'] == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a inicio.", classes: "rounded"})
    setTimeout("location.href='home.php'", 8000);
  </script>
    <?php
}else{
  $user_id = $_SESSION['user_id'];
  $datos = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
  ?>
  <script>
    function validar_email( email )   {
        var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email) ? true : false;
    };
    function editar_perfil(id){
      var textoNombres = $("input#nombres").val();
      var textoApellidos = $("input#apellidos").val();
      var textoUsuario = $("input#usuario").val();
      var textoEmail = $("input#email").val();

      if (textoNombres == "") {
        M.toast({html:"El campo Nombres se encuentra vacío", classes: "rounded"});
      }else if (textoApellidos == "") {
        M.toast({html:"El campo Apellidos se encuentra vacío", classes: "rounded"});
      }else if (textoUsuario == "") {
        M.toast({html:"El campo Usuario se encuentra vacío", classes: "rounded"});
      }else if (!validar_email(textoEmail)) {
          M.toast({html:"Por favor ingrese un E-mail correcto.", classes: "rounded"});
      }else if (textoEmail == "") {
        M.toast({html:"El campo E-mail se encuentra vacío", classes: "rounded"});
      }else {
        $.post("../php/update_user.php", { 
            valorId: id,
            valorNombres: textoNombres,
            valorApellidos: textoApellidos,
            valorUsuario: textoUsuario,
            valorEmail: textoEmail,
        }, function(mensaje) {
            $("#update_perfil").html(mensaje);   
        });
      }
    }
    function editar_pass(id){
        M.toast({html:"editar contraseña.", classes: "rounded"});
        M.toast({html:"muestra modal y cambiar contraseña ahi.", classes: "rounded"});
    }
  </script>
  <body>
    <div class="container" id="update_perfil">
      <div class="row">
        <h2 class="hide-on-med-and-down">Perfil:</h2>
        <h4 class="hide-on-large-only">Perfil:</h4>
      </div>
      <div class="row">
        <ul class="collection">
          <li class="collection-item avatar">
            <div class="row">
                <img src="../img/cliente.png" alt="" class="circle">
                <span class="title"><b>Id. Usuario: </b><?php echo $datos['user_id'];?></span><br>
                <div class="col s12"><br>
                  <b class="col s4 m2 l2">Nombre(s): </b>
                  <div class="col s12 m9 l9">
                    <input type="text" id="nombres" name="nombres" value="<?php echo $datos['firstname']; ?>">
                  </div>
                </div>
                <div class="col s12">
                  <b class="col s4 m2 l2">Apellidos: </b>
                  <div class="col s12 m9 l9">
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo $datos['lastname'];?>">
                  </div>
                </div>
                <div class="col s12">
                  <b class="col s4 m2 l2">Usuario: </b>
                  <div class="col s12 m9 l9">
                    <input type="text" id="usuario" name="usuario" value="<?php echo $datos['user_name'];?>">
                  </div>
                </div>
                <div class="col s12">
                  <b class="col s4 m2 l2">E-mail: </b>
                  <div class="col s12 m9 l9">
                    <input type="text" id="email" name="email" value="<?php echo $datos['user_email'];?>">
                  </div>
                </div>
                <div class="col s12"><br>
                  <b class="col s4 m2 l2">Area: </b><?php echo $datos['area'];?>
                </div>
            </div>
            <br><hr>
            <b>Accion: </b>
            <a onclick="editar_pass(<?php echo $datos['user_id'];?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">security</i>Editar contraseña</a>
            <a onclick="editar_perfil(<?php echo $datos['user_id'];?>);" class="waves-effect waves-light btn pink lighten-1 right"><i class="material-icons right">edit</i>Editar perfil</a><br><br>
          </li><br>
        </ul>   
      </div>
    </div>
  </body>
<?php 
}
?>
</html>