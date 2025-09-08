<?php
require_once('../modelo/clsUsuario.php');

$objUsu = new clsUsuario();

$usuario = $_POST['nombre'];
$estado = $_POST['estado'];

$listaUsuario = $objUsu->listarUsuario($usuario, $estado);
$listaUsuario = $listaUsuario->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaUsuario">
	<thead>
		<tr class="bg-primary">
			<th>COD</th>
			<th>NOMBRE</th>
			<th>USUARIO</th>
			<th>PERFIL</th>
			<th>ESTADO</th>
			<th>EDITAR</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaUsuario as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idusuario']; ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $v['usuario']; ?></td>
			<td><?php echo $v['perfil']; ?></td>
			<td><?php echo $estado; ?></td>
			<td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarUsuario(<?php echo $v['idusuario']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="cambiarEstadoUsuario(<?php echo $v['idusuario']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="cambiarEstadoUsuario(<?php echo $v['idusuario']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="cambiarEstadoUsuario(<?php echo $v['idusuario']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarUsuario(id){
		$.ajax({
			method: 'POST',
			url: 'controlador/contUsuario.php',
			data:{
				'accion': 'CONSULTAR_USUARIO',
				'idusuario': id
			},
			dataType: 'json'
		})
		.done(function(resultado){
			$('#nombre').val(resultado.nombre);
			$('#estado').val(resultado.estado);
			$('#usuario').val(resultado.usuario);
			$('#idperfil').val(resultado.idperfil);
			$('#idusuario').val(resultado.idusuario);
			$('#modalUsuario').modal('show');
		})
	}

	function cambiarEstadoUsuario(idusuario, estado){
		proceso = new Array('ANULAR', 'ACTIVAR', 'ELIMINAR');
		mensaje = "¿Estás Seguro de "+proceso[estado]+" el Usuario?";
		accion = "EjecutarCambiarEstadoUsuario("+idusuario+","+estado+")";

		mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoUsuario(idusuario, estado){
		$.ajax({
			method: 'POST',
			url: 'controlador/contUsuario.php',
			data:{
				'accion': 'CAMBIAR_ESTADO_USUARIO',
				'idusuario': idusuario,
				'estado': estado
			},
			dataType: 'json'
		})
	.done(function(resultado){
		var feedback = '';
		if(resultado.correcto==1){
			feedback = '<div class="callout callout-success">'+resultado.mensaje+'</div>';
			verListado();
		}else{
			feedback = '<div class="callout callout-danger">'+resultado.mensaje+'</div>';
		}
		$("#tablaUsuario_wrapper").prepend(feedback);
		setTimeout(function(){ $(".callout").fadeOut(); }, 3000);
	})
	}

	$("#tablaUsuario").DataTable({
	  "responsive": true, 
	  "lengthChange": true, 
	  "autoWidth": false,
	  "ordering": true,
	  "searching": false,
	  "lengthMenu": [[10,25,50,100,-1],[10,25,50,100,'Todos']],
	  "language": {
			"decimal":        "",
			"emptyTable":     "Sin datos",
			"info":           "Del _START_ al _END_ de _TOTAL_ filas",
			"infoEmpty":      "Del 0 a 0 de 0 filas",
			"infoFiltered":   "(filtro de _MAX_ filas totales)",
			"infoPostFix":    "",
			"thousands":      ",",
			"lengthMenu":     "Ver _MENU_ filas",
			"loadingRecords": "Cargando...",
			"processing":     "Procesando...",
			"search":         "Buscar:",
			"zeroRecords":    "No se encontraron resultados",
			"paginate": {
				"first":      "Primero",
				"last":       "Ultimo",
				"next":       "Siguiente",
				"previous":   "Anterior"
			},
			"aria": {
				"sortAscending":  ": orden ascendente",
				"sortDescending": ": orden descendente"
			}
		},
	  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
	}).buttons().container().appendTo('#tablaUsuario_wrapper .col-md-6:eq(0)');

</script>