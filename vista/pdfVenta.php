<?php 
define('FPDF_FONTPATH','font/'); 
require_once('../fpdf/fpdf.php');
require_once("../phpqrcode/qrlib.php");
require_once('../modelo/clsVenta.php');
require_once('../modelo/clsCliente.php');
require_once('../modelo/cantidad_en_letras.php');

$objVenta = new clsVenta();
$objCliente = new clsCliente();

// CONSULTAMOS LOS DATOS NECESARIOS PARA MOSTRAR EN EL PDF
$venta = $objVenta->consultarVenta($_GET['id']);
$venta = $venta->fetch(PDO::FETCH_NAMED);
$detalle = $objVenta->consultarDetalleVenta($_GET['id']);

$cuotas = array();
$emisor = array(
    "ruc"=> "20602814211",
    "razon_social"=>"San Rafael",
    "direccion"=>""
);

$tipo_comprobante = $objVenta->obtenerComprobante($venta['idtipocomprobante']);
$tipo_comprobante = $tipo_comprobante->fetch(PDO::FETCH_NAMED);

$cliente = $objCliente->consultarClientePorId($venta['idcliente']);
$cliente = $cliente->fetch(PDO::FETCH_NAMED);

// INICIAMOS CON LA CREACION DEL PDF
$pdf = new FPDF();
$pdf->AddPage('P','A4');
$pdf->SetFont('Arial','B',12);
$pdf->Image("../fpdf/logo.png",90,2,30,30);
$pdf->Ln(18);

// Datos del emisor
$pdf->SetFont('Arial','',8);
$pdf->Cell(105,6,"RUC - ".$emisor['ruc'],0,0,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,$emisor['ruc'],'LRT',1,'C',0);

$pdf->SetFont('Arial','',8);
$pdf->Cell(105,6,$emisor['razon_social'],0,0,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,$tipo_comprobante['nombre'],'LR',1,'C',0);

$pdf->SetFont('Arial','',8);
$pdf->Cell(105,6,utf8_decode($emisor['direccion']),0,0,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,$venta['serie']." - ".$venta['correlativo'],'BLR',0,'C',0);

$pdf->SetAutoPageBreak('auto',2);
$pdf->SetDisplayMode('fullpage');

$pdf->Ln();
$fecha = explode('-', $venta['fecha']);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"FECHA:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,6,$fecha[2].'/'.$fecha[1].'/'.$fecha[0],0,1,'L',0);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"RUC:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,6,$cliente['nrodocumento'],0,1,'L',0);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"CLIENTE:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,6,$cliente['nombre'],0,1,'L',0);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"DIRECCION:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,6,$cliente['direccion'],0,1,'L',0);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"FORMA DE PAGO:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$formapago = $venta['formapago']=="C"?"CONTADO":"CREDITO";
$pdf->Cell(30,6,$formapago,0,1,'L',0);

if($venta['formapago']!='C'){
    $fecha = explode('-', $venta['vencimiento']);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,6,"VENCIMIENTO:",0,0,'L',0);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,$fecha[2].'/'.$fecha[1].'/'.$fecha[0],0,1,'L',0);
}
$pdf->Ln(3);

// Detalle de la venta
$pdf->SetFont('Arial','B',8);
$pdf->Cell(10,6,"ITEM",1,0,'C',0);
$pdf->Cell(20,6,"CANTIDAD",1,0,'C',0);
$pdf->Cell(105,6,"PRODUCTO",1,0,'C',0);
$pdf->Cell(20,6,"PRECIO",1,0,'C',0);
$pdf->Cell(25,6,"SUBTOTAL",1,1,'C',0);

$pdf->SetFont('Arial','',8);
$i=1;
while($fila = $detalle->fetch(PDO::FETCH_NAMED)){
    $pdf->Cell(10,6,$i,1,0,'C',0);
    $pdf->Cell(20,6,(float) $fila['cantidad'],1,0,'C',0);
    $pdf->Cell(105,6,$fila['nombre'],1,0,'L',0);
    $pdf->Cell(20,6,(float) $fila['pventa'],1,0,'C',0);
    $pdf->Cell(25,6,(float) $fila['total'],1,1,'C',0);
    $i++;
}

