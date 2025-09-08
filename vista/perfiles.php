<?php
require_once('../modelo/clsPerfil.php');

$objPer = new clsPerfil();

$listaOpciones = $objPer->listarOpcion();
$listaOpciones = $listaOpciones->fetchAll(PDO::FETCH_NAMED);
?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Listado de Perfiles</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Perfil</span>
							</div>
							<input type="text" class="form-control" name="txtBusquedaNombre" id="txtBusquedaNombre" onkeyup="if(event.keyCode=='13'){ verListado(); }">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group mb-3">
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
						<button type="button" class="btn btn-success" onclick="openModalPerfil()"><i class="fa fa-plus"></i> Nuevo</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card card-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" id="divListadoPerfil">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalPerfil">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Formulario Perfil</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formPerfil" id="formPerfil">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="nombre">Perfil</label>
								<input type="text" class="form-control" name="nombre" id="nombre">
								<input type="hidden" class="form-control" name="idperfil" id="idperfil">
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
				<button type="button" class="btn btn-success" onclick="registrarPerfil()"><i class="fa fa-save"></i> Registrar</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<div class="modal fade" id="modalPerfil_permisos">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Permisos</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formPerfil_permiso" id="formPerfil_permiso">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-sm table-hover table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>Opcion Sistema
											<input type="hidden" class="form-control" name="idperfil_permiso" id="idperfil_permiso">
										</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($listaOpciones as $k=>$v){ ?>
									<tr>
										<td>
											<input type="checkbox" name="permiso<?php echo $v['idopcion']; ?>" id="permiso<?php echo $v['idopcion']; ?>" onclick="verificarPermiso(<?php echo $v['idopcion']; ?>)">
										</td>
										<td><?php echo $v['descripcion']; ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</form>
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
	      	url: 'vista/perfiles_listado.php',
	      	data:{
	      		nombre: $('#txtBusquedaNombre').val(),
	      		estado: $('#cboBusquedaEstado').val()
	      	}
	    })
	    .done(function(resultado){
	      	$('#divListadoPerfil').html(resultado);
	    })
	}

	verListado();

	function openModalPerfil(){
		$('#idperfil').val("");
		$('#formPerfil').trigger('reset');
		$('#modalPerfil').modal('show');
	}

	function registrarPerfil(){

		var datax = $('#formPerfil').serializeArray();
		var idperfil = $('#idperfil').val();

		if(idperfil!=""){
			datax.push({name: "accion", value: "ACTUALIZAR" });
		}else{
			datax.push({name: "accion", value: "NUEVO" });
		}
		$.ajax({
	        method: 'POST',
	        url: 'controlador/contPerfil.php',
	        data: datax,
	        dataType: 'json'
	    })
	    .done(function(respuesta){
	        if(respuesta.correcto==1){
	        	toastCorrecto(respuesta.mensaje);
	        	$('#modalPerfil').modal('hide');
	        	$('#formPerfil').trigger('reset');
	        	verListado();
	        }else{
	         	toastError(respuesta.mensaje);
	        }

	    })
	}

</script>