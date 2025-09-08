<?php
require_once('modelo/clsPerfil.php');
require_once('modelo/clsVenta.php');
require_once('modelo/clsProducto.php');

$objPer = new clsPerfil();
$objVen = new clsVenta();
$objPro = new clsProducto();

$listaOpciones = $objPer->listarOpcionesMenu($_SESSION['idperfil']);
$listaOpciones = $listaOpciones->fetchAll(PDO::FETCH_NAMED);

$dataVenta = $objVen->ventasDelDia();
$dataVenta = $dataVenta->fetchAll(PDO::FETCH_NAMED);


$resumen = array('contado'=>0, 'credito'=>0, 'total'=>0, 'nro_venta'=>0);

foreach($dataVenta as $k=>$v){
  if($v['formapago']=='C'){
    $resumen['contado'] = $v['total'];
  }else if($v['formapago']=='D'){
    $resumen['credito'] = $v['total'];
  }

  $resumen['total'] += $v['total'];
  $resumen['nro_venta'] += $v['nro_ventas'];
}

$problemasStock = $objPro->productosConProblemaStock();
$problemasStock = $problemasStock->fetchAll(PDO::FETCH_NAMED);

$productosVendidos = $objVen->productosMasVendidos();
$productosVendidos = $productosVendidos->fetchAll(PDO::FETCH_NAMED);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema | Farmacia</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- fileinput -->
  <link rel="stylesheet" href="plugins/fileinput/css/fileinput.css"> 
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <link rel="stylesheet" href="plugins/fileinput/css/fileinput.css"> 
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="principal.php" class="nav-link">Inicio</a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  <?= $_SESSION['nombre'] ?>
                </h3>
                <p class="text-sm"><?= $_SESSION['perfil'] ?></p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="index.php" class="btn btn-danger dropdown-footer">Cerrar Sesion</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">San Rafael</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?= $_SESSION['nombre'] ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <?php

            foreach($listaOpciones as $k=>$v){
          ?>
          <li class="nav-item">
            <a href="#" class="nav-link" onclick="AbrirPagina('<?php echo $v['url'] ?>')">
              <i class="nav-icon fa <?php echo $v['icono'] ?>"></i>
              <p>
                <?php echo $v['descripcion']; ?>
              </p>
            </a>
          </li>
         <?php } ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" id="divPrincipal">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $resumen['total'] ?></h3>
                <p>Total Ventas</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
              <a href="#" class="small-box-footer" onclick="AbrirPagina('vista/ventas.php')">Más informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $resumen['contado'] ?></h3>
                <p>Venta Contado</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">Más informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $resumen['credito'] ?></h3>
                <p>Venta Credito</p>
              </div>
              <div class="icon">
                <i class="ion ion-alert-circled"></i>
              </div>
              <a href="#" class="small-box-footer">Más informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $resumen['nro_venta'] ?></h3>
                <p>Nro Ventas</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-cart"></i>
              </div>
              <a href="#" class="small-box-footer">Más informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Productos Con Problema de Stock</h3>
              </div>
              <div class="card-body table-responsive" style="padding: 0px; height: 200px;">
                <table class="table table-bordered table-sm table-hover table-striped">
                  <thead>
                    <tr class="bg-info">
                      <th>NOMBRE</th>
                      <th>UNIDAD</th>
                      <th>STOCK</th>
                      <th>STOCK MINIMO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($problemasStock as $k=>$v){ ?>
                      <tr>
                        <td><?= $v['nombre'] ?></td>
                        <td><?= $v['unidad'] ?></td>
                        <td class="text-right"><?= $v['stock'] ?></td>
                        <td class="text-right"><?= $v['stockseguridad'] ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Productos Más Vendidos(TOP 10)</h3>
              </div>
              <div class="card-body table-responsive" style="padding: 0px; height: 200px;">
                <table class="table table-bordered table-sm table-hover table-striped">
                  <thead>
                    <tr class="bg-info">
                      <th>NOMBRE</th>
                      <th>UNIDAD</th>
                      <th>CANTIDAD</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($productosVendidos as $k=>$v){ ?>
                      <tr>
                        <td><?= $v['nombre'] ?></td>
                        <td><?= $v['unidad'] ?></td>
                        <td class="text-right"><?= $v['cantidad'] ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>



      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->

  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2025 <a href="#">Avellaneda</a>.</strong>
    Todos los derechos reservados.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 2.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- /.modal de confirmacion-->
<div class="modal fade" id="modalConfirmacion">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        <div class="modal-header bg-info">
            <h4 class="modal-title">Confirmar</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="mensaje_confirmacion">
            ¿Está seguro de Anular la categoría?
        </div>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <div id="boton_confirmacion">
                
            </div>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<!-- fileinput -->
<script src="plugins/fileinput/js/fileinput.js"></script>
<script src="plugins/fileinput/js/fileinput_locale_es.js"></script>

<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="plugins/fileinput/js/fileinput.js"></script>
<script src="plugins/fileinput/js/fileinput_locale_es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js
"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="dist/js/pages/dashboard.js"></script> -->
<script>

  function AbrirPagina(urlx){
    $.ajax({
      method: 'POST',
      url: urlx
    })
    .done(function(retorno){
      $('#divPrincipal').html(retorno);
    });
  }

  function mostrarModalConfirmacion(mensaje, accion){
      $("#mensaje_confirmacion").html(mensaje);

      btn_html = '<button type="button" class="btn btn-primary" onclick="CerrarModalConfirmacion();'+accion+'">Confirmar</button>';

      $("#boton_confirmacion").html(btn_html);
      $("#modalConfirmacion").modal("show");
  }

  function CerrarModalConfirmacion(){
    $("#modalConfirmacion").modal("hide");
  }

  function toastCorrecto(mensaje){
    $(document).Toasts('create', {
        title: 'Correcto',
        class: 'bg-success',
        autohide: true,
        delay: 3000,
        body: mensaje
    });
  }

  function toastError(mensaje){
    $(document).Toasts('create', {
        title: 'Error',
        class: 'bg-danger',
        autohide: true,
        delay: 3000,
        body: mensaje
    });
  }

  var opcionActiva=null;
  function verificarSeleccionado(element, menuvertical=1){
    console.log(element);
    $(opcionActiva).parent().parent().parent().removeClass("menu-is-opening menu-open");
    $(opcionActiva).parent().parent().css("display","none");
    $(opcionActiva).parent().parent().prev().removeClass("active");
    $(opcionActiva).removeClass("bg-teal-active active");
    $(opcionActiva).parent().parent().prev().css("border-left-color","");

    console.log($(element).parent());
    if(!$(element).parent().hasClass("active") && menuvertical==1){
      $(element).parent().parent().parent().addClass("menu-is-opening menu-open");
      $(element).parent().parent().css("display","block");
      $(element).parent().parent().prev().addClass("active");
      $(element).addClass("bg-teal-active active");
      $(element).parent().parent().prev().css("border-left-color","#458d94");

      opcionActiva=element;
    }

    
  }

</script>
</body>
</html>
