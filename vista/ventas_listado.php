<?php
require_once('../modelo/clsVenta.php');

$objVenta = new clsVenta();

$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$cliente = $_POST['nombre'];
$idtipocomprobante = $_POST['idtipocomprobante'];
$correlativo = $_POST['correlativo'];
$idusuario = $_POST['idusuario'];
$estado = $_POST['estado'];

$listaVenta = $objVenta->listarVenta($desde, $hasta, $cliente, $idtipocomprobante, $correlativo, $idusuario, $estado );
$listaVenta = $listaVenta->fetchAll(PDO::FETCH_NAMED);

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaVenta">
	<thead>
		<tr class="bg-primary">
			<th>COD</th>
			<th>FECHA</th>
			<th>DOCUMENTO</th>
			<th>CLIENTE</th>
			<th>IMPORTE</th>
			<th>PRODUCTOS</th>
			<th>ESTADO</th>
			<th>PDF</th>
			<th>TICKET</th>
			<th>SUNAT</th>
			<th>XML</th>
			<th>CDR</th>
			<th>EDITAR</th>
			<th>ANULAR</th>
			<th>ELIMINAR</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($listaVenta as $k=>$v){ 
			$estado= 'ACTIVO';
			$class = "";
			if($v['estado']==0){
				$estado= 'ANULADO';
				$class = "text-danger";
			}
			$documento = $v['comprobante'].' '.$v['serie'].'-'.$v['correlativo'];
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $v['idventa']; ?></td>
			<td><?php echo $v['fecha']; ?></td>
			<td><?php echo $documento; ?></td>
			<td><?php echo $v['cliente']; ?></td>
			<td><?php echo $v['total'] ?></td>
			<td><?php echo $v['producto'] ?></td>
			<td><?php echo $estado; ?></td>
			<td>
				<a class="btn btn-sm bg-primary" href="vista/pdfVenta.php?id=<?= $v['idventa'] ?>" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>
			</td>
			<td>
				<a class="btn btn-sm bg-maroon" href="vista/pdfTicket.php?id=<?= $v['idventa'] ?>" target="_blank"><i class="fa fa-file-pdf"></i> TICKET</a>
			</td>
			<td><?php echo $v['estadoSunat']; ?></td>
			<td>
				<a class="btn btn-sm bg-maroon" href="<?= $v['xml'] ?>" target="_blank"><i class="fa fa-file-pdf"></i> XML</a>
			</td>
			<td>
				<?= $v['cdr'] != "" ? '<a class="btn btn-sm bg-maroon" href="' . $v['cdr'] . '" target="_blank"><i class="fa fa-file-pdf"></i> CDR</a>' : 'Sin CDR' ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-info btn-sm" onclick="editarVenta(<?php echo $v['idventa']; ?>)"><i class="fa fa-edit"></i> Editar</button>
			</td>
			<td class="text-center">
				<?php if($v['estado']==1){ ?>
					<button type="button" class="btn btn-warning btn-sm" onclick="CambiarEstadoVenta(<?php echo $v['idventa']; ?>,0)"><i class="fa fa-trash"></i> Anular</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-success btn-sm" onclick="CambiarEstadoVenta(<?php echo $v['idventa']; ?>,1)"><i class="fa fa-check"></i> Activar</button>
				<?php } ?>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-danger btn-sm" onclick="CambiarEstadoVenta(<?php echo $v['idventa']; ?>,2)"><i class="fa fa-times"></i> Eliminar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
	
	function editarVenta(id){
		$.ajax({
	      	method: 'POST',
	      	url: 'vista/ventas_formulario.php',
	      	data:{
	      		'proceso': "EDITAR",
	      		'idventa': id
	      	}
	    })
	    .done(function(resultado){
	    	$('#divPrincipal').html(resultado);
	    })
	}

	function CambiarEstadoVenta(idventa, estado, documento=''){
	    proceso = estado==0?"ANULAR":(estado==1?"ACTIVAR":"ELIMINAR");
	    mensaje = "¿Esta seguro de <b>"+proceso+"</B> el comprobante <b>"+documento+"</b>?";
	    accion = "EjecutarCambiarEstadoVenta("+idventa+",'"+proceso+"')";
	    mostrarModalConfirmacion(mensaje, accion);
	}

	function EjecutarCambiarEstadoVenta(idventa,proceso){  
	    $.ajax({
	        method: "POST",
	        url: "controlador/contVenta.php",
	        data: {
	            'accion': proceso,
	            'idventa': idventa
	        }
	    }).done(function(resultado){
	        if(resultado==1){
	            toastCorrecto("Cambio de estado satisfactorio.");
	            verListado();
	        }else if(resultado==0){
	            toastError("Problemas en la actualización de estado. Inténtelo nuevamente.");
	        }else{
	            toastError(resultado);
	        }
	    });
	}

	$("#tablaVenta").DataTable({
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
    }).buttons().container().appendTo('#tablaVenta_wrapper .col-md-6:eq(0)');

</script>