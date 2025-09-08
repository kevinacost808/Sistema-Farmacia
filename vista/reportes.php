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
				<h3 class="card-title">Reporte Mensual de Ventas</h3>
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
						<button type="button" class="btn btn-primary" onclick="verListado()"><i class="fa fa-search"></i> Buscar</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card card-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" id="divListadoReporte">
						
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
	      	url: 'vista/reportes_listado.php',
	      	data:{
	      		desde: $('#txtBusquedaFechaDesde').val(),
	      		hasta: $('#txtBusquedaFechaHasta').val(),
	      		idtipocomprobante: $('#cboBusquedaComprobante').val(),
	      		idusuario: $('#cboBusquedaTrabajador').val()
	      	}
	    })
	    .done(function(resultado){
	      	$('#divListadoReporte').html(resultado);
	    })
	}

	verListado();


</script>