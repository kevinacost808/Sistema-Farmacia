<?php
require_once('../modelo/clsCliente.php');
$accion = $_POST['accion'];

controlador($accion);

function controlador($accion){

	$objCli = new clsCliente();

	switch ($accion) {
		case 'NUEVO':
			$resultado = array();
			try {
				
				$nombre = $_POST['nombre'];
				$estado = $_POST['estado'];
				$idtipodocumento = $_POST['idtipodocumento'];
				$nrodocumento = $_POST['nrodocumento'];
				$direccion = $_POST['direccion'];

				$existeCliente = $objCli->verificarDuplicado($nrodocumento);
				if($existeCliente->rowCount()>0){
					throw new Exception("Existe un cliente con el mismo numero de documento", 1);
				}

				$objCli->insertarCliente($idtipodocumento, $nrodocumento, $nombre, $direccion, $estado);
				$resultado['correcto']=1;
				$resultado['mensaje']="Cliente Registrado de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo registrar el cliente. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CONSULTAR_CLIENTE':
			try {
				$idcliente = $_POST['idcliente'];
				$resultado = $objCli->consultarClientePorId($idcliente);
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
				$idtipodocumento = $_POST['idtipodocumento'];
				$nrodocumento = $_POST['nrodocumento'];
				$direccion = $_POST['direccion'];
				$idcliente = $_POST['idcliente'];

				$existeCliente = $objCli->verificarDuplicado($nrodocumento, $idcliente);
				if($existeCliente->rowCount()>0){
					throw new Exception("Existe un cliente con el mismo numero de documento", 1);
				}

				$objCli->actualizarCliente($idcliente, $idtipodocumento, $nrodocumento, $nombre, $direccion, $estado);
				$resultado['correcto']=1;
				$resultado['mensaje']="Cliente Actualizado de forma Satisfactoria";

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado['correcto']=0;
				$resultado['mensaje']="No se pudo actualizar el cliente. ".$e->getMessage();
				echo json_encode($resultado);
			}
			break;

		case 'CAMBIAR_ESTADO_CLIENTE':
			try {
				$idcliente = $_POST['idcliente'];
				$estado = $_POST['estado'];
				$arrayEstado = array('ANULADO', 'ACTIVADO', 'ELIMINADO');

				$objCli->actualizarEstadoCliente($idcliente, $estado);
				$resultado = array('correcto'=>1, 'mensaje'=>'El cliente ha sido '.$arrayEstado[$estado].' de forma satisfactoria.');

				echo json_encode($resultado);

			} catch (Exception $e) {
				$resultado = array('correcto'=>0, 'mensaje'=>$e->getMessage());
				echo json_encode($resultado);
			}
			break;

		case 'CONSULTAR_DATOS_WS':
			$retorno = array();
            try{
                $idtipodoc = $_POST['idtipodocumento'];
                $nrodocumento =  $_POST['nrodocumento'];
                $retorno = array(
                        "idtipodocumento"=>$_POST['idtipodocumento'],
                        "nombre"=>"",
                        "direccion"=>""
                    );
                
                $existe = $objCli->verificarDuplicado($_POST['nrodocumento']);
                $consultarws = true;
                if($existe->rowCount()>0){
                    $cliente = $existe->fetch(PDO::FETCH_NAMED);
                    $retorno = array(
                        "idtipodocumento"=>$cliente["idtipodocumento"],
                        "nombre"=>$cliente['nombre'],
                        "direccion"=>$cliente['direccion']
                    );
                    $consultarws = false;
                }   

                if($idtipodoc==1 && $consultarws){
                    $ws = "https://dniruc.apisperu.com/api/v1/dni/".$nrodocumento."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imx1aXN0aW1hbmFnb256YWdhQGhvdG1haWwuY29tIn0.IxAceLS9puCS0LdM3yLtHZwzsstZAX6ot6RZdTVAiZc";
                    
                    $opts = array(
					    'ssl' => array(
					        'verify_peer' => false,
					        'verify_peer_name' => false
					    )
					);

					$context = stream_context_create($opts);
                    $datos = file_get_contents($ws,false,$context);
                    $datos = json_decode($datos,true);
                    if(isset($datos['nombres'])){
                        $retorno["nombre"]=$datos['nombres'].' '.$datos['apellidoPaterno'].' '.$datos['apellidoMaterno'];
                    }
                }

                if($idtipodoc==6 && $consultarws){
                    $ws = "https://api.apis.net.pe/v1/ruc?numero=$nrodocumento";
                    $datos = file_get_contents($ws);
                    $datos = json_decode($datos,true);
                    if(isset($datos['nombre'])){
                        $retorno['nombre'] = $datos['nombre'];
                        $retorno['direccion'] = $datos['direccion'].' '.$datos['distrito'].'-'.$datos['provincia'].'-'.$datos['departamento'];
                    }
                }                    
            }catch(Exception $ex){
                $retorno = array();
            }
            echo json_encode($retorno);
            break;
		
		default:
			// code...
			break;
	}

}


?>