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
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1><i class="fa fa-coins text-success"></i> Reporte Mensual de Utilidades</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-outline card-primary shadow">
					<div class="card-header bg-primary">
						<h3 class="card-title"><i class="fa fa-filter"></i> Filtros de búsqueda</h3>
					</div>
					<div class="card-body">
						<form class="form-row align-items-end" onsubmit="verListadoUtilidad(); return false;">
							<div class="form-group col-md-3">
								<label for="txtBusquedaFechaDesdeUtil">Desde</label>
								<input type="date" class="form-control" name="txtBusquedaFechaDesdeUtil" id="txtBusquedaFechaDesdeUtil" value="<?= date('Y-m-01'); ?>">
							</div>
							<div class="form-group col-md-3">
								<label for="txtBusquedaFechaHastaUtil">Hasta</label>
								<input type="date" class="form-control" name="txtBusquedaFechaHastaUtil" id="txtBusquedaFechaHastaUtil">
							</div>
							<div class="form-group col-md-3">
								<label for="cboBusquedaComprobanteUtil">Comprobante</label>
								<select class="form-control" name="cboBusquedaComprobanteUtil" id="cboBusquedaComprobanteUtil">
									<option value="">- Todos -</option>
									<?php foreach($listaComprobante as $k=>$v){ ?>
									<option value="<?= $v['idtipocomprobante'] ?>"><?php echo $v['nombre']; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label for="cboBusquedaTrabajadorUtil">Trabajador</label>
								<select class="form-control" name="cboBusquedaTrabajadorUtil" id="cboBusquedaTrabajadorUtil">
									<option value="">- Todos -</option>
									<?php foreach($listaUsuario as $k=>$v){ ?>
									<option value="<?= $v['idusuario'] ?>"><?php echo $v['nombre']; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-md-12 mt-2">
								<button type="button" class="btn btn-primary btn-block" onclick="verListadoUtilidad()"><i class="fa fa-search"></i> Buscar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-outline card-success shadow">
					<div class="card-header bg-success">
						<h3 class="card-title"><i class="fa fa-table"></i> Resultados</h3>
					</div>
					<div class="card-body">
						<div id="contenedorListadoUtilidad"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
function verListadoUtilidad(){
	var desde = $("#txtBusquedaFechaDesdeUtil").val();
	var hasta = $("#txtBusquedaFechaHastaUtil").val();
	var comprobante = $("#cboBusquedaComprobanteUtil").val();
	var usuario = $("#cboBusquedaTrabajadorUtil").val();
	$.post('vista/reporte_utilidades_listado.php', {
		desde: desde,
		hasta: hasta,
		idtipocomprobante: comprobante,
		idusuario: usuario
	}, function(data){
		$("#contenedorListadoUtilidad").html(data);
	});
}
$(function(){
	verListadoUtilidad();
});
</script>
