<?php
require_once('../modelo/clsVenta.php');
require_once('../modelo/clsProducto.php');

$objVenta = new clsVenta();
$objPro = new clsProducto();

$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$idtipocomprobante = $_POST['idtipocomprobante'];
$idusuario = $_POST['idusuario'];

$meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

// Obtener todas las ventas agrupadas por mes y producto
$sql = "SELECT YEAR(v.fecha) as anio, MONTH(v.fecha) as mes, d.idproducto, SUM(d.cantidad) as cantidad_vendida, d.pventa, pr.pcompra, pr.nombre, pr.codigobarra, un.descripcion as unidad
        FROM venta v
        INNER JOIN detalle d ON v.idventa = d.idventa
        INNER JOIN producto pr ON d.idproducto = pr.idproducto
        INNER JOIN unidad un ON pr.idunidad = un.idunidad
        WHERE v.estado=1 AND d.estado=1";
$params = array();
if($desde!=""){
    $sql .= " AND v.fecha >= :desde ";
    $params[':desde'] = $desde;
}
if($hasta!=""){
    $sql .= " AND v.fecha <= :hasta ";
    $params[':hasta'] = $hasta;
}
if($idtipocomprobante!=""){
    $sql .= " AND v.idtipocomprobante = :idtipocomprobante ";
    $params[':idtipocomprobante'] = $idtipocomprobante;
}
if($idusuario!=""){
    $sql .= " AND v.idusuario = :idusuario ";
    $params[':idusuario'] = $idusuario;
}
$sql .= " GROUP BY anio, mes, d.idproducto ORDER BY anio, mes, pr.nombre";

// Ejecutar consulta
require_once('../modelo/conexion.php');
$pre = $cnx->prepare($sql);
$pre->execute($params);
$utilidades = $pre->fetchAll(PDO::FETCH_NAMED);

// Organizar datos para tabla y gráfico
$tabla = array();
$labels = array();
$utilidad_mensual = array();
foreach($utilidades as $row){
    $key = $row['anio'].'-'.$row['mes'];
    if(!isset($tabla[$key])){
        $tabla[$key] = array();
        $labels[$key] = $meses[$row['mes']-1].' '.$row['anio'];
        $utilidad_mensual[$key] = 0;
    }
    $venta_total = $row['cantidad_vendida'] * $row['pventa'];
    $costo_total = $row['cantidad_vendida'] * $row['pcompra'];
    $utilidad = $venta_total - $costo_total;
    $tabla[$key][] = array(
        'producto' => $row['nombre'],
        'codigo' => $row['codigobarra'],
        'unidad' => $row['unidad'],
        'cantidad' => $row['cantidad_vendida'],
        'pventa' => $row['pventa'],
        'pcompra' => $row['pcompra'],
        'venta_total' => $venta_total,
        'costo_total' => $costo_total,
        'utilidad' => $utilidad
    );
    $utilidad_mensual[$key] += $utilidad;
}
?>
<table class="table table-bordered table-sm table-hover table-striped" id="tablaUtilidadMes">
    <thead>
        <tr class="bg-primary">
            <th>MES</th>
            <th>CODIGO</th>
            <th>PRODUCTO</th>
            <th>UNIDAD</th>
            <th>CANTIDAD</th>
            <th>P. COMPRA</th>
            <th>P. VENTA</th>
            <th>UTILIDAD</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($tabla as $key=>$productos){
            foreach($productos as $prod){ ?>
            <tr>
                <td><?= $labels[$key] ?></td>
                <td><?= $prod['codigo'] ?></td>
                <td><?= $prod['producto'] ?></td>
                <td><?= $prod['unidad'] ?></td>
                <td class="text-right"><?= number_format($prod['cantidad'],2) ?></td>
                <td class="text-right"><?= number_format($prod['pcompra'],2) ?></td>
                <td class="text-right"><?= number_format($prod['pventa'],2) ?></td>
                <td class="text-right text-bold"><?= number_format($prod['utilidad'],2) ?></td>
            </tr>
        <?php }} ?>
    </tbody>
    <tfoot>
        <tr class="bg-success">
            <th colspan="7" class="text-right">UTILIDAD TOTAL</th>
            <th class="text-right text-bold">
                <?= number_format(array_sum($utilidad_mensual),2) ?>
            </th>
        </tr>
    </tfoot>
</table>
<!-- BAR CHART -->
<div class="card card-success">
    <div class="card-body">
        <div class="chart">
            <canvas id="barChartUtilidad" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
</div>
<script>
$("#tablaUtilidadMes").DataTable({
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
}).buttons().container().appendTo('#tablaUtilidadMes_wrapper .col-md-6:eq(0)');

// Gráfico de barras de utilidad mensual
var areaChartData = {
  labels  : [<?php echo "'".implode("','", $labels)."'"; ?>],
  datasets: [
    {
      label               : 'Utilidad',
      backgroundColor     : 'rgba(40,167,69,0.9)',
      borderColor         : 'rgba(40,167,69,0.8)',
      pointRadius          : false,
      pointColor          : '#28a745',
      pointStrokeColor    : 'rgba(40,167,69,1)',
      pointHighlightFill  : '#fff',
      pointHighlightStroke: 'rgba(40,167,69,1)',
      data                : [<?php echo implode(",", $utilidad_mensual); ?>]
    }
  ]
}
var barChartCanvas = $('#barChartUtilidad').get(0).getContext('2d')
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
