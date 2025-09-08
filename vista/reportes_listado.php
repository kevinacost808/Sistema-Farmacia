<?php
require_once('../modelo/clsVenta.php');

$objVenta = new clsVenta();

$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$idtipocomprobante = $_POST['idtipocomprobante'];
$idusuario = $_POST['idusuario'];

$meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

$ventaspormes = $objVenta->reporteVentaPorMes($desde, $hasta, $idtipocomprobante, $idusuario);
$ventaspormes = $ventaspormes->fetchAll(PDO::FETCH_NAMED);

$label = array();
$datos = array();

foreach($ventaspormes as $k=>$v){
	$label[] = $meses[$v['mes']-1].'-'.$v['anio'];
	$datos[] = $v['total'];
}

?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaventaXmes">
	<thead>
		<tr class="bg-primary">
			<th>AÑO</th>
			<th>MES</th>
			<th>TOTAL</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($ventaspormes as $k=>$v){ 
		?>
		<tr>
			<td><?= $v['anio'] ?></td>
			<td><?= $meses[$v['mes']-1] ?></td>
			<td class="text-right"><?= $v['total'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!-- BAR CHART -->
<div class="card card-success">
	<div class="card-body">
		<div class="chart">
			<canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
		</div>
	</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
<script>

	$("#tablaventaXmes").DataTable({
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
    }).buttons().container().appendTo('#tablaventaXmes_wrapper .col-md-6:eq(0)');

	var areaChartData = {
      labels  : [<?php echo "'".implode("','", $label)."'"; ?>],
      datasets: [
        {
          label               : 'Ventas',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php echo implode(",", $datos); ?>]
        }
      ]
    }

	//-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, areaChartData)
    var temp0 = areaChartData.datasets[0]
    barChartData.datasets[0] = temp0

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false
    }

    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
</script>