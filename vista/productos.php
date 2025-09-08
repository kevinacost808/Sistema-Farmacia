<?php
	require_once('../modelo/clsCategoria.php');
	require_once('../modelo/clsProducto.php');

	$objCat = new clsCategoria();
	$objPro = new clsProducto();

	$listaCategoria = $objCat->listarCategoria('',1);
	$listaCategoria = $listaCategoria->fetchAll(PDO::FETCH_NAMED);

	$listaUnidad = $objPro->consultarUnidad();
	$listaUnidad = $listaUnidad->fetchAll(PDO::FETCH_NAMED);

	$listaAfectacion = $objPro->consultarAfectacion();
	$listaAfectacion = $listaAfectacion->fetchAll(PDO::FETCH_NAMED);

?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Listado de Productos</h3>
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
						<button type="button" class="btn btn-success" onclick="openModalProducto()"><i class="fa fa-plus"></i> Nuevo</button>
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
<div class="modal fade" id="modalProducto">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Formulario Producto</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formProducto" id="formProducto">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="nombre">Codigo</label>
								<input type="text" class="form-control" name="codigobarra" id="codigobarra">
							</div>
							<div class="form-group">
								<label for="nombre">Producto</label>
								<input type="text" class="form-control" name="nombre" id="nombre">
								<input type="hidden" class="form-control" name="idproducto" id="idproducto">
							</div>
							<div class="form-group">
								<label for="idunidad">Unidad</label>
								<select class="form-control" name="idunidad" id="idunidad">
									<option value="">- Seleccione -</option>
									<?php foreach($listaUnidad as $k=>$v){ ?>
									<option value="<?php echo $v['idunidad']; ?>"><?php echo $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label for="idcategoria">Categoria</label>
								<select class="form-control" name="idcategoria" id="idcategoria">
									<option value="">- Seleccione -</option>
									<?php foreach($listaCategoria as $k=>$v){ ?>
									<option value="<?php echo $v['idcategoria']; ?>"><?php echo $v['nombre'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="pventa">Precio Venta</label>
								<input type="number" step="0.01" class="form-control" name="pventa" id="pventa">
							</div>
							<div class="form-group">
								<label for="pcompra">Precio Compra</label>
								<input type="number" step="0.01" class="form-control" name="pcompra" id="pcompra">
							</div>
							<div class="form-group">
								<label for="stock">Stock</label>
								<input type="number" step="0.01" class="form-control" name="stock" id="stock">
							</div>
							<div class="form-group">
								<label for="stock">Stock Seguridad</label>
								<input type="stockseguridad" step="0.01" class="form-control" name="stockseguridad" id="stockseguridad">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="idafectacion">Afectación</label>
								<select class="form-control" name="idafectacion" id="idafectacion">
									<option value="">- Seleccione -</option>
									<?php foreach($listaAfectacion as $k=>$v){ ?>
									<option value="<?php echo $v['idafectacion']  ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label for="afectoicbper">¿Afecto al ICBPER?</label>
								<select class="form-control" name="afectoicbper" id="afectoicbper">
									<option value="1">SI</option>
									<option value="0" selected>NO</option>
								</select>
							</div>
							<div class="form-group">
								<label for="estado">Estado</label>
								<select class="form-control" name="estado" id="estado">
									<option value="1">ACTIVO</option>
									<option value="0">ANULADO</option>
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
				<button type="button" class="btn btn-success" onclick="registrarProducto()"><i class="fa fa-save"></i> Registrar</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<div class="modal fade" id="modalProducto_Imagen">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Subir Imagen</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formProducto_imagen" id="formProducto_imagen" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="nombre_imagen">Producto</label>
								<input type="text" class="form-control" name="nombre_imagen" id="nombre_imagen">
								<input type="hidden" class="form-control" name="idproducto_imagen" id="idproducto_imagen">
							</div>
							<div class="form-group">
								<label for="url_imagen">Imagen</label>
								<input type="text" readonly class="form-control" name="url_imagen" id="url_imagen">
							</div>
							<input name="uploadFile" id="uploadFile" class="file-loading" type="file" multiple data-min-file-count="1">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
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
			url: 'vista/productos_listado.php',
			data:{
				nombre: $('#txtBusquedaNombre').val(),
				codigo: $('#txtBusquedaCodigo').val(),
				idcategoria: $('#cboBusquedaCategoria').val(),
				estado: $('#cboBusquedaEstado').val()
			}
		})
		.done(function(resultado){
			$('#divListadoProducto').html(resultado);
		})
	}

	verListado();

	function openModalProducto(){
		$('#idproducto').val("");
		$('#formProducto').trigger('reset');
		$('#modalProducto').modal('show');
	}

	function registrarProducto(){

		var datax = $('#formProducto').serializeArray();
		var idproducto = $('#idproducto').val();

		if(idproducto!=""){
			datax.push({name: "accion", value: "ACTUALIZAR" });
		}else{
			datax.push({name: "accion", value: "NUEVO" });
		}
		$.ajax({
			method: 'POST',
			url: 'controlador/contProducto.php',
			data: datax,
			dataType: 'json'
		})
	.done(function(respuesta){
		var feedback = '';
		if(respuesta.correcto==1){
			feedback = '<div class="callout callout-success">'+respuesta.mensaje+'</div>';
			$('#modalProducto').modal('hide');
			$('#formProducto').trigger('reset');
			verListado();
		}else{
			feedback = '<div class="callout callout-danger">'+respuesta.mensaje+'</div>';
		}
		$("#divListadoProducto").prepend(feedback);
		setTimeout(function(){ $(".callout").fadeOut(); }, 3000);
	})
	}

</script>