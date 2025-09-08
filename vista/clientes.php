<?php
require_once('../modelo/clsCliente.php');
$objCli = new clsCliente();

$listaTipoDoc = $objCli->listaTipoDocumento();
$listaTipoDoc = $listaTipoDoc->fetchAll(PDO::FETCH_NAMED);

?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Listado de Clientes</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Tipo Doc</span>
							</div>
							<select class="form-control" name="cboBusquedaTipoDoc" id="cboBusquedaTipoDoc" onchange="verListado()">
								<option value="">- Todos -</option>
								<?php foreach($listaTipoDoc as $k=>$v){ ?>
								<option value="<?php echo $v['idtipodocumento']; ?>"><?php echo $v['nombre']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">DNI/RUC</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaDocumento" id="txtBusquedaDocumento" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Nombre</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaNombre" id="txtBusquedaNombre" onkeyup="if(event.keyCode=='13'){ verListado(); }">
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
						<button type="button" class="btn btn-success" onclick="openModalCliente()"><i class="fa fa-plus"></i> Nuevo</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card card-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" id="divListadoCliente">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalCliente">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Formulario Cliente</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formCliente" id="formCliente">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<input type="hidden" class="form-control" name="idcliente" id="idcliente">
								<label for="idtipodocumento">Tipo de Documento</label>
								<select class="form-control" name="idtipodocumento" id="idtipodocumento">
									<option value="">- Seleccione -</option>
									<?php foreach($listaTipoDoc as $k=>$v){ ?>
										<option value="<?php echo $v['idtipodocumento']; ?>"><?php echo $v['nombre']; ?></option>
									<?php } ?>
								</select>

							</div>
							<div class="form-group">
								<label for="nombre">Numero de Documento</label>
								<div class="input-group mb-3">
									<input type="text" class="form-control" name="nrodocumento" id="nrodocumento">
									<div class="input-group-append">
										<span type="button" class="input-group-text" onclick="consultarDatoCliente()"><i class="fas fa-search"></i></span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="direccion">Direccion</label>
								<textarea class="form-control" name="direccion" id="direccion" style="height: 123px;"></textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="nombre">Nombre</label>
								<input type="text" class="form-control" name="nombre" id="nombre">
							</div>
							<div class="form-group" style="display: none;">
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
				<button type="button" class="btn btn-success" onclick="registrarCliente()"><i class="fa fa-save"></i> Registrar</button>
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
	      	url: 'vista/clientes_listado.php',
	      	data:{
	      		nombre: $('#txtBusquedaNombre').val(),
	      		estado: $('#cboBusquedaEstado').val(),
	      		idtipodocumento: $('#cboBusquedaTipoDoc').val(),
	      		documento: $('#txtBusquedaDocumento').val()
	      	}
	    })
	    .done(function(resultado){
	      	$('#divListadoCliente').html(resultado);
	    })
	}

	verListado();

	function openModalCliente(){
		$('#idcliente').val("");
		$('#formCliente').trigger('reset');
		$('#modalCliente').modal('show');
	}

	function registrarCliente(){

		var datax = $('#formCliente').serializeArray();
		var idcliente = $('#idcliente').val();

		if(idcliente!=""){
			datax.push({name: "accion", value: "ACTUALIZAR" });
		}else{
			datax.push({name: "accion", value: "NUEVO" });
		}
		$.ajax({
	        method: 'POST',
	        url: 'controlador/contCliente.php',
	        data: datax,
	        dataType: 'json'
	    })
	    .done(function(respuesta){
	        if(respuesta.correcto==1){
	        	toastCorrecto(respuesta.mensaje);
	        	$('#modalCliente').modal('hide');
	        	$('#formCliente').trigger('reset');
	        	verListado();
	        }else{
	         	toastError(respuesta.mensaje);
	        }

	    })
	}

	function consultarDatoCliente(){
		$('#formCliente').LoadingOverlay("show");
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contCliente.php',
	      	data:{
	      		accion: 'CONSULTAR_DATOS_WS',
	      		idtipodocumento: $('#idtipodocumento').val(),
	      		nrodocumento: $('#nrodocumento').val()
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	$('#formCliente').LoadingOverlay("hide");
	      	$('#nombre').val(resultado.nombre);
	      	$('#direccion').val(resultado.direccion);
	    })
	}

</script>