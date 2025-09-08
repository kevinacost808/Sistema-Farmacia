<?php
require_once('../modelo/clsPerfil.php');

$objPer = new clsPerfil();

$perfil = $_POST['nombre'];
$estado = $_POST['estado'];

$listaPerfil = $objPer->listarPerfil($perfil, $estado);
$listaPerfil = $listaPerfil->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaPerfil">
	<thead>
		<tr class="bg-primary">
			<th>COD</th>
			<th>DESCRIPCION</th>
			<th>ESTADO</th>
			<th>PERMISOS</th>
			<th>EDITAR</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaPerfil as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idperfil']; ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $estado; ?></td>
			<td class="text-center">
				<button type="button" class="btn bg-maroon btn-sm" onclick="asignarPermisos(<?php echo $v['idperfil']; ?>)"><i class="fa fa-lock"></i> Permisos</button>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarPerfil(<?php echo $v['idperfil']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="cambiarEstadoPerfil(<?php echo $v['idperfil']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="cambiarEstadoPerfil(<?php echo $v['idperfil']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="cambiarEstadoPerfil(<?php echo $v['idperfil']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarPerfil(id){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contPerfil.php',
	      	data:{
	      		'accion': 'CONSULTAR_PERFIL',
	      		'idperfil': id
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	$('#nombre').val(resultado.nombre);
	    	$('#estado').val(resultado.estado);
	    	$('#idperfil').val(resultado.idperfil);
	      	$('#modalPerfil').modal('show');
	    })
	}

	function cambiarEstadoPerfil(idperfil, estado){
		proceso = new Array('ANULAR', 'ACTIVAR', 'ELIMINAR');
		mensaje = "¿Estás Seguro de "+proceso[estado]+" el perfil?";
		accion = "EjecutarCambiarEstadoPerfil("+idperfil+","+estado+")";

		mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoPerfil(idperfil, estado){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contPerfil.php',
	      	data:{
	      		'accion': 'CAMBIAR_ESTADO_PERFIL',
	      		'idperfil': idperfil,
	      		'estado': estado
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	if(resultado.correcto==1){
	    		toastCorrecto(resultado.mensaje);
	    		verListado();
	    	}else{
	    		toastError(resultado.mensaje);
	    	}
	    })
	}

	$("#tablaPerfil").DataTable({
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
    }).buttons().container().appendTo('#tablaPerfil_wrapper .col-md-6:eq(0)');

    function asignarPermisos(id){
    	$('#formPerfil_permiso').trigger('reset');
    	$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contPerfil.php',
	      	data:{
	      		'accion': 'CONSULTAR_ACCESO',
	      		'idperfil': id
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	console.log(resultado);
	    	if(resultado.length>0){
	    		for(i=0; i<resultado.length; i++){
	    			$('#permiso'+resultado[i].idopcion).prop('checked',true);
	    		}
	    	}
	    	$('#idperfil_permiso').val(id);
	      	$('#modalPerfil_permisos').modal('show');
	    })
    	
    }

    function verificarPermiso(idopcion){
    	asignar = 0;
    	if($('#permiso'+idopcion).is(':checked')){
    		asignar = 1;
    	}

    	$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contPerfil.php',
	      	data:{
	      		'accion': 'VERIFICAR_ACCESO',
	      		'idopcion': idopcion,
	      		'idperfil': $('#idperfil_permiso').val(),
	      		'estado': asignar
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	if(resultado.correcto==1){
	    		toastCorrecto(resultado.mensaje);
	    	}else{
	    		toastError(resultado.mensaje);
	    	}
	    })
    }

</script>