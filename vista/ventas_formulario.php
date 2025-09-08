<?php 
include_once("../modelo/clsProducto.php");
include_once("../modelo/clsVenta.php");
include_once("../modelo/clsCliente.php");

$objVenta = new clsVenta();
$objClie = new clsCliente();

$documentos = $objClie->listaTipoDocumento();
$comprobantes = $objVenta->consultarComprobante();
$monedas = $objVenta->listarMoneda();

$objPro = new clsProducto();
$productos = $objPro->listarProducto("",1,"","");

if(isset($_GET['limpiar_sesion'])){
    if($_GET['limpiar_sesion']==1){
        $_SESSION['carrito']=array();
    }
}

$idventa = 0;
if(isset($_POST['idventa'])){
    $idventa = $_POST['idventa'];
}

$textoBoton = "Guardar";
$venta = NULL;
$cliente = NULL;
$idcliente = 0;
if($idventa>0){
    $textoBoton = "Actualizar";
    $venta = $objVenta->consultarVenta($idventa);
    if($venta->rowCount()>0){
        $venta = $venta->fetch(PDO::FETCH_NAMED);
        $idcliente = $venta['idcliente'];
    }
    $cliente = $objClie->consultarClientePorId($idcliente);
    if($cliente->rowCount()>0){
        $cliente = $cliente->fetch(PDO::FETCH_NAMED);
    }else{
        $cliente = NULL;
    }
}

