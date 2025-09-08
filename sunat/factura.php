<?php
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

require __DIR__.'/../vendor/autoload.php';

$see = require __DIR__.'/config.php';

/**
 * 📌 Variables dinámicas (ejemplo: vendrían de BD o formulario)
 */

// Cliente
$tipoDocCliente   = "6"; // RUC
$numDocCliente    = "20000000001";
$razonSocialClie  = "EMPRESA X";

// Emisor
$rucEmisor        = "20123456789";
$razonSocialEmi   = "GREEN SAC";
$nombreComercial  = "GREEN";
$direccionEmi     = "Av. Villa Nueva 221";
$ubigeo           = "150101";
$departamento     = "LIMA";
$provincia        = "LIMA";
$distrito         = "LIMA";
$urbanizacion     = "-";
$codigoLocal      = "0000"; 

// Venta
$tipoOperacion    = "0101";  // Venta - Cat 51
$tipoDocVenta     = "01";    // Factura
$serie            = "F001";
$correlativo      = "1";
$fechaEmision     = new DateTime('now', new DateTimeZone('America/Lima'));
$moneda           = "PEN";
$formaPago        = new FormaPagoContado();

// Totales (ejemplo dinámico)
$mtoOperGravadas  = 100.00;
$mtoIGV           = 18.00;
$totalImpuestos   = 18.00;
$valorVenta       = 100.00;
$subTotal         = 118.00;
$mtoImpVenta      = 118.00;

// Detalles dinámicos (puede ser un foreach desde la BD)
$detalles = [
    [
        "codProducto" => "P001",
        "unidad"      => "NIU",
        "cantidad"    => 2,
        "valorUnit"   => 50.00,
        "descripcion" => "PRODUCTO 1",
        "baseIgv"     => 100.00,
        "porcentajeIgv"=> 18.00,
        "igv"         => 18.00,
        "tipAfeIgv"   => "10",
        "totalImp"    => 18.00,
        "valorVenta"  => 100.00,
        "precioUnit"  => 59.00
    ]
];

// Leyendas dinámicas
$leyendaMontoLetras = "SON CIENTO DIECIOCHO CON 00/100 SOLES";

/**
 * 📌 CREACIÓN DE OBJETOS
 */
// Cliente
$client = (new Client())
    ->setTipoDoc($tipoDocCliente)
    ->setNumDoc($numDocCliente)
    ->setRznSocial($razonSocialClie);

// Dirección
$address = (new Address())
    ->setUbigueo($ubigeo)
    ->setDepartamento($departamento)
    ->setProvincia($provincia)
    ->setDistrito($distrito)
    ->setUrbanizacion($urbanizacion)
    ->setDireccion($direccionEmi)
    ->setCodLocal($codigoLocal);

// Empresa
$company = (new Company())
    ->setRuc($rucEmisor)
    ->setRazonSocial($razonSocialEmi)
    ->setNombreComercial($nombreComercial)
    ->setAddress($address);

// Comprobante
$invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion($tipoOperacion)
    ->setTipoDoc($tipoDocVenta)
    ->setSerie($serie)
    ->setCorrelativo($correlativo)
    ->setFechaEmision($fechaEmision)
    ->setFormaPago($formaPago)
    ->setTipoMoneda($moneda)
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas($mtoOperGravadas)
    ->setMtoIGV($mtoIGV)
    ->setTotalImpuestos($totalImpuestos)
    ->setValorVenta($valorVenta)
    ->setSubTotal($subTotal)
    ->setMtoImpVenta($mtoImpVenta);

// Items
$items = [];
foreach ($detalles as $d) {
    $items[] = (new SaleDetail())
        ->setCodProducto($d['codProducto'])
        ->setUnidad($d['unidad'])
        ->setCantidad($d['cantidad'])
        ->setMtoValorUnitario($d['valorUnit'])
        ->setDescripcion($d['descripcion'])
        ->setMtoBaseIgv($d['baseIgv'])
        ->setPorcentajeIgv($d['porcentajeIgv'])
        ->setIgv($d['igv'])
        ->setTipAfeIgv($d['tipAfeIgv'])
        ->setTotalImpuestos($d['totalImp'])
        ->setMtoValorVenta($d['valorVenta'])
        ->setMtoPrecioUnitario($d['precioUnit']);
}

// Leyendas
$legend = (new Legend())
    ->setCode('1000')
    ->setValue($leyendaMontoLetras);

// Agregar al comprobante
$invoice->setDetails($items)
        ->setLegends([$legend]);

/**
 * 📌 ENVIAR A SUNAT
 */
$result = $see->send($invoice);

// Guardar XML
file_put_contents($invoice->getName().'.xml',
                  $see->getFactory()->getLastXml());

// Verificar
if (!$result->isSuccess()) {
    echo 'Codigo Error: '.$result->getError()->getCode()."\n";
    echo 'Mensaje Error: '.$result->getError()->getMessage();
    exit();
}

// Guardar CDR
file_put_contents('R-'.$invoice->getName().'.zip', $result->getCdrZip());



// EL CDR PARA OBTENER RESPUESTA
$cdr = $result->getCdrResponse();

$code = (int)$cdr->getCode();

if ($code === 0) {
    echo 'ESTADO: ACEPTADA'.PHP_EOL;
    if (count($cdr->getNotes()) > 0) {
        echo 'OBSERVACIONES:'.PHP_EOL;
        // Corregir estas observaciones en siguientes emisiones.
        var_dump($cdr->getNotes());
    }  
} else if ($code >= 2000 && $code <= 3999) {
    echo 'ESTADO: RECHAZADA'.PHP_EOL;
} else {
    /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
    /*code: 0100 a 1999 */
    echo 'Excepción';
}

echo $cdr->getDescription().PHP_EOL;