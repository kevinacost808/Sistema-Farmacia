<?php
require_once('conexion.php');

class clsCliente{

	function listarCliente($nombre, $estado, $idtipodocumento, $nrodocumento){
		$sql = "SELECT * FROM cliente WHERE estado<2 ";
		$parametros = array();

		if($nombre!=""){
			$sql.=" AND nombre LIKE :nombre ";
			$parametros[':nombre'] = '%'.$nombre.'%';
		}

		if($estado!=""){
			$sql.=" AND estado=:estado ";
			$parametros[':estado']=$estado;
		}

		if($idtipodocumento!=""){
			$sql.=" AND idtipodocumento=:idtipodocumento ";
			$parametros[':idtipodocumento']=$idtipodocumento;
		}

		if($nrodocumento!=""){
			$sql.=" AND nrodocumento LIKE :nrodocumento ";
			$parametros[':nrodocumento'] = '%'.$nrodocumento.'%';
		}

		$sql.=" ORDER BY nombre ASC";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function insertarCliente($idtipodocumento, $nrodocumento, $nombre, $direccion, $estado){
		$sql = "INSERT INTO cliente VALUES(null, :nombre, :idtipodocumento, :nrodocumento, :direccion, :estado)";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado, ':idtipodocumento'=>$idtipodocumento, ':nrodocumento'=>$nrodocumento, ':direccion'=>$direccion);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function verificarDuplicado($nrodocumento, $idcliente=0){
		$sql = "SELECT * FROM cliente WHERE estado<2 AND nrodocumento=:nrodocumento AND idcliente<>:idcliente ";
		$parametros = array(':nrodocumento'=>$nrodocumento, ':idcliente'=>$idcliente);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarClientePorId($idcliente){
		$sql = "SELECT * FROM cliente WHERE idcliente=:idcliente";
		$parametros = array(':idcliente'=>$idcliente);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarCliente($idcliente, $idtipodocumento, $nrodocumento, $nombre, $direccion, $estado){
		$sql = "UPDATE cliente SET nombre=:nombre, estado=:estado, idtipodocumento=:idtipodocumento, nrodocumento=:nrodocumento, direccion=:direccion WHERE idcliente=:idcliente";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado, ':idtipodocumento'=>$idtipodocumento, ':nrodocumento'=>$nrodocumento, ':direccion'=>$direccion,':idcliente'=>$idcliente);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarEstadoCliente($idcliente, $estado){
		$sql = "UPDATE cliente SET estado=:estado WHERE idcliente=:idcliente";
		$parametros = array(':estado'=>$estado, ':idcliente'=>$idcliente);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function listaTipoDocumento(){
		$sql = "SELECT * FROM tipodocumento WHERE estado=1";
		$parametros = array();

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

}

?>