<?php 
	require_once('../modelo/clsVenta.php');
	require_once('../modelo/clsUsuario.php');

	$objVenta = new clsVenta();
	$objUsu = new clsUsuario();

	$listaComprobante = $objVenta->consultarComprobante();
	$listaComprobante = $listaComprobante->fetchAll(PDO::FETCH_NAMED);

	$listaUsuario = $objUsu->listarUsuario('',1);
	$listaUsuario = $listaUsuario->fetchAll(PDO::FETCH_NAMED);
?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Listado de Ventas</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Desde</span>
							</div>
							<input type="date" class="form-control" name="txtBusquedaFechaDesde" id="txtBusquedaFechaDesde" onkeyup="if(event.keyCode=='13'){ verListado(); }" value="<?= date('Y-m-01'); ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Hasta</span>
							</div>
							<input type="date" class="form-control" name="txtBusquedaFechaHasta" id="txtBusquedaFechaHasta" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Cliente</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaNombre" id="txtBusquedaNombre" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Comprobante</span>
							</div>
							<select class="form-control" name="cboBusquedaComprobante" id="cboBusquedaComprobante" onchange="verListado()">
								<option value="">- Todos -</option>
								<?php foreach($listaComprobante as $k=>$v){ ?>
								<option value="<?= $v['idtipocomprobante'] ?>"><?php echo $v['nombre']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Correlativo</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaCorrelativo" id="txtBusquedaCorrelativo" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Trabajador</span>
							</div>
							<select class="form-control" name="cboBusquedaTrabajador" id="cboBusquedaTrabajador" onchange="verListado()">
								<option value="">- Todos -</option>
								<?php foreach($listaUsuario as $k=>$v){ ?>
								<option value="<?= $v['idusuario'] ?>"><?php echo $v['nombre']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Estado</span>
							</div>
							<select class="form-control" name="cboBusquedaEstado" id="cboBusquedaEstado" onchange="verListado()">
								<option value="">- Todos -</option>
								<option value="1">Activos</option>
								<option value="0">Anulados</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<button type="button" class="btn btn-primary" onclick="verListado()"><i class="fa fa-search"></i> Buscar</button>
						<button type="button" class="btn btn-success" onclick="NuevaVenta()"><i class="fa fa-plus"></i> Nuevo</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card card-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" id="divListadoVentas">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
	function verListado(){
		$.ajax({
	      	method: 'POST',
	      	url: 'vista/ventas_listado.php',
	      	data:{
	      		desde: $('#txtBusquedaFechaDesde').val(),
	      		hasta: $('#txtBusquedaFechaHasta').val(),
	      		nombre: $('#txtBusquedaNombre').val(),
	      		idtipocomprobante: $('#cboBusquedaComprobante').val(),
	      		correlativo: $('#txtBusquedaCorrelativo').val(),
	      		idusuario: $('#cboBusquedaTrabajador').val(),
	      		estado: $('#cboBusquedaEstado').val()
	      	}
	    })
	    .done(function(resultado){
	      	$('#divListadoVentas').html(resultado);
	    })
	}

	verListado();

	function NuevaVenta(){
		$.ajax({
	      	method: 'POST',
	      	url: 'vista/ventas_formulario.php',
	      	data:{
	      		'proceso': "NUEVO"
	      	}
	    })
	    .done(function(resultado){
	      	$('#divPrincipal').html(resultado);
	    })
	}



</script>