?>
<section class="content mt-2">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">NUEVA VENTA</h3>
            </div>
            <div class="card-body">
                <form id="frmVenta" name="frmVenta">
                <div class="row">
                    <div class="col-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Fecha</span>
                            </div>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= $idventa>0?$venta['fecha']:date('Y-m-d') ?>" />
                            <input type="hidden" id="idventa" name="idventa" value="<?= $idventa ?>" />
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Comprobante</span>
                            </div>
                            <select class="form-control" id="idtipocomprobante" name="idtipocomprobante" <?php if($idventa>0){ ?> disabled <?php } ?> onchange="obtenerSeries();">
                                <option value="0">- Seleccione uno -</option>
                                <?php foreach($comprobantes as $k=>$v){ 
                                    $selected = "";
                                    if($idventa>0){
                                        if($v['idtipocomprobante']==$venta['idtipocomprobante']){
                                            $selected = "selected";
                                        }
                                    }
                                    ?>
                                <option value="<?= $v['idtipocomprobante']?>" <?= $selected ?> ><?= $v['nombre'] ?></option>
                                <?php }?>
                            </select>
                        </div> 
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Serie</span>
                            </div>
                            <select class="form-control" id="serie" name="serie" <?php if($idventa>0){ ?> disabled <?php } ?> onchange="obtenerCorrelativo()">
                                <option value="0">- Seleccione uno -</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Correlativo</span>
                            </div>
                            <input class="form-control" type="text" id="correlativo" <?php if($idventa>0){ ?> readonly <?php } ?> name="correlativo" />
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Forma de Pago</span>
                            </div>
                            <select class="form-control" id="formapago" name="formapago" onchange="verificarFormaPago()">
                                <option value="C">CONTADO</option>
                                <option value="D">CREDITO</option>
                            </select>
                        </div>                        
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo Doc</span>
                            </div>
                            <select class="form-control" name="idtipodocumento" id="idtipodocumento">
                                <?php foreach($documentos as $k=>$v){ ?>
                                <option value="<?= $v['idtipodocumento'];?>"><?= $v['nombre'] ?></option>                                
                                <?php }?>
                            </select>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nro Doc</span>
                            </div>
                            <input type="text" class="form-control" id="nrodocumento" name="nrodocumento" onblur="BuscarClienteDocumento()"/>
                            <span class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="BuscarClienteDocumento()" >
                                    <span class="fas fa-search"></span>
                                </button>
                            </span>        
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Cliente</span>
                            </div>
                            <input type="text" class="form-control" id="nombre" name="nombre"/>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Dirección</span>
                            </div>
                            <input type="text" class="form-control" id="direccion" name="direccion"/>
                        </div>
                        <div class="input-group d-none" id="divVencimiento">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Vencimiento</span>
                                <input type="date" class="form-control" id="vencimiento" name="vencimiento"/>
                            </div>                            
                        </div>                        
                    </div>                    
                    <div class="col-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Moneda</span>
                                <select class="form-control" id="moneda" name="moneda">
                                    <?php foreach($monedas as $k=>$v){ ?>
                                        <option value="<?= $v["idmoneda"] ?>"><?= $v["nombre"]?></option>
                                    <?php }?>
                                </select>
                            </div>                            
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Guia Rem.</span>
                                <input type="text" class="form-control" id="guiaremision" name="guiaremision"/>
                            </div>                            
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Orden Compra</span>
                                <input type="text" class="form-control" id="ordencompra" name="ordencompra"/>
                            </div>                            
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Monto Recibido</span>
                                <input type="text" class="form-control" id="montorecibido" name="montorecibido" onkeyup="if(event.keyCode=='13'){ verificarVuelto() }" onblur="verificarVuelto()" />
                            </div>                            
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Vuelto</span>
                                <input type="text" class="form-control" id="vuelto" name="vuelto" disabled />
                            </div>                            
                        </div>                                              
                    </div>
                </div>
                </form>
                <div class="row">
                    <div class="col-6 mt-3">
                        <table class="table table-bordered table-hover table-sm text-sm table-striped table-responsive"
                                id="tablaProducto">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Agregar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($productos as $k=>$v){?>
                                <tr>
                                    <td><?= $v['codigobarra'] ?></td>
                                    <td><?= $v['nombre'] ?></td>
                                    <td><?= $v['stock'] ?></td>
                                    <td><?= $v['pventa'] ?></td>
                                    <td>
                                        <button 
                                        type="button" 
                                        class="btn btn-xs btn-warning"
                                        onclick="AgregarProducto(<?= $v['idproducto'] ?>)"
                                        >Agregar</button>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <div id="divCarritoVenta">

                        </div>
                        <div align="right">
                            <button type="button" class="btn bg-maroon" onclick="LimpiarDetalleSesion()" >Limpiar Detalle</button>
                        </div>
                    </div>
                    <div class="col-12" align="center">
                        <button type="button" class="btn btn-primary" onclick="GuardarVenta()"><?= $textoBoton ?></button> 
                        <button type="button" class="btn btn-default" onclick="CancelarVenta()">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<!-- /.modal -->

<div class="modal fade" id="modalItem">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h4 class="modal-title">Categoría</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="frmItem" name="frmItem">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group d-none">
                            <label>Código</label>
                            <input type="text" class="form-control" name="item_codigo" id="item_codigo" readonly />
                            <input type="hidden" name="item" id="item" />
                        </div>
                        <div class="form-group">
                            <label>Producto</label>
                            <input type="text" class="form-control" name="item_nombre" id="item_nombre" readonly />
                        </div>
                        <div class="form-group">
                            <label>Precio</label>
                            <input type="text" class="form-control" name="item_pventa" id="item_pventa" />
                        </div>
                        <div class="form-group">
                            <label>Cantidad (Stock <span id='spanStock'></span>)</label>
                            <input type="text" class="form-control" name="item_cantidad" id="item_cantidad" />
                        </div>
                        <div class="form-group">
                            <label>Descuento</label>
                            <input type="text" class="form-control" name="item_descuento" id="item_descuento" />
                        </div>                                                                                                                        
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="ActualizarItem()">Aceptar</button>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->

<script>

<?php if($idventa>0){?>
    obtenerSeries(<?= $venta['idtipocomprobante']?>);
    $("#formapago").val('<?= $venta['formapago']?>');
    $("#vencimiento").val('<?= $venta['vencimiento']?>');
    $("#moneda").val('<?= $venta['idmoneda']?>');
    $("#guiaremision").val('<?= $venta['guiaremision']?>');
    $("#ordencompra").val('<?= $venta['ordencompra']?>');

    <?php if($cliente){ ?>
        $("#idtipodocumento").val("<?= $cliente['idtipodocumento'] ?>");
        $("#nrodocumento").val("<?= $cliente['nrodocumento'] ?>");
        $("#nombre").val("<?= $cliente['nombre'] ?>");
        $("#direccion").val("<?= $cliente['direccion'] ?>");
    <?php }?>

<?php }?>
function obtenerSeries(idtipocomprobante=0){
    $.ajax({
        method: "POST",
        url: "controlador/contVenta.php",
        data:{
            'accion': "SERIES",
            'idtipocomprobante': $("#idtipocomprobante").val()
        },
        dataType: 'json'
    }).done(function(resultado){
        series = "";
        for(i=0;i<resultado.length;i++){
            series = series + "<option value='"+resultado[i].serie+"'>"+resultado[i].serie+"</option>"; 
        }
        $("#serie").html(series);
        if(idtipocomprobante>0){
            <?php if($idventa>0){?>
            $("#serie").val('<?= $venta['serie'] ?>');
            $("#correlativo").val('<?= $venta['correlativo'] ?>');
            <?php }?>
        }else{
            obtenerCorrelativo();
        }
        
    });
}

function obtenerCorrelativo(){
    $.ajax({
        method: "POST",
        url: "controlador/contVenta.php",
        data:{
            'accion': "OBTENER_CORRELATIVO",
            'idtipocomprobante': $("#idtipocomprobante").val(),
            'serie': $("#serie").val()
        }
    }).done(function(resultado){
        $("#correlativo").val(resultado);
    });    
}

function verificarFormaPago(){
    if($("#formapago").val()=="C"){
        $("#divVencimiento").addClass("d-none");
    }else{
        $("#divVencimiento").removeClass("d-none");
    }
}

function BuscarClienteDocumento(){
    if($("#nrodocumento").val()!=""){
        $.ajax({
            method: "POST",
            url: "controlador/contCliente.php",
            data: {
                accion : "CONSULTAR_DATOS_WS",
                idtipodocumento : $("#idtipodocumento").val(),
                nrodocumento : $("#nrodocumento").val()
            },
            dataType: "json"
        }).done(function(resultado){
           if(resultado["nombre"]!=""){
                toastCorrecto("Cliente localizado");        
                $("#idtipodocumento").val(resultado['idtipodocumento']);
                $("#nombre").val(resultado['nombre']);
                $("#direccion").val(resultado['direccion']);                    
           }else{
                msjError = "No se localizó cliente."
                toastError(msjError); 
           }
        });
    }
}

$('#tablaProducto').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "order":[[1,'asc']],
      "info": true,
      "autoWidth": false,
      "responsive": true
    });

