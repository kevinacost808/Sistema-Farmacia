<?php
define('FPDF_FONTPATH', 'font/');
require_once('../fpdf/fpdf.php');
require_once("../phpqrcode/qrlib.php");
require_once('../modelo/clsVenta.php');
require_once('../modelo/clsCliente.php');
require_once('../modelo/cantidad_en_letras.php');

class ComprobantePDF {
    private $objVenta;
    private $objCliente;

    public function __construct() {
        $this->objVenta = new clsVenta();
        $this->objCliente = new clsCliente();
    }

    public function generarPDF($id) {
        // Validar y sanitizar el ID
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $venta = $this->objVenta->consultarVenta($id);
        $venta = $venta->fetch(PDO::FETCH_NAMED);

        if (!$venta) {
            throw new Exception("Venta no encontrada.");
        }

        $detalle = $this->objVenta->consultarDetalleVenta($id);
        $cuotas = array();

        $emisor = array(
            "ruc" => "20602814211",
            "razon_social" => "San Rafael",
            "direccion" => ""
        );

        $tipo_comprobante = $this->objVenta->obtenerComprobante($venta['idtipocomprobante']);
        $tipo_comprobante = $tipo_comprobante->fetch(PDO::FETCH_NAMED);

        $cliente = $this->objCliente->consultarClientePorId($venta['idcliente']);
        $cliente = $cliente->fetch(PDO::FETCH_NAMED);

        // INICIAMOS CON LA CREACION DEL PDF
        $pdf = new FPDF();
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Image("../fpdf/logo.png", 90, 2, 30, 30);
        $pdf->Ln(18);

        // Imprimir datos del emisor y comprobante
        $this->imprimirDatosEmisor($pdf, $emisor, $tipo_comprobante, $venta);
        
        // Imprimir datos del cliente
        $this->imprimirDatosCliente($pdf, $cliente, $venta);

        // Imprimir detalle de la venta
        $this->imprimirDetalleVenta($pdf, $detalle, $venta);

        // Generar y agregar código QR
        $this->generarCodigoQR($venta, $emisor, $cliente);

        // Salida del PDF
        $pdf->Output('I', $this->nombreArchivo($venta) . '.pdf');
    }

    private function imprimirDatosEmisor($pdf, $emisor, $tipo_comprobante, $venta) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(105, 6, "RUC - " . $emisor['ruc'], 0, 0, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 6, $emisor['ruc'], 'LRT', 1, 'C', 0);
        
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(105, 6, $emisor['razon_social'], 0, 0, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 6, $tipo_comprobante['nombre'], 'LR', 1, 'C', 0);
        
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(105, 6, utf8_decode($emisor['direccion']), 0, 0, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 6, $venta['serie'] . " - " . $venta['correlativo'], 'BLR', 0, 'C', 0);
        $pdf->Ln();
    }

    private function imprimirDatosCliente($pdf, $cliente, $venta) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, "FECHA:", 0, 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $fecha = explode('-', $venta['fecha']);
        $pdf->Cell(30, 6, $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0], 0, 1, 'L', 0);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, "RUC:", 0, 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 6, $cliente['nrodocumento'], 0, 1, 'L', 0);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, "CLIENTE:", 0, 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 6, $cliente['nombre'], 0, 1, 'L', 0);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, "DIRECCION:", 0, 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 6, $cliente['direccion'], 0, 1, 'L', 0);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, "FORMA DE PAGO:", 0, 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $formapago = $venta['formapago'] == "C" ? "CONTADO" : "CREDITO";
        $pdf->Cell(30, 6, $formapago, 0, 1, 'L', 0);

        if ($venta['formapago'] != 'C') {
            $fecha = explode('-', $venta['vencimiento']);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(30, 6, "VENCIMIENTO:", 0, 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(30, 6, $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0], 0, 1, 'L', 0);
        }
        $pdf->Ln(3);
    }

    private function imprimirDetalleVenta($pdf, $detalle, $venta) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(10, 6, "ITEM", 1, 0, 'C', 0);
        $pdf->Cell(20, 6, "CANTIDAD", 1, 0, 'C', 0);
        $pdf->Cell(105, 6, "PRODUCTO", 1, 0, 'C', 0);
        $pdf->Cell(20, 6, "PRECIO", 1, 0, 'C', 0);
        $pdf->Cell(25, 6, "SUBTOTAL", 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 8);
        $i = 1;
        while ($fila = $detalle->fetch(PDO::FETCH_NAMED)) {
            $pdf->Cell(10, 6, $i, 1, 0, 'C', 0);
            $pdf->Cell(20, 6, (float)$fila['cantidad'], 1, 0, 'C', 0);
            $pdf->Cell(105, 6, $fila['nombre'], 1, 0, 'L', 0);
            $pdf->Cell(20, 6, (float)$fila['pventa'], 1, 0, 'C', 0);
            $pdf->Cell(25, 6, (float)$fila['total'], 1, 1, 'C', 0);
            $i++;
        }

        $this->imprimirTotales($pdf, $venta);
    }

    private function imprimirTotales($pdf, $venta) {
        $pdf->Cell(155, 6, "OP. GRAVADAS", 0, 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_gravado'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "IGV (18%)", '', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_igv'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "OP. EXONERADAS", '', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_exonerado'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "OP. INAFECTAS", '', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_inafecto'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "ICBPER", '', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_icbper'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "DESCUENTO", '', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total_descuento'], 1, 1, 'C', 0);
        $pdf->Cell(155, 6, "IMPORTE TOTAL", '0', 0, 'R', 0);
        $pdf->Cell(25, 6, $venta['total'], 1, 1, 'C', 0);
        $pdf->Ln(10);
        $pdf->Cell(180, 6, utf8_decode("TOTAL EN LETRAS: SON " . CantidadEnLetra($venta['total'])), 1, 0, 'L', 0);
        $pdf->Ln(10);
    }

    private function generarCodigoQR($venta, $emisor, $cliente) {
        $ruc = $emisor['ruc'];
        $tipo_documento = $venta['idtipocomprobante']; // factura
        $serie = $venta['serie'];
        $correlativo = $venta['correlativo'];
        $igv = $venta['total_igv'];
        $total = $venta['total'];
        $fecha = $venta['fecha'];
        $tipodoccliente = $cliente['idtipodocumento'];
        $nro_doc_cliente = $cliente['nrodocumento'];

        $nombrexml = $ruc . "-" . $tipo_documento . "-" . $serie . "-" . $correlativo;
        $text_qr = "{$ruc} | {$tipo_documento} | {$serie} | {$correlativo} | {$igv} | {$total} | {$fecha} | {$tipodoccliente} | {$nro_doc_cliente}";
        $ruta_qr = "../phpqrcode/qr/" . $nombrexml . '.png';

        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);
        $pdf = new FPDF();
        $pdf->Image($ruta_qr, 90, $pdf->GetY(), 25, 25);
        $pdf->Ln(30);
    }

    private function nombreArchivo($venta) {
        return $venta['serie'] . '-' . $venta['correlativo'];
    }
}

// Uso de la clase
try {
    $comprobantePDF = new ComprobantePDF();
    $comprobantePDF->generarPDF($_GET['id']);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
