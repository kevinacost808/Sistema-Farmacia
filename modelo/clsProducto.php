<?php
require_once('conexion.php');

class clsProducto{

	function listarProducto($nombre, $estado, $codigo, $idcategoria, $filtro=''){
		$sql = "SELECT
					pr.*, un.descripcion as 'unidad', ca.nombre as 'categoria' 
				FROM
					producto pr
					INNER JOIN unidad un ON pr.idunidad = un.idunidad
					INNER JOIN categoria ca ON pr.idcategoria = ca.idcategoria 
				WHERE
					pr.estado<2";
		$parametros = array();

		if($nombre!=""){
			$sql.=" AND pr.nombre LIKE :nombre ";
			$parametros[':nombre'] = '%'.$nombre.'%';
		}

		if($estado!=""){
			$sql.=" AND pr.estado=:estado ";
			$parametros[':estado']=$estado;
		}

		if($codigo!=""){
			$sql .= " AND pr.codigobarra LIKE :codigo ";
			$parametros[':codigo'] = '%'.$codigo.'%';
		}

		if($idcategoria!=""){
			$sql .= " AND pr.idcategoria= :idcategoria ";
			$parametros[':idcategoria'] = $idcategoria;
		}

		if($filtro!=""){
			if($filtro=='PCS'){
				$sql .= " AND pr.stock>0 ";
			}else if($filtro=='PSS'){
				$sql .= " AND pr.stock<=0 ";
			}else if($filtro=='PSM'){
				$sql .= " AND pr.stock<pr.stockseguridad ";
			}
		}

		$sql.=" ORDER BY pr.nombre ASC";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarUnidad(){
		$sql = "SELECT * FROM unidad WHERE estado=1 ";

		global $cnx;
		$pre = $cnx->query($sql);
		return $pre;
	}

	function consultarAfectacion(){
		$sql = "SELECT * FROM afectacion ";

		global $cnx;
		$pre = $cnx->query($sql);
		return $pre;
	}

	function insertarProducto($nombre, $codigobarra, $pventa, $pcompra, $stock, $idunidad, $idcategoria, $idafectacion, $afectoicbper, $estado, $stockseguridad){
		$sql = "INSERT INTO `producto`(`idproducto`, `nombre`, `codigobarra`, `pventa`, `pcompra`, `stock`, `idunidad`, `idcategoria`, `idafectacion`, `afectoicbper`, `estado`, `stockseguridad`) VALUES (null,:nombre, :codigobarra, :pventa, :pcompra, :stock, :idunidad, :idcategoria, :idafectacion, :afectoicbper, :estado, :stockseguridad)";
		$parametros = array(':nombre'=>$nombre, ':codigobarra'=>$codigobarra, ':pventa'=>$pventa, ':pcompra'=>$pcompra, ':stock'=>$stock, ':idunidad'=>$idunidad, ':idcategoria'=>$idcategoria, ':idafectacion'=>$idafectacion, ':afectoicbper'=>$afectoicbper, ':estado'=>$estado, ':stockseguridad'=>$stockseguridad);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function verificarDuplicado($nombre, $idproducto=0){
		$sql = "SELECT * FROM producto WHERE estado<2 AND nombre=:nombre AND idproducto<>:idproducto ";
		$parametros = array(':nombre'=>$nombre, ':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarProductoPorId($idproducto){
		$sql = "SELECT * FROM producto WHERE idproducto=:idproducto";
		$parametros = array(':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarProducto($nombre, $codigobarra, $pventa, $pcompra, $stock, $idunidad, $idcategoria, $idafectacion, $afectoicbper, $estado, $stockseguridad, $idproducto){
		$sql = "UPDATE `producto` SET `nombre`=:nombre,`codigobarra`=:codigobarra,`pventa`=:pventa,`pcompra`=:pcompra,`stock`=:stock,`idunidad`=:idunidad,`idcategoria`=:idcategoria,`idafectacion`=:idafectacion,`afectoicbper`=:afectoicbper,`estado`=:estado,`stockseguridad`=:stockseguridad WHERE idproducto=:idproducto";
		$parametros = array(':nombre'=>$nombre, ':codigobarra'=>$codigobarra, ':pventa'=>$pventa, ':pcompra'=>$pcompra, ':stock'=>$stock, ':idunidad'=>$idunidad, ':idcategoria'=>$idcategoria, ':idafectacion'=>$idafectacion, ':afectoicbper'=>$afectoicbper, ':estado'=>$estado, ':stockseguridad'=>$stockseguridad, ':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarEstadoProducto($idproducto, $estado){
		$sql = "UPDATE producto SET estado=:estado WHERE idproducto=:idproducto";
		$parametros = array(':estado'=>$estado, ':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarImagen($idproducto, $ruta){
		$sql = "UPDATE producto SET urlimagen=:urlimagen WHERE idproducto=:idproducto ";
		$parametros = array(':urlimagen'=>$ruta, ':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function actualizarStock($idproducto, $cantidad){
		$sql = "UPDATE producto SET stock=stock+:cantidad WHERE idproducto=:idproducto";
		$parametros = array(':idproducto'=>$idproducto, ':cantidad'=>$cantidad);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function detalleVentaPorProducto($idproducto){
		$sql = "SELECT
					DATE_FORMAT( t1.fecha, '%d/%m/%Y' ) AS 'fecha',
					CONCAT( t3.nombre, ' ', t1.serie, '-', t1.correlativo ) AS 'documento',
					t4.nombre AS 'cliente',
					t2.cantidad,
					t2.pventa,
					t2.total 
				FROM
					venta t1
					INNER JOIN detalle t2 ON t1.idventa = t2.idventa
					INNER JOIN tipocomprobante t3 ON t1.idtipocomprobante = t3.idtipocomprobante
					LEFT JOIN cliente t4 ON t1.idcliente = t4.idcliente 
				WHERE
					t1.estado = 1 
					AND t2.estado = 1 
					AND t2.idproducto = :idproducto
				ORDER BY
					t1.fecha ASC,
					t1.idtipocomprobante ASC,
					t1.correlativo ASC ";
		$parametros = array(':idproducto'=>$idproducto);

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function productosConProblemaStock(){
		$sql = "SELECT pr.idproducto, pr.nombre, un.descripcion as 'unidad', pr.stock, pr.stockseguridad FROM producto pr INNER JOIN unidad un ON pr.idunidad=un.idunidad WHERE pr.estado=1 AND (pr.stock=0 OR pr.stock<pr.stockseguridad) ORDER BY pr.stock ASC";
		$parametros = array();

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

}

?>