function AgregarProducto(idproducto){
    $.ajax({
        method: "POST",
        url: "controlador/contVenta.php",
        data: {
            "accion" : "AGREGAR_PRODUCTO",
            "idproducto": idproducto 
        }
    }).done(function(resultado){
       verCarrito();
    });  
}

function verCarrito(idventa=0){
    $.ajax({
            method: "POST",
            url: "controlador/contVenta.php",
            data: {
                "accion" : "VER_CARRITO",
                "idventa" : idventa 
        }
    }).done(function(resultado){
        $("#divCarritoVenta").html(resultado);
    }); 
}

verCarrito(<?= $idventa ?>);

function LimpiarDetalleSesion(){
    $.ajax({
            method: "POST",
            url: "controlador/contVenta.php",
            data: {
                "accion" : "ELIMINAR_CARRITO" 
        }
    }).done(function(resultado){
        verCarrito();
    });     
}

function ActualizarItem(){
    $.ajax({
            method: "POST",
            url: "controlador/contVenta.php",
            data: {
                "accion" : "ACTUALIZAR_ITEM",
                "item"    : $("#item").val(),
                "pventa"  : $("#item_pventa").val(),
                "cantidad": $("#item_cantidad").val(),
                "descuento": $("#item_descuento").val()
        }
    }).done(function(resultado){        
        verCarrito();
        $("#modalItem").modal('hide');
    });  
}

