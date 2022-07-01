<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<!--Import material-icons.css-->
  <link href="css/material-icons.css" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="shortcut icon" href="../img/logo.jpg" type="image/jpg" />
  <style rel="stylesheet">
    .dropdown-content{  overflow: visible;  }
  </style>

<!DOCTYPE html>
<html lang="en">
<head>
<title>SIC | Usuarios</title>
<script>
  function validar_email( email )   {
      var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email) ? true : false;
  };
  function insert_usuario() {
      var textoNombre = $("input#nombre").val();
      var textoApellidos = $("input#apellidos").val();
      var textoEmail = $("input#email").val();
      var textoUsuario = $("input#usuario").val();
      var textoContra = $("input#contra").val();
      var textoRepiteContra = $("input#repite_contra").val();
      var textoRol = $("select#rol").val();
    
      if (textoNombre == "") {
        M.toast({html:"Por favor ingrese el nombre(s).", classes: "rounded"});
      }else if(textoApellidos == ""){
        M.toast({html:"Por favor ingrese los apellidos.", classes: "rounded"});
      }else if(textoEmail == ""){
        M.toast({html:"Por favor ingrese un Email.", classes: "rounded"});
      }else if (!validar_email(textoEmail)) {
        M.toast({html:"Por favor ingrese un Email correcto.", classes: "rounded"});
      }else if(textoUsuario == ""){
        M.toast({html:"Por favor ingrese el nombre de usuario.", classes: "rounded"});
      }else if(textoContra == ""){
        M.toast({html:"Por favor ingrese una contraseña.", classes: "rounded"});
      }else if ((textoContra.length) < 6) {
        M.toast({html:"Por favor ingrese una contraseña mas larga.", classes: "rounded"});
      }else if(textoContra != textoRepiteContra){
        M.toast({html:"Las contraseñas no coinciden.", classes: "rounded"});
      }else if(textoRol == 0){
        M.toast({html:"Seleccione un rol de usuario.", classes: "rounded"});
      }else{
        $.post("../php/insert_user.php", {
            valorNombre: textoNombre,
            valorApellidos: textoApellidos,
            valorEmail: textoEmail,
            valorUsuario: textoUsuario,
            valorContra: textoContra,
            valorRol: textoRol
          }, function(mensaje) {
              $("#resultado_usuarios").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<body>
  <div class="container">
    <div id="resultado_usuarios"></div>
    <div class="row"><br><br><br>
      <a href = "login.php" class="btn waves-effect waves-light pink right">Iniciar Sesión</a>
      <h3>Nuevo Usuario:</h3>
      <div class="row col s12">

        <div class="col s12 m6 l6">
          <div class="input-field">
            <input type="text" class="validate" required id="nombre">
            <label for="nombre">Nombre</label>
          </div>
          <div class="input-field">
            <input type="email" class="validate" required id="email">
            <label for="email">E-mail</label>
          </div>
          <div class="input-field">
            <input type="password" class="validate" required id="contra">
            <label for="contra">Contraseña</label>
          </div>
          <div class="input-field">
            <select id="rol" class="browser-default">
              <option value="0" selected>Seleccione un rol</option>
              <option value="Taller">Taller</option>
              <option value="Redes">Redes</option>
              <option value="Oficina">Oficina</option>
              <option value="Administrador">Administrador</option>
              <option value="Cobrador">Cobrador</option>
            </select>
          </div>
        </div> 
        <div class="col s12 m6 l6">
          <div class="input-field">
            <input type="text" class="validate" required id="apellidos">
            <label for="apellidos">Apellidos</label>
          </div>
          <div class="input-field">
            <input type="text" class="validate" required id="usuario">
            <label for="usuario">Nombre de usuario</label>
          </div>
          <div class="input-field">
            <input type="password" class="validate" required id="repite_contra">
            <label for="repite_contra">Repite Contraseña</label>
          </div>
          <div class="input-field">
            <a onclick="insert_usuario();" class="waves-effect waves-light btn pink right">GUARDAR<i class="material-icons right">send</i></a>
          </div>
        </div>        
      </div>
    </div>
  </div>
</body>
</main>
</html>
<?php 
  include('../views/modals.php');
  include('../php/scripts.php');
  ?>
  <script src="js/jquery-3.1.1.js"></script>
  <!--JavaScript at end of body for optimized loading-->
  <script type="text/javascript" src="js/materialize.min.js"></script>
