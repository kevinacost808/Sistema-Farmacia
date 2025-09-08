<?php
require_once('../modelo/clsCategoria.php');

$objCat = new clsCategoria();

$categoria = $_POST['nombre'];
$estado = $_POST['estado'];

$listaCategoria = $objCat->listarCategoria($categoria, $estado);
$listaCategoria = $listaCategoria->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaCategoria">
	<thead>
		<tr class="bg-primary">
			<th>COD</th>
			<th>DESCRIPCION</th>
			<th>ESTADO</th>
			<th>EDITAR</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaCategoria as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idcategoria']; ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $estado; ?></td>
			<td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarCategoria(<?php echo $v['idcategoria']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="cambiarEstadoCategoria(<?php echo $v['idcategoria']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="cambiarEstadoCategoria(<?php echo $v['idcategoria']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="cambiarEstadoCategoria(<?php echo $v['idcategoria']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarCategoria(id){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contCategoria.php',
	      	data:{
	      		'accion': 'CONSULTAR_CATEGORIA',
	      		'idcategoria': id
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	$('#nombre').val(resultado.nombre);
	    	$('#estado').val(resultado.estado);
	    	$('#idcategoria').val(resultado.idcategoria);
	      	$('#modalCategoria').modal('show');
	    })
	}

	function cambiarEstadoCategoria(idcategoria, estado){
		proceso = new Array('ANULAR', 'ACTIVAR', 'ELIMINAR');
		mensaje = "¿Estás Seguro de "+proceso[estado]+" la categoria?";
		accion = "EjecutarCambiarEstadoCategoria("+idcategoria+","+estado+")";

		mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoCategoria(idcategoria, estado){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contCategoria.php',
	      	data:{
	      		'accion': 'CAMBIAR_ESTADO_CATEGORIA',
	      		'idcategoria': idcategoria,
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

	$("#tablaCategoria").DataTable({
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
    }).buttons().container().appendTo('#tablaCategoria_wrapper .col-md-6:eq(0)');

</script>