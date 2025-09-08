<?php
session_start();
session_destroy();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Sistema POS</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    .fondo_imagen{
      background: url('imagen/fondo.jpg') top center no-repeat;
      background-size: cover;
    }
  </style>
</head>
<body class="hold-transition login-page fondo_imagen">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Sistema</b>POS</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Ingresar al Sistema</p>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="txtuser" id="txtuser" placeholder="Usuario">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="txtpass" id="txtpass" onkeyup="if(event.keyCode=='13'){ ingresarSistema(); }" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" onclick="ingresarSistema()">Ingresar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script>
  
  function ingresarSistema(){
    if(verificarFormulario()){
      $.ajax({
        method: 'POST',
        url: 'controlador/contUsuario.php',
        data: {
            accion: 'INICIAR_SESION',
            usuario: $('#txtuser').val(),
            clave: $('#txtpass').val()
        },
        dataType: 'json'
      })
      .done(function(respuesta){
        if(respuesta.correcto==1){
          window.open('principal.php','_self');
        }else{
          alert(respuesta.mensaje);
        }

      })
    }
  }

  function verificarFormulario(){
    let condicion = true;

    let usuario = $('#txtuser').val();
    let clave = $('#txtpass').val();

    if(usuario==''){
      condicion = false;
      alert('Por favor ingresa tu usuario...');
    }else if(clave==''){
      condicion = false;
      alert('Por favor ingresa tu contraseña...');
    }

    return condicion;
  }

</script>
</body>
</html>