function EliminarItem(item){
    $.ajax({
            method: "POST",
            url: "controlador/contVenta.php",
            data: {
                "accion" : "ELIMINAR_ITEM",
                "item"    : item
        }
    }).done(function(resultado){
        verCarrito();
    });  
}

function EditarItem(item){
    $.ajax({
            method: "POST",
            url: "controlador/contVenta.php",
            data: {
                "accion" : "OBTENER_ITEM",
                "item"    : item
            },
            dataType: "json"
    }).done(function(resultado){
            $("#item").val(item);
            $("#item_codigo").val(resultado.codigo);
            $("#item_nombre").val(resultado.nombre);
            $("#item_pventa").val(resultado.pventa);
            $("#item_cantidad").val(resultado.cantidad);
            $("#item_descuento").val(resultado.descuento);
            $("#spanStock").html(resultado.stock);
            $("#modalItem").modal('show');
    });  
}

function CancelarVenta(){
    <?php if($idventa>0){ ?>
        AbrirPagina('vista/ventas.php');    
    <?php }else{ ?>
        AbrirPagina('vista/ventas_formulario.php?limpiar_sesion=1');
    <?php }?>
}

function GuardarVenta() {
    if (!ValidarFormulario()) {
        return 0;
    }

    var datos_formulario = $("#frmVenta").serializeArray();
    
    if ($("#idventa").val() != "" && $("#idventa").val() != "0") {
        datos_formulario.push({ name: "accion", value: "ACTUALIZAR" });
    } else {
        datos_formulario.push({ name: "accion", value: "NUEVO" });
    }

    datos_formulario.push({ name: "idtipocomprobante1", value: $('#idtipocomprobante').val() });
    datos_formulario.push({ name: "serie1", value: $('#serie').val() });

    $.ajax({
        method: "POST",
        url: "controlador/contVenta.php",
        data: datos_formulario,
        dataType: 'json'
    }).done(function(resultado) {
        if (resultado.codigoError == 1) {
            toastCorrecto("Registro satisfactorio");
            
            CancelarVenta();
        } else if (resultado.codigoError == 99) {
            toastError("Existen problemas de stock de algunos productos");
            for (i = 0; i < resultado.problemasStock.length; i++) {
                $("#trcarrito" + resultado.problemasStock[i].item).addClass("bg-danger");
            }
        } else {
            msjError = resultado == 2 ? "Venta duplicada" : "No se pudo registrar la venta.";
            toastError(msjError);
        }
    });
}





function ValidarFormulario(){
    retorno = true;
    if($("#idtipocomprobante").val()=="" || $("#idtipocomprobante").val()=="0"){
        toastError('Especifique un documento de venta.');          
        retorno = false;
    }
    
    if($("#idtipocomprobante").val()=="01" && $("#idtipodocumento").val()!="6"){
        toastError('En las facturas debe especificar RUC.');          
        retorno = false;
    }

    if($("#idtipocomprobante").val()=="03"){
        let monto_total = $('#txtTotalVenta').val();
        if(monto_total>700 && ($('#nrodocumento').val()=="" || $('#nombre').val()=="" )){
            toastError('Es obligatoria ingresar datos del cliente, ha sobrepasado los 700 soles.');          
            retorno = false;
        }
    }

    return retorno;
}

<?php if($idventa==0){ ?>
obtenerSeries();
<?php } ?>

function verificarVuelto(){
    vuelto = parseFloat($('#montorecibido').val()) - parseFloat($('#txtTotalVenta').val())
    $('#vuelto').val(vuelto.toFixed(2));
}

verificarFormaPago();

</script>