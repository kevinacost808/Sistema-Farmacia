<?php
require_once('conexion.php');

class clsUsuario{

	function verificarUsuario($usuario, $clave){
		$sql = "SELECT us.*, pe.nombre as 'perfil' FROM usuario us INNER JOIN perfil pe ON us.idperfil=pe.idperfil WHERE us.usuario=:usuario AND us.clave=SHA1(:clave) AND us.estado=1 ";
		$parametros = array(':usuario'=>$usuario, ':clave'=>$clave);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);

		return $pre;
	}

	function listarUsuario($nombre, $estado){
		$sql = "SELECT us.*, pe.nombre as 'perfil' FROM usuario us INNER JOIN perfil pe ON us.idperfil=pe.idperfil WHERE us.estado<2 ";
		$parametros = array();

		if($nombre!=""){
			$sql .= " AND us.nombre LIKE :nombre ";
			$parametros[':nombre'] = '%'.$nombre.'%';
		}

		if($estado!=""){
			$sql .= " AND us.estado = :estado ";
			$parametros[':estado'] = $estado;
		}

		$sql .= "ORDER BY us.nombre ASC";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);

		return $pre;
	}

	function insertarUsuario($nombre, $usuario, $clave, $idperfil, $estado){
		$sql = "INSERT INTO usuario VALUES(null, :nombre, :usuario, SHA1(:clave), :idperfil, :estado)";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado, ':usuario'=>$usuario, ':clave'=>$clave, ':idperfil'=>$idperfil);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function verificarDuplicado($usuario, $idusuario=0){
		$sql = "SELECT * FROM usuario WHERE estado<2 AND usuario=:usuario AND idusuario<>:idusuario ";
		$parametros = array(':usuario'=>$usuario, ':idusuario'=>$idusuario);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarUsuarioPorId($idusuario){
		$sql = "SELECT * FROM usuario WHERE idusuario=:idusuario";
		$parametros = array(':idusuario'=>$idusuario);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarUsuario($nombre, $usuario, $clave, $idperfil, $estado, $idusuario){
		$sql = "UPDATE usuario SET nombre=:nombre, estado=:estado, usuario=:usuario, idperfil=:idperfil ";
		$parametros = array(':nombre'=>$nombre, ':estado'=>$estado, ':idusuario'=>$idusuario, ':usuario'=>$usuario, ':idperfil'=>$idperfil);

		if($clave!=""){
			$sql .= ", clave=SHA1(:clave) ";
			$parametros[':clave'] = $clave;
		}

		$sql .= " WHERE idusuario=:idusuario";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarEstadoUsuario($idusuario, $estado){
		$sql = "UPDATE usuario SET estado=:estado WHERE idusuario=:idusuario";
		$parametros = array(':estado'=>$estado, ':idusuario'=>$idusuario);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}
}



?>