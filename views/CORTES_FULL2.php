<!DOCTYPE html>
<html lang="en">
<head>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE QUE SOLOS LOS USUARIOS QUE SEAN ADMINISTRADORES PUEDAN ACCEDER A ESTA LISTA
  include('../php/admin.php');
?>
<title>SIC | Cortando...</title>
<script>

  function lista_Cortes() {
    var textoServidor = $("select#servidor").val();
    if (textoServidor == 0) {
      M.toast({html:"Seleccione un servidor.", classes: "rounded"});
    }else{
      M.toast({html: 'Esto puede tardar unos minutos espera...', classes: 'rounded'});
      //HACEMOS VISIBLE EL DIV QUE TIENE LE ID content
      element = document.getElementById("content");
      element.style.display='block'; 

      $.post("../php/lista_Cortes.php", {
          valorServidor: textoServidor,
          }, function(mensaje) {
              $("#lista").html(mensaje);
          }); 
    }
  };
  function verificar(id, inicia) { 
    M.toast({html: 'Verificando lista...', classes: 'rounded'});

    $.post("../php/verificar_list.php", {
      valorServidor: id,
      valorInicia: inicia,
    }, function(mensaje) {
      $("#verificar").html(mensaje);
    }); 
  };
  function tempShow(seconds){
    M.toast({html: 'Temporizar...', classes: 'rounded'});
    element = document.getElementById('clock');
    var fecha = new Date();
    fecha.setSeconds(fecha.getSeconds() + seconds);// SEGUNDOS QUE PONDRA EN EL TEMPORIZADOR
    fecha.getSeconds();
    countdown(fecha);
    element = document.getElementById('clock');
    element.style.display='block';
  }
  function iniciarCorte(id) { 
    M.toast({html: 'Cortando servicios...', classes: 'rounded'});

    $.post("../php/cortar_internet.php", {
      valorServidor: id,
    }, function(mensaje) {
      $("#lista").html(mensaje);
    }); 
  };
  //FUNCION QUE CREA EL CORTE EN UNA TABLA CON FECHA PARA ASI IR TENIENDO UN REGISTRO
  function crear_corte(){
    $.post("../php/crear_corteInt.php", {
    }, function(mensaje){
        $("#Ver").html(mensaje);
    });
  }

</script>
</head>
<body>
	<div class="container"><br><br>
    <div id="Ver"></div>
    <div class="row">
      <div class="col l4 m4 s12"> 
        <h3>Cortar Servicio</h3>
      </div> 
      <div class="input-field col l4 m4 s12"><br>
        <select id="servidor" class="browser-default">
          <option value="0" selected>Seleccione un servidor</option>
          <?php 
          $sql = mysqli_query($conn,"SELECT * FROM servidores ");
          while($Servidor = mysqli_fetch_array($sql)){
          ?>
            <option value="<?php echo $Servidor['id_servidor'];?>"><?php echo $Servidor['nombre'];?></option>
          <?php
          }
          ?>
        </select>
      </div> 
      <div class="col l2 m2 s12"><br><br>
        <button class="btn waves-light waves-effect right pink" onclick="lista_Cortes();"><i class="material-icons prefix right">list</i>Lista Cortar</button>
      </div> 
      <div class="col l2 m2 s12"><br><br>
        <a href="#!" class="waves-effect waves-light pink btn" onclick="crear_corte();"><i class="material-icons left">content_cut</i>Crear</a>
      </div> 
    </div>
    <div class="row" id="lista"></div><br>
    <div class="progress" id="content" style="display: none;"><br><br>
      <div class="indeterminate indigo darken-4"></div>
    </div>
  </div>
</body>
</html>
<script>
  // EN MINUTOS Y SEGUNDOS MUESTRA LA DIFERENCIA DE TIEMPO Y CADA QUE SEA SOLICITADO MOSTRARA LA DIFECENCIA
  const getRemainingTime = deadline => {
    let now = new Date(),
    remainTime = (new Date(deadline) - now + 1000) / 1000,// SE HACE LA RESTA DE TIEMPOS Y SE SUMA 1000 SEL SEGUNDO QUE YA PASO EN LA SOLICITUD COMPENZAR
    remainSeconds = ('0' + Math.floor(remainTime % 60)).slice(-2),//MUESTRA DOS DIJITOS
    remainMinutes = ('0' + Math.floor(remainTime / 60 % 60)).slice(-2);//MUESTRA DOS DIJITOS
    return {
      remainSeconds,
      remainMinutes,
      remainTime
    }
  };
  //MOSTRAMOS EN EL ELEMENTO clock DENTRO DEL HTML
  const countdown = (deadline) => {
    const el = document.getElementById('clock');
    //ACTUALIZAMOS CADA SEGUNDO Y MOSTRAMOS LA DIFERENCIA DE TIEMPO SOLICITANDO CADA SEGUNDO
    const timerUpdate = setInterval( () => {
      let t = getRemainingTime(deadline);
      el.innerHTML = `${t.remainMinutes}m:${t.remainSeconds}s`;
      // SI SE CUMPLE QUITAMOS EL RELOJ Y MOSTRAMOS MSJ
      if(t.remainTime <= 1) {
        clearInterval(timerUpdate);
        el.innerHTML = '<h5 class = "green-text">¡Tiempo de Ejecución Terminado !</h5>';
      }
    }, 1000);
  };
</script>