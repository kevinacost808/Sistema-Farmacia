<?php
require_once('../modelo/clsCliente.php');

$objCli = new clsCliente();

$nombre = $_POST['nombre'];
$estado = $_POST['estado'];
$idtipodocumento = $_POST['idtipodocumento'];
$documento = $_POST['documento'];

$listaCliente = $objCli->listarCliente($nombre, $estado, $idtipodocumento, $documento);
$listaCliente = $listaCliente->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaCliente">
	<thead>
		<tr class="bg-primary">
			<th>COD</th>
			<th>NOMBRE</th>
			<th>NRO DOC.</th>
			<th>DIRECCION</th>
			<th>ESTADO</th>
			<th>EDITAR</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaCliente as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idcliente']; ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $v['nrodocumento']; ?></td>
			<td><?php echo $v['direccion']; ?></td>
			<td><?php echo $estado; ?></td>
			<td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarCliente(<?php echo $v['idcliente']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="cambiarEstadoCliente(<?php echo $v['idcliente']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="cambiarEstadoCliente(<?php echo $v['idcliente']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="cambiarEstadoCliente(<?php echo $v['idcliente']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarCliente(id){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contCliente.php',
	      	data:{
	      		'accion': 'CONSULTAR_CLIENTE',
	      		'idcliente': id
	      	},
	      	dataType: 'json'
	    })
	    .done(function(resultado){
	    	$('#nombre').val(resultado.nombre);
	    	$('#estado').val(resultado.estado);
	    	$('#idcliente').val(resultado.idcliente);
	    	$('#idtipodocumento').val(resultado.idtipodocumento);
	    	$('#nrodocumento').val(resultado.nrodocumento);
	    	$('#direccion').val(resultado.direccion);
	      	$('#modalCliente').modal('show');
	    })
	}

	function cambiarEstadoCliente(idcliente, estado){
		proceso = new Array('ANULAR', 'ACTIVAR', 'ELIMINAR');
		mensaje = "¿Estás Seguro de "+proceso[estado]+" el cliente?";
		accion = "EjecutarCambiarEstadoCliente("+idcliente+","+estado+")";

		mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoCliente(idcliente, estado){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contCliente.php',
	      	data:{
	      		'accion': 'CAMBIAR_ESTADO_CLIENTE',
	      		'idcliente': idcliente,
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

	$("#tablaCliente").DataTable({
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
    }).buttons().container().appendTo('#tablaCliente_wrapper .col-md-6:eq(0)');

</script>