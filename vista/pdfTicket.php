<?php 
define('FPDF_FONTPATH','font/'); 
require_once('../fpdf/fpdf.php');
require_once("../phpqrcode/qrlib.php");
require_once('../modelo/clsVenta.php');
require_once('../modelo/clsCliente.php');
require_once('../modelo/cantidad_en_letras.php');

$objVenta = new clsVenta();
$objCliente = new clsCliente();

//CONSULTAMOS LOS DATOS NECESARIOS PARA MOSTRAR EN EL PDF

$venta = $objVenta->consultarVenta($_GET['id']);
$venta = $venta->fetch(PDO::FETCH_NAMED);

$detalle = $objVenta->consultarDetalleVenta($_GET['id']);

$cuotas = array();
$emisor = array(
    "ruc"=> "20602814211",
    "razon_social"=>"Arcancel San Rafael",
    "direccion"=>"Aqui va tu direccion"
);

$tipo_comprobante = $objVenta->obtenerComprobante($venta['idtipocomprobante']);
$tipo_comprobante = $tipo_comprobante->fetch(PDO::FETCH_NAMED);

$cliente = $objCliente->consultarClientePorId($venta['idcliente']);
$cliente = $cliente->fetch(PDO::FETCH_NAMED);

//INICIAMOS CON LA CREACION DEL PDF

$pdf = new FPDF();
//$pdf->AddPage('P','A4');
$pdf->AddPage('P',array(80,3278)); //FORMATO PARA TICKETS
$pdf->SetMargins(5,5);
$pdf->SetFont('Arial','B',8);

$pdf->Image("../fpdf/logo.png",27,2,25,25);

$pdf->Ln(18);

$pdf->SetFont('Arial','',8);
$pdf->Cell(70,6,"RUC - ".$emisor['ruc'],0,0,'C');
$pdf->Ln();
$pdf->MultiCell(70,6,$emisor['razon_social'],0,'C');
$pdf->MultiCell(70,6,utf8_decode($emisor['direccion']),0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(70,6,$tipo_comprobante['nombre'],0,1,'C',0);
$pdf->Cell(70,6,$venta['serie']." - ".$venta['correlativo'],0,0,'C',0);
$pdf->SetFont('Arial','',8);
$pdf->SetAutoPageBreak('auto',2);

$pdf->SetDisplayMode(75);

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
$pdf->MultiCell(30,6,$cliente['nombre'],0,'L',0);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,6,"DIRECCION:",0,0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(30,4,$cliente['direccion'],0,'L');

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

$pdf->SetFont('Arial','B',8);
$pdf->Cell(10,6,"CANT",'B',0,'C',0);
$pdf->Cell(40,6,"PRODUCTO",'B',0,'C',0);
$pdf->Cell(10,6,"P.U.",'B',0,'C',0);
$pdf->Cell(10,6,"SUB",'B',1,'C',0);

$pdf->SetFont('Arial','',8);
$pdf->SetFillColor(255,255,255);
$i=1;
while($fila = $detalle->fetch(PDO::FETCH_NAMED)){
    $top= ($i==1)?"T":0;
    $pdf->Cell(10,6,(float) $fila['cantidad'],0,0,'C',0);
    $pdf->Cell(40,6,$fila['nombre'],0,0,'L',0);
    $pdf->Cell(10,6,(float) $fila['pventa'],$top,0,'C',1);
    $pdf->Cell(10,6,(float) $fila['total'],$top,1,'C',1);
    $i++;
}

$pdf->Cell(60,6,"OP. GRAVADAS",'T',0,'R',0);
$pdf->Cell(10,6,$venta['total_gravado'],'T',1,'C',0);
$pdf->Cell(60,6,"IGV (18%)",'',0,'R',0);
$pdf->Cell(10,6,$venta['total_igv'],0,1,'C',0);
$pdf->Cell(60,6,"OP. EXONERADAS",'',0,'R',0);
$pdf->Cell(10,6,$venta['total_exonerado'],0,1,'C',0);
$pdf->Cell(60,6,"OP. INAFECTAS",'',0,'R',0);
$pdf->Cell(10,6,$venta['total_inafecto'],0,1,'C',0);
$pdf->Cell(60,6,"ICBPER",'',0,'R',0);
$pdf->Cell(10,6,$venta['total_icbper'],0,1,'C',0);
$pdf->Cell(60,6,"DESCUENTO",'',0,'R',0);
$pdf->Cell(10,6,$venta['total_descuento'],0,1,'C',0);
$pdf->Cell(60,6,"IMPORTE TOTAL",'0',0,'R',0);
$pdf->Cell(10,6,$venta['total'],0,1,'C',0);

$pdf->Ln(5);

$pdf->MultiCell(70,4,utf8_decode("TOTAL EN LETRAS: SON ".CantidadEnLetra($venta['total'])),0,'C');

$pdf->Ln(5);
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
    foreach( $cuotas as $k=>$fila){
        $pdf->Ln();
        $fecha = explode("-",$fila['fecha_vencimiento']);
        $pdf->Cell(20,6,$fila['numero'],1,0,'C',0);
        $pdf->Cell(20,6,number_format($fila['importe'],2),1,0,'R',0);
        $pdf->Cell(30,6,$fecha[2].'/'.$fecha[1].'/'.$fecha[0],1,0,'C',0);
    }
    $pdf->Ln(10);
}
//codigo qr
    /*RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |*/

$ruc = $emisor['ruc'];
$tipo_documento = $venta['idtipocomprobante']; //factura
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

$pdf->Image($ruta_qr, 28 , $pdf->GetY(),25,25);

//$pdf->Ln(28);
//$pdf->MultiCell(70,3,utf8_decode("Representación impresa de la ".$tipo_comprobante['nombre']." ELECTRÓNICA"),0,'C');
//$pdf->Ln(5);
//$pdf->MultiCell(70,3,utf8_decode("Este comprobante electrónico podrá ser consultado en factura.isi.pe"),0,'C',0);

$pdf->Output('I',$nombrexml.'.pdf');
//$pdf->Output('D',$nombrexml.'.pdf');
?>