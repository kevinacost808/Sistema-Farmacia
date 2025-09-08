<?php
	require_once('../modelo/clsCategoria.php');
	require_once('../modelo/clsProducto.php');

	$objCat = new clsCategoria();
	$objPro = new clsProducto();

	$listaCategoria = $objCat->listarCategoria('',1);
	$listaCategoria = $listaCategoria->fetchAll(PDO::FETCH_NAMED);

	$listaUnidad = $objPro->consultarUnidad();
	$listaUnidad = $listaUnidad->fetchAll(PDO::FETCH_NAMED);

?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Inventario de Productos</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Codigo</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaCodigo" id="txtBusquedaCodigo" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Producto</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaNombre" id="txtBusquedaNombre" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Categorias</span>
							</div>
							<select class="form-control" name="cboBusquedaCategoria" id="cboBusquedaCategoria" onchange="verListado()">
								<option value="">- Todos -</option>
								<?php foreach($listaCategoria as $k=>$v){ ?>
									<option value="<?php echo $v['idcategoria'] ?>"><?php echo $v['nombre'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Filtrar</span>
							</div>
							<select class="form-control" name="cboBusquedaFiltro" id="cboBusquedaFiltro" onchange="verListado()">
								<option value="">- Todos -</option>
								<option value="PCS">Productos con Stock</option>
								<option value="PSS">Productos sin Stock</option>
								<option value="PSM">Productos con Stock < Stock de Seguridad</option>
							</select>
						</div>
					</div>
					<div class="col-md-4" style="display:none;">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Estado</span>
							</div>
							<select class="form-control" name="cboBusquedaEstado" id="cboBusquedaEstado" onchange="verListado()">
								<option value="">- Todos -</option>
								<option value="1" selected>Activos</option>
								<option value="0">Anulados</option>
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
					<div class="col-md-12" id="divListadoProducto">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalKardex">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Kardex Producto</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 table-responsive" id="tablakardex">
						
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
	function verListado(){
		$.ajax({
	      	method: 'POST',
	      	url: 'vista/inventario_listado.php',
	      	data:{
	      		nombre: $('#txtBusquedaNombre').val(),
	      		codigo: $('#txtBusquedaCodigo').val(),
	      		idcategoria: $('#cboBusquedaCategoria').val(),
	      		estado: $('#cboBusquedaEstado').val(),
	      		filtro: $('#cboBusquedaFiltro').val()
	      	}
	    })
	    .done(function(resultado){
	      	$('#divListadoProducto').html(resultado);
	    })
	}

	verListado();

</script>