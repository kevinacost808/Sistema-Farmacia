<?php
require_once('../modelo/clsProducto.php');

$objPro = new clsProducto();

$producto = $_POST['nombre'];
$estado = $_POST['estado'];
$codigo = $_POST['codigo'];
$idcategoria = $_POST['idcategoria'];
$filtro = $_POST['filtro'];

$listaProducto = $objPro->listarProducto($producto, $estado, $codigo, $idcategoria, $filtro);
$listaProducto = $listaProducto->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaProductoInventario">
	<thead>
		<tr class="bg-primary">
			<th>ID</th>
			<th>CODIGO</th>
			<th>PRODUCTO</th>
			<th>UNIDAD</th>
			<th class="bg-maroon">STOCK</th>
			<th class="bg-maroon">STOCK SEGURIDAD</th>
			<th>FALTANTE</th>
			<th>ESTADO</th>
			<th>KARDEX</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaProducto as $k=>$v){ 
			$class = "";
			$icono = 'fa fa-thumbs-down text-danger';
			if($v['stock']>=$v['stockseguridad']){
				$icono = 'fa fa-thumbs-up text-success';
			}

			$cantidad_faltante = 0;
			if($v['stockseguridad']>$v['stock']){
				$cantidad_faltante = $v['stockseguridad'] - $v['stock'];
			}
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idproducto']; ?></td>
			<td><?php echo $v['codigobarra'] ?></td>
			<td><?php echo $v['nombre']; ?></td>
			<td><?php echo $v['unidad'] ?></td>
			<td class="text-bold text-right bg-maroon"><?php echo $v['stock'] ?></td>
			<td class="text-bold text-right bg-maroon"><?php echo $v['stockseguridad'] ?></td>
			<td class="text-bold text-right"><?= ($cantidad_faltante>0)? $cantidad_faltante : ''; ?></td>
			<td class="text-center">
				<i class="<?= $icono ?> fa-2x"></i>
			</td>
			<td>
				<button type="button" class="btn btn-info btn-sm" onclick="kardexProducto(<?= $v['idproducto'] ?>)"><i class="fa fa-eye"> Kardex</i> </button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>

	$("#tablaProductoInventario").DataTable({
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
    }).buttons().container().appendTo('#tablaProductoInventario_wrapper .col-md-6:eq(0)');


    function kardexProducto(id){
		$.ajax({
	      	method: 'POST',
	      	url: 'controlador/contProducto.php',
	      	data:{
	      		'accion': 'DETALLE_VENTA_PRODUCTO',
	      		'idproducto': id
	      	}
	    })
	    .done(function(resultado){
	    	$('#tablakardex').html(resultado);
	      	$('#modalKardex').modal('show');
	    })
	}

</script>