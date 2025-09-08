<?php
require_once('../modelo/clsProducto.php');
$accion = $_POST['accion'];

controlador($accion);

function controlador($accion){

	$objPro = new clsProducto();

	switch ($accion) {
		case 'NUEVO':
			$resultado = array();
			try {
				
				$nombre = $_POST['nombre'];
				$estado = $_POST['estado'];
				$codigobarra = $_POST['codigobarra'];
				$pventa = $_POST['pventa'];
				$pcompra = $_POST['pcompra'];
				$stock = $_POST['stock'];
				$idunidad = $_POST['idunidad'];
				$idcategoria = $_POST['idcategoria'];
				$idafectacion = $_POST['idafectacion'];
				$afectoicbper = $_POST['afectoicbper'];
				$stockseguridad = $_POST['stockseguridad'];

				$existeProducto = $objPro->verificarDuplicado($nombre);
				if($existeProducto->rowCount()>0){
					throw new Exception("Existe un Producto con el mismo nombre", 1);
				}

				$objPro->insertarProducto($nombre, $codigobarra, $pventa, $pcompra, $stock, $idunidad, $idcategoria, $idafectacion, $afectoicbper, $estado, $stockseguridad);
				$resultado['correcto']=1;
				$resultado['mensaje']="Producto Registrado de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo registrar el producto. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CONSULTAR_PRODUCTO':
			try {
				$idproducto = $_POST['idproducto'];
				$resultado = $objPro->consultarProductoPorId($idproducto);
				$resultado = $resultado->fetch(PDO::FETCH_NAMED);
				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado = array('correcto'=>0, 'mensaje'=>$e->getMessage());
				echo json_encode($resultado);
			}
			break;

		case 'ACTUALIZAR':
			$resultado = array();
			try {
				
				$nombre = $_POST['nombre'];
				$estado = $_POST['estado'];
				$codigobarra = $_POST['codigobarra'];
				$pventa = $_POST['pventa'];
				$pcompra = $_POST['pcompra'];
				$stock = $_POST['stock'];
				$idunidad = $_POST['idunidad'];
				$idcategoria = $_POST['idcategoria'];
				$idafectacion = $_POST['idafectacion'];
				$afectoicbper = $_POST['afectoicbper'];
				$stockseguridad = $_POST['stockseguridad'];
				$idproducto = $_POST['idproducto'];

				$existeProducto = $objPro->verificarDuplicado($nombre, $idproducto);
				if($existeProducto->rowCount()>0){
					throw new Exception("Existe un Producto con el mismo nombre", 1);
				}

				$objPro->actualizarProducto($nombre, $codigobarra, $pventa, $pcompra, $stock, $idunidad, $idcategoria, $idafectacion, $afectoicbper, $estado, $stockseguridad, $idproducto);
				$resultado['correcto']=1;
				$resultado['mensaje']="Producto Actualizado de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo actualizar el producto. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CAMBIAR_ESTADO_PRODUCTO':
			try {
				$idproducto = $_POST['idproducto'];
				$estado = $_POST['estado'];
				$arrayEstado = array('ANULADO', 'ACTIVADO', 'ELIMINADO');

				$objPro->actualizarEstadoProducto($idproducto, $estado);
				$resultado = array('correcto'=>1, 'mensaje'=>'El Producto ha sido '.$arrayEstado[$estado].' de forma satisfactoria.');

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado = array('correcto'=>0, 'mensaje'=>$e->getMessage());
				echo json_encode($resultado);
			}
			break;

		case 'SUBIR_IMAGEN':
			try {
				$idproducto = $_POST['idproducto'];
				if(empty($_FILES)){
					throw new Exception("No se encontro la imagen a subir", 1);
				}

				$imagenfile = $_FILES['uploadFile'];
				$nombre_imagen = "IMG_".$idproducto.$imagenfile['name'];
				$ruta = "imagen/productos/".$nombre_imagen;

				move_uploaded_file($imagenfile['tmp_name'], '../'.$ruta);
				
				$objPro->actualizarImagen($idproducto, $ruta);

				echo '[]';

			} catch (Exception $e) {
				$resultado = array('correcto'=>0, 'mensaje'=>$e->getMessage());
				echo json_encode($resultado);
			}
			break;

		case 'DETALLE_VENTA_PRODUCTO':
			try {
				$idproducto = $_POST['idproducto'];
				$resultado = $objPro->consultarProductoPorId($idproducto);
				$resultado = $resultado->fetch(PDO::FETCH_NAMED);

				$listado = $objPro->detalleVentaPorProducto($idproducto);
				$listado = $listado->fetchAll(PDO::FETCH_NAMED);


			   $resultado = '<table class="table table-bordered table-sm table-hover table-striped">';
			   $resultado .= '<thead>';
			   $resultado .= '<tr>
							   <th>FECHA</th>
							   <th>DOCUMENTO</th>
							   <th>CLIENTE</th>
							   <th>CANTIDAD</th>
						   </tr>
						   </thead>
						   <tbody>';
			   $total_vendido = 0;
			   foreach($listado as $k=>$v){
				   $resultado.= '<tr>
								   <td>'.$v['fecha'].'</td>
								   <td>'.$v['documento'].'</td>
								   <td>'.$v['cliente'].'</td>
								   <td>'.$v['cantidad'].'</td>
							   </tr>';
				   $total_vendido += $v['cantidad'];
			   }
			   $resultado.='</tbody>';
			   $resultado.='<tfoot><tr><th colspan="3" class="text-right">TOTAL VENDIDO</th><th class="text-bold">'.number_format($total_vendido,2).'</th></tr></tfoot>';
			   $resultado.='</table>';

			   echo $resultado;

		   } catch (Exception $e) {
			   $resultado = $e->getMessage();
			   echo $resultado;
		   }
		   break;
		
		default:
			// code...
			break;
	}

}


?>