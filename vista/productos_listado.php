<?php
require_once('../modelo/clsProducto.php');

$objPro = new clsProducto();

$producto = $_POST['nombre'];
$estado = $_POST['estado'];
$codigo = $_POST['codigo'];
$idcategoria = $_POST['idcategoria'];

$listaProducto = $objPro->listarProducto($producto, $estado, $codigo, $idcategoria);
$listaProducto = $listaProducto->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaProducto">
	<thead>
		<tr class="bg-primary">
			<th>OPCIONES</th>
			<th>#</th>
			<th>IMAGEN</th>
			<th>CODIGO</th>
			<th>PRODUCTO</th>
			<th>UNIDAD</th>
			<th>CATEGORIA</th>
			<th>P.VENTA</th>
			<th>ESTADO</th>
			<!-- <th>EDITAR</th>
			<th>IMAGEN</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th> -->
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaProducto as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td class="text-center">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn bg-maroon dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-cogs"></i>
					</button>
					<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
						<a class="dropdown-item" href="#" onclick="subirImagen(<?php echo $v['idproducto']; ?>)"><i class="fa fa-image"></i> Subir Imagen</a>
						<a class="dropdown-item" href="#" onclick="editarProducto(<?php echo $v['idproducto']; ?>)"><i class="fa fa-edit"></i> Editar</a>
						<?php if($v['estado']==1){ ?>
						<a class="dropdown-item" href="#" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,0)"><i class="fa fa-trash"></i> Anular</a>
						<?php }else{ ?>
						<a class="dropdown-item" href="#" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,1)"><i class="fa fa-check"></i> Activar</a>
						<?php } ?>
						<a class="dropdown-item" href="#" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,2)"><i class="fa fa-times"></i> Eliminar</a>
					</div>
				</div>
			</td>
			<td><?php echo $v['idproducto']; ?></td>
			<td class="text-center">
				<img src="<?php echo $v['urlimagen'] ?>" style="width: 40px; height: 40px;">
			</td>
			<td><?php echo $v['codigobarra'] ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $v['unidad'] ?></td>
			<td><?php echo $v['categoria'] ?></td>
			<td><?php echo $v['pventa'] ?></td>
			<td><?php echo $estado; ?></td>
			<!-- <td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarProducto(<?php echo $v['idproducto']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td>
				<button type="button" class="btn btn-primary btn-sm" onclick="editarProducto(<?php echo $v['idproducto']; ?>)"><i class="fa fa-image"></i> Subir</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="cambiarEstadoProducto(<?php echo $v['idproducto']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td> -->
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarProducto(id){
		$.ajax({
			method: 'POST',
			url: 'controlador/contProducto.php',
			data:{
				'accion': 'CONSULTAR_PRODUCTO',
				'idproducto': id
			},
			dataType: 'json'
		})
		.done(function(resultado){
			$('#nombre').val(resultado.nombre);
			$('#estado').val(resultado.estado);
			$('#idproducto').val(resultado.idproducto);
			$('#codigobarra').val(resultado.codigobarra);
			$('#pventa').val(resultado.pventa);
			$('#pcompra').val(resultado.pcompra);
			$('#stock').val(resultado.stock);
			$('#idunidad').val(resultado.idunidad);
			$('#idcategoria').val(resultado.idcategoria);
			$('#idafectacion').val(resultado.idafectacion);
			$('#afectoicbper').val(resultado.afectoicbper);
			$('#stockseguridad').val(resultado.stockseguridad);
			$('#modalProducto').modal('show');
		})
	}

	function cambiarEstadoProducto(idproducto, estado){
		proceso = new Array('ANULAR', 'ACTIVAR', 'ELIMINAR');
		mensaje = "¿Estás Seguro de "+proceso[estado]+" el Producto?";
		accion = "EjecutarCambiarEstadoProducto("+idproducto+","+estado+")";

		mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoProducto(idproducto, estado){
		$.ajax({
			method: 'POST',
			url: 'controlador/contProducto.php',
			data:{
				'accion': 'CAMBIAR_ESTADO_PRODUCTO',
				'idproducto': idproducto,
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
		$("#tablaProducto_wrapper").prepend(feedback);
		setTimeout(function(){ $(".callout").fadeOut(); }, 3000);
	})
	}

	$("#tablaProducto").DataTable({
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
	}).buttons().container().appendTo('#tablaProducto_wrapper .col-md-6:eq(0)');

	function subirImagen(id){
		$.ajax({
			method: 'POST',
			url: 'controlador/contProducto.php',
			data:{
				'accion': 'CONSULTAR_PRODUCTO',
				'idproducto': id
			},
			dataType: 'json'
		})
		.done(function(resultado){
			$('#nombre_imagen').val(resultado.nombre);
			$('#url_imagen').val(resultado.urlimagen);
			$('#idproducto_imagen').val(resultado.idproducto);

			$("#uploadFile").fileinput({
				language: 'es',
				showRemove: false,
				uploadAsync: true,
				uploadExtraData: {
					accion: 'SUBIR_IMAGEN', 
					idproducto: $('#idproducto_imagen').val()
				},
				uploadUrl: 'controlador/contProducto.php',
				maxFileCount: 1,
				autoReplace: true, 
				allowedFileExtensions: ['jpg','png']
			}).on('fileuploaded', function(event, data, id, index) {
				$('#modalProducto_Imagen').modal('hide');
				verListado();
				$("#uploadFile").fileinput('destroy');
			});


			$('#modalProducto_Imagen').modal('show');
		})
	}
</script>