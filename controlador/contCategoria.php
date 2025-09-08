<?php
require_once('../modelo/clsCategoria.php');
$accion = $_POST['accion'];

controlador($accion);

function controlador($accion){

	$objCat = new clsCategoria();

	switch ($accion) {
		case 'NUEVO':
			$resultado = array();
			try {
				
				$nombre = $_POST['nombre'];
				$estado = $_POST['estado'];

				$existeCategoria = $objCat->verificarDuplicado($nombre);
				if($existeCategoria->rowCount()>0){
					throw new Exception("Existe una categoria con el mismo nombre", 1);
				}

				$objCat->insertarCategoria($nombre, $estado);
				$resultado['correcto']=1;
				$resultado['mensaje']="Categoria Registrada de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo registrar la categoria. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CONSULTAR_CATEGORIA':
			try {
				$idcategoria = $_POST['idcategoria'];
				$resultado = $objCat->consultarCategoriaPorId($idcategoria);
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
				$idcategoria = $_POST['idcategoria'];

				$existeCategoria = $objCat->verificarDuplicado($nombre, $idcategoria);
				if($existeCategoria->rowCount()>0){
					throw new Exception("Existe una categoria con el mismo nombre", 1);
				}

				$objCat->actualizarCategoria($idcategoria, $nombre, $estado);
				$resultado['correcto']=1;
				$resultado['mensaje']="Categoria Actualizada de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo actualizar la categoria. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CAMBIAR_ESTADO_CATEGORIA':
			try {
				$idcategoria = $_POST['idcategoria'];
				$estado = $_POST['estado'];
				$arrayEstado = array('ANULADA', 'ACTIVADA', 'ELIMINADA');

				$objCat->actualizarEstadoCategoria($idcategoria, $estado);
				$resultado = array('correcto'=>1, 'mensaje'=>'La Categoria ha sido '.$arrayEstado[$estado].' de forma satisfactoria.');

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado = array('correcto'=>0, 'mensaje'=>$e->getMessage());
				echo json_encode($resultado);
			}
			break;
		
		default:
			// code...
			break;
	}

}


?>