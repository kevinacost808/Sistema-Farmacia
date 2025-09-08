<?php
require_once('conexion.php');

class clsPerfil{

	function listarOpcionesMenu($idperfil){
		$sql = "SELECT op.* FROM opcion op INNER JOIN acceso ac ON op.idopcion=ac.idopcion WHERE op.estado=1 AND ac.estado=1 AND ac.idperfil=:idperfil ";
		$parametros = array(':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);

		return $pre;
	}

	function listarPerfil($nombre, $estado){
		$sql = "SELECT * FROM perfil WHERE estado<2 ";
		$parametros = array();

		if($nombre!=""){
			$sql.=" AND nombre LIKE :nombre ";
			$parametros[':nombre'] = '%'.$nombre.'%';
		}

		if($estado!=""){
			$sql.=" AND estado=:estado ";
			$parametros[':estado']=$estado;
		}

		$sql.=" ORDER BY nombre ASC";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function insertarPerfil($nombre, $estado){
		$sql = "INSERT INTO perfil VALUES(null, :nombre, :estado)";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function verificarDuplicado($nombre, $idperfil=0){
		$sql = "SELECT * FROM perfil WHERE estado<2 AND nombre=:nombre AND idperfil<>:idperfil ";
		$parametros = array(':nombre'=>$nombre, ':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarPerfilPorId($idperfil){
		$sql = "SELECT * FROM perfil WHERE idperfil=:idperfil";
		$parametros = array(':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarPerfil($idperfil, $nombre, $estado){
		$sql = "UPDATE perfil SET nombre=:nombre, estado=:estado WHERE idperfil=:idperfil";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado, ':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarEstadoPerfil($idperfil, $estado){
		$sql = "UPDATE perfil SET estado=:estado WHERE idperfil=:idperfil";
		$parametros = array(':estado'=>$estado, ':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function listarOpcion(){
		$sql = "SELECT * FROM opcion WHERE estado=1 AND idopcion_ref>0 ";

		global $cnx;
		$pre = $cnx->query($sql);
		return $pre;
	}

	function verificarPermiso($idperfil, $idopcion){
		$sql = "SELECT * FROM acceso WHERE idperfil=:idperfil AND idopcion=:idopcion ";
		$parametros = array(':idperfil'=>$idperfil, ':idopcion'=>$idopcion);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarPermiso($idperfil, $idopcion, $estado){
		$sql = "UPDATE acceso SET estado=:estado WHERE idperfil=:idperfil AND idopcion=:idopcion ";
		$parametros = array(':idperfil'=>$idperfil, ':idopcion'=>$idopcion, ':estado'=>$estado);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function insertarPermiso($idperfil, $idopcion){
		$sql = "INSERT INTO acceso VALUES(:idperfil, :idopcion, 1)";
		$parametros = array(':idperfil'=>$idperfil, ':idopcion'=>$idopcion);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

}

?>