// Totales
$pdf->Cell(155,6,"OP. GRAVADAS",0,0,'R',0);
$pdf->Cell(25,6,$venta['total_gravado'],1,1,'C',0);
$pdf->Cell(155,6,"IGV (18%)",'',0,'R',0);
$pdf->Cell(25,6,$venta['total_igv'],1,1,'C',0);
$pdf->Cell(155,6,"OP. EXONERADAS",'',0,'R',0);
$pdf->Cell(25,6,$venta['total_exonerado'],1,1,'C',0);
$pdf->Cell(155,6,"OP. INAFECTAS",'',0,'R',0);
$pdf->Cell(25,6,$venta['total_inafecto'],1,1,'C',0);
$pdf->Cell(155,6,"ICBPER",'',0,'R',0);
$pdf->Cell(25,6,$venta['total_icbper'],1,1,'C',0);
$pdf->Cell(155,6,"DESCUENTO",'',0,'R',0);
$pdf->Cell(25,6,$venta['total_descuento'],1,1,'C',0);
$pdf->Cell(155,6,"IMPORTE TOTAL",'0',0,'R',0);
$pdf->Cell(25,6,$venta['total'],1,1,'C',0);

$pdf->Ln(10);
$pdf->Cell(180,6,utf8_decode("TOTAL EN LETRAS: SON ".CantidadEnLetra($venta['total'])),1,0,'L',0);
$pdf->Ln(10);

// Detalle de cuotas si es crédito
if($venta['formapago']!='C'){
    $pdf->Cell(70,6,'IMPORTE NETO AL CREDITO:'.$venta['total'],1,0,'L',0);
    $pdf->Ln();
    $pdf->Cell(70,6,'NRO CUOTAS: '.count($cuotas),1,0,'L',0);
    $pdf->Ln();    
    $pdf->Cell(70,6,'DETALLE DE CUOTAS',1,0,'L',0);
    $pdf->Ln();
    $pdf->Cell(20,6,'CUOTA',1,0,'L',0);
    $pdf->Cell(20,6,'MONTO',1,0,'L',0);
    $pdf->Cell(30,6,'VENCIMIENTO',1,0,'L',0);
    foreach($cuotas as $k=>$fila){
        $pdf->Ln();
        $fecha = explode("-",$fila['fecha_vencimiento']);
        $pdf->Cell(20,6,$fila['numero'],1,0,'C',0);
        $pdf->Cell(20,6,number_format($fila['importe'],2),1,0,'R',0);
        $pdf->Cell(30,6,$fecha[2].'/'.$fecha[1].'/'.$fecha[0],1,0,'C',0);
    }
    $pdf->Ln(10);
}

// Generación del código QR
$ruc = $emisor['ruc'];
$tipo_documento = $venta['idtipocomprobante']; // factura
$serie = $venta['serie'];
$correlativo = $venta['correlativo'];
$igv = $venta['total_igv'];
$total = $venta['total'];
$fecha = $venta['fecha'];
$tipodoccliente = $cliente['idtipodocumento'];
$nro_doc_cliente = $cliente['nrodocumento'];

$nombrexml = $ruc."-".$tipo_documento."-".$serie."-".$correlativo;

$text_qr = $ruc." | ".$tipo_documento." | ".$serie." | ".$correlativo." | ".$igv." | ".$total." | ".$fecha." | ".$tipodoccliente." | ".$nro_doc_cliente;
$ruta_qr = "../phpqrcode/qr/".$nombrexml.'.png';

QRcode::png($text_qr, $ruta_qr, 'Q',15, 0);
$pdf->Image($ruta_qr, 90 , $pdf->GetY(),25,25);

$pdf->Ln(30);
$pdf->Output('I',$nombrexml.'.pdf');
?>
