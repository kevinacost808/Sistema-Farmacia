<?php
require_once('../modelo/clsPerfil.php');

$objPer = new clsPerfil();

$arrayPerfil = $objPer->listarPerfil('',1);
$arrayPerfil = $arrayPerfil->fetchAll(PDO::FETCH_NAMED);

?>
<section class="content-header">
	<div class="container-fluid">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Listado de Usuarios</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Nombre</span>
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
						<button type="button" class="btn btn-success" onclick="openModalUsuario()"><i class="fa fa-plus"></i> Nuevo</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card card-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" id="divListadoUsuario">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalUsuario">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h4 class="modal-title">Formulario Usuario</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="formUsuario" id="formUsuario">
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
							<div class="form-group">
								<label for="nombre">Nombre</label>
								<input type="text" class="form-control" name="nombre" id="nombre">
								<input type="hidden" class="form-control" name="idusuario" id="idusuario">
							</div>
							<div class="form-group">
								<label for="usuario">Usuario</label>
								<input type="text" class="form-control" name="usuario" id="usuario">
							</div>
							<div class="form-group">
								<label for="usuario">Clave</label>
								<input type="password" class="form-control" name="clave" id="clave" autocomplete="off">
							</div>
							<div class="form-group">
								<label for="idperfil">Perfil</label>
								<select class="form-control" name="idperfil" id="idperfil">
									<option value="">- SELECCIONE -</option>
									<?php foreach($arrayPerfil as $k=>$v){ ?>
									<option value="<?php echo $v['idperfil']; ?>"><?php echo $v['nombre']; ?></option>
									<?php } ?>
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
				<button type="button" class="btn btn-success" onclick="registrarUsuario()"><i class="fa fa-save"></i> Registrar</button>
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
			url: 'vista/usuarios_listado.php',
			data:{
				nombre: $('#txtBusquedaNombre').val(),
				estado: $('#cboBusquedaEstado').val()
			}
		})
		.done(function(resultado){
			$('#divListadoUsuario').html(resultado);
		})
	}

	verListado();

	function openModalUsuario(){
		$('#idusuario').val("");
		$('#formUsuario').trigger('reset');
		$('#modalUsuario').modal('show');
	}

	function registrarUsuario(){

		var datax = $('#formUsuario').serializeArray();
		var idusuario = $('#idusuario').val();

		if(idusuario!=""){
			datax.push({name: "accion", value: "ACTUALIZAR" });
		}else{
			datax.push({name: "accion", value: "NUEVO" });
		}
		$.ajax({
			method: 'POST',
			url: 'controlador/contUsuario.php',
			data: datax,
			dataType: 'json'
		})
	.done(function(respuesta){
		var feedback = '';
		if(respuesta.correcto==1){
			feedback = '<div class="callout callout-success">'+respuesta.mensaje+'</div>';
			$('#modalUsuario').modal('hide');
			$('#formUsuario').trigger('reset');
			verListado();
		}else{
			feedback = '<div class="callout callout-danger">'+respuesta.mensaje+'</div>';
		}
		$("#divListadoUsuario").prepend(feedback);
		setTimeout(function(){ $(".callout").fadeOut(); }, 3000);
	})
	}

</script>