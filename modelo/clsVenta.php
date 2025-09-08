<?php
require_once('conexion.php');

class clsVenta{

    function ObtenerDatosVenta($idventa) {
        global $cnx; // Conexión a la base de datos
        $datosVenta = array();

        try {
            // Consulta para obtener los datos de la venta y del cliente
            $stmt = $cnx->prepare("SELECT v.serie, v.correlativo, v.fecha, v.total, v.total_gravado, v.total_exonerado, 
                                        v.total_inafecto, v.total_igv, v.total_icbper, 
                                        v.total_descuento, v.idcliente, v.idtipocomprobante, 
                                        c.nrodocumento, c.nombre AS nombreCliente
                                    FROM venta v
                                    JOIN cliente c ON v.idcliente = c.idcliente
                                    INNER JOIN (SELECT de.*, pr.nombre FROM detalle de INNER JOIN producto pr ON de.idproducto=pr.idproducto) tbx ON tbx.idventa = v.idventa
                                    WHERE v.idventa = :idventa");
            $stmt->bindParam(':idventa', $idventa);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $venta = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Asignar datos de la venta al array
                $datosVenta["fechaEmision"] = $venta["fecha"];
                $datosVenta["total"] = $venta["total"];
                $datosVenta["total_gravado"] = $venta["total_gravado"];
                $datosVenta["total_exonerado"] = $venta["total_exonerado"];
                $datosVenta["total_inafecto"] = $venta["total_inafecto"];
                $datosVenta["total_igv"] = $venta["total_igv"];
                $datosVenta["total_icbper"] = $venta["total_icbper"];
                $datosVenta["total_descuento"] = $venta["total_descuento"];
                $datosVenta["idcliente"] = $venta["idcliente"];
                $datosVenta["nrodocumento"] = $venta["nrodocumento"];
                $datosVenta["nombreCliente"] = $venta["nombreCliente"];
                $datosVenta["idtipocomprobante"] = $venta["idtipocomprobante"];
                $datosVenta["serie"] = $venta["serie"];
                $datosVenta["correlativo"] = $venta["correlativo"];
                
                // Obtener detalles de la venta
                $stmtDetalles = $cnx->prepare("SELECT d.idproducto, d.cantidad, d.pventa, d.igv, d.icbper, d.descuento, d.idafectacion 
                                                FROM detalle d 
                                                WHERE d.idventa = :idventa");
                $stmtDetalles->bindParam(':idventa', $idventa);
                $stmtDetalles->execute();
                
                $detalles = array();
                while ($detalle = $stmtDetalles->fetch(PDO::FETCH_ASSOC)) {
                    $detalles[] = $detalle;
                }
                $datosVenta["detalles"] = $detalles;

            } else {
                throw new Exception("Venta no encontrada.");
            }

        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error al obtener datos de la venta: " . $e->getMessage());
            return null; // O manejar el error de otra manera
        }

        return $datosVenta;
    }



	function listarVenta($desde, $hasta, $cliente, $idtipocomprobante, $correlativo, $idusuario, $estado ){
		$sql = "
			SELECT
				ve.idventa,
				DATE_FORMAT(ve.fecha,'%d/%m/%Y') as fecha,
				tc.nombre AS 'comprobante',
				ve.serie,
				ve.correlativo,
				cl.nombre AS 'cliente',
				ve.total,
                ve.xml,ve.cdr,ve.estadoSunat,
				ve.estado,
				GROUP_CONCAT('[',tbx.cantidad,']',' ',tbx.nombre SEPARATOR '<br>') as 'producto'
			FROM
				venta ve
				INNER JOIN tipocomprobante tc ON ve.idtipocomprobante = tc.idtipocomprobante
				LEFT JOIN cliente cl ON ve.idcliente = cl.idcliente 
				INNER JOIN (SELECT de.*, pr.nombre FROM detalle de INNER JOIN producto pr ON de.idproducto=pr.idproducto WHERE de.estado<2) tbx ON tbx.idventa = ve.idventa
			WHERE
				ve.estado <2
		";
		$parametros = array();

		if($desde!=""){
			$sql .= " AND ve.fecha>=:desde ";
			$parametros[':desde'] = $desde;
		}

		if($hasta!=""){
			$sql .= " AND ve.fecha<=:hasta ";
			$parametros[':hasta'] = $hasta;
		}

		if($cliente!=""){
			$sql .= " AND cl.nombre LIKE :cliente ";
			$parametros[':cliente'] = '%'.$cliente.'%';
		}

		if($idtipocomprobante!=""){
			$sql .= " AND ve.idtipocomprobante = :idtipocomprobante ";
			$parametros[':idtipocomprobante'] = $idtipocomprobante;
		}

		if($correlativo!=""){
			$sql .= " AND ve.correlativo LIKE :correlativo ";
			$parametros[':correlativo'] = '%'.$correlativo.'%';
		}

		if($idusuario!=""){
			$sql .= " AND ve.idusuario = :idusuario ";
			$parametros[':idusuario'] = $idusuario;
		}


		if($estado!=""){
			$sql .= " AND ve.estado = :estado ";
			$parametros[':estado'] = $estado;
		}

		$sql .=" GROUP BY ve.idventa ORDER BY ve.fecha DESC";

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function consultarComprobante(){
		$sql = "SELECT * FROM tipocomprobante WHERE estado=1 ";

		global $cnx;
		$pre = $cnx->query($sql);
		return $pre;
	}

	function consultarVenta($idventa){
        $sql = "SELECT * FROM venta WHERE idventa=? ";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute(array($idventa));
        return $pre;
    }

    function consultarDetalleVenta($idventa){
        $sql = "SELECT d.*, p.nombre 
            FROM detalle d INNER JOIN producto p ON d.idproducto=p.idproducto
            WHERE d.idventa=? AND d.estado<>2 ORDER BY d.iddetalle ASC";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute(array($idventa));
        return $pre;
    }

    function consultarVentaExistente($idtipocomprobante, $serie, $correlativo, $idventa=0){
        $sql = "SELECT * FROM venta WHERE idtipocomprobante=? AND serie=? 
                AND correlativo=? AND idventa<>?";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute(array($idtipocomprobante,$serie, $correlativo, $idventa));
        return $pre;
    }

    function insertar($venta){
        $sql = "INSERT INTO 
                venta(idventa, fecha, idcliente, idtipocomprobante, serie, correlativo,
                    total, total_gravado, total_exonerado, total_inafecto, total_igv,
                    total_icbper, total_descuento, formapago, idmoneda, vencimiento, 
                    guiaremision, ordencompra, idusuario, estado) 
                VALUES (NULL,:fecha, :idcliente, :idtipocomprobante, :serie, :correlativo,
                    :total, :total_gravado, :total_exonerado, :total_inafecto, :total_igv,
                    :total_icbper, :total_descuento, :formapago, :idmoneda, :vencimiento, 
                    :guiaremision, :ordencompra, :idusuario, :estado)";
        global $cnx;
        $parametros = array(
            ":fecha"            =>$venta["fecha"], 
            ":idcliente"        =>$venta["idcliente"], 
            ":idtipocomprobante"=>$venta["idtipocomprobante"], 
            ":serie"            =>$venta["serie"], 
            ":correlativo"      =>$venta["correlativo"], 
            ":total"            =>$venta["total"], 
            ":total_gravado"    =>$venta["total_gravado"],    
            ":total_exonerado"  =>$venta["total_exonerado"], 
            ":total_inafecto"   =>$venta["total_inafecto"], 
            ":total_igv"        =>$venta["total_igv"],
            ":total_icbper"     =>$venta["total_icbper"], 
            ":total_descuento"  =>$venta["total_descuento"], 
            ":formapago"        =>$venta["formapago"], 
            ":idmoneda"         =>$venta["idmoneda"], 
            ":vencimiento"      =>$venta["vencimiento"], 
            ":guiaremision"     =>$venta["guiaremision"], 
            ":ordencompra"      =>$venta["ordencompra"], 
            ":idusuario"        =>$_SESSION["idusuario"], 
            ":estado"           =>1
        );
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function insertarDetalle($detalle){
        $sql = "INSERT INTO detalle(iddetalle, idventa, idproducto, cantidad, unidad,
                            pventa, igv, icbper, descuento, total, idafectacion, estado)
                VALUES (NULL, :idventa, :idproducto, :cantidad, :unidad,
                            :pventa, :igv, :icbper, :descuento, :total, :idafectacion, :estado)";
        global $cnx;
        $pre = $cnx->prepare($sql);
        
        foreach($detalle as $k=>$v){
            $parametros = array(
                ":idventa"      =>$v["idventa"], 
                ":idproducto"   =>$v["idproducto"], 
                ":cantidad"     =>$v["cantidad"], 
                ":unidad"       =>$v["unidad"],
                ":pventa"       =>$v["pventa"], 
                ":igv"          =>$v["igv"], 
                ":icbper"       =>$v["icbper"], 
                ":descuento"    =>$v["descuento"], 
                ":total"        =>$v["total"], 
                ":idafectacion" =>$v["idafectacion"], 
                ":estado"       =>1
            );
            $pre->execute($parametros);  
        }
    }

    function actualizarSunar($xml, $cdr, $estadoSunat, $idventa) {
        $sql = "UPDATE venta 
                SET xml = :xml, cdr = :cdr , estadoSunat = :estadoSunat
                WHERE idventa = :idventa";
        global $cnx;

        $parametros = array(
            ':idventa' => $idventa,
            ':xml'     => $xml,
            ':cdr'     => $cdr,
            ':estadoSunat'     => $estadoSunat
        );

        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }


    function cambiarEstadoDetalle($idventa, $estado){
        $sql = "UPDATE detalle SET estado=:estado WHERE idventa=:idventa AND estado<>2";
        global $cnx;
        $parametros = array(':idventa'=>$idventa,':estado'=>$estado);
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function actualizarEstado($idventa, $estado){
        $sql = "UPDATE venta SET estado=:estado WHERE idventa=:idventa";
        global $cnx;
        $parametros = array(":idventa"=>$idventa, ":estado"=>$estado);
        $pre= $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function actualizar($venta){
        $sql = "UPDATE venta 
                SET fecha=:fecha, idcliente=:idcliente, idtipocomprobante=:idtipocomprobante,
                    serie=:serie, correlativo=:correlativo, total=:total, 
                    total_gravado=:total_gravado, total_exonerado=:total_exonerado, 
                    total_inafecto=:total_inafecto, total_igv=:total_igv,
                    total_icbper=:total_icbper, total_descuento=:total_descuento, 
                    formapago=:formapago, idmoneda=:idmoneda, vencimiento=:vencimiento, 
                    guiaremision=:guiaremision, ordencompra=:ordencompra, 
                    idusuario=:idusuario, estado=:estado 
                WHERE idventa=:idventa";
        global $cnx;
        $parametros = array(
            ":idventa"          =>$venta["idventa"],
            ":fecha"            =>$venta["fecha"], 
            ":idcliente"        =>$venta["idcliente"], 
            ":idtipocomprobante"=>$venta["idtipocomprobante"], 
            ":serie"            =>$venta["serie"], 
            ":correlativo"      =>$venta["correlativo"], 
            ":total"            =>$venta["total"], 
            ":total_gravado"    =>$venta["total_gravado"],    
            ":total_exonerado"  =>$venta["total_exonerado"], 
            ":total_inafecto"   =>$venta["total_inafecto"], 
            ":total_igv"        =>$venta["total_igv"],
            ":total_icbper"     =>$venta["total_icbper"], 
            ":total_descuento"  =>$venta["total_descuento"], 
            ":formapago"        =>$venta["formapago"], 
            ":idmoneda"         =>$venta["idmoneda"], 
            ":vencimiento"      =>$venta["vencimiento"], 
            ":guiaremision"     =>$venta["guiaremision"], 
            ":ordencompra"      =>$venta["ordencompra"], 
            ":idusuario"        =>$_SESSION["idusuario"], 
            ":estado"           =>$venta["estado"]
        );
        $pre= $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function consultarSeries($idtipocomprobante){
        $sql = "SELECT * FROM serie WHERE idtipocomprobante=:idtipocomprobante AND estado=1";
        $parametros = array(':idtipocomprobante'=>$idtipocomprobante);
        global $cnx;
        $pre= $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;        
    }

    function obtenerCorrelativo($idtipocomprobante, $serie){
        $sql = "SELECT correlativo FROM serie WHERE idtipocomprobante=? AND serie=? AND estado=1";
        $parametros = array($idtipocomprobante, $serie);
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);

        if($pre->rowCount()>0){
            $pre = $pre->fetch(PDO::FETCH_NUM);
            $pre = $pre[0]+1;
        }else{
            $pre = 0;
        }
        return $pre;
    }

    function actualizarCorrelativo($idtipocomprobante, $serie, $correlativo){
        $sql = "UPDATE serie SET correlativo=? WHERE idtipocomprobante=? AND serie=? AND estado=1";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute(array($correlativo, $idtipocomprobante,$serie));
        return $pre;
    }

    function listarVentasPorProducto($idproducto, $desde, $hasta){
        $sql = "SELECT v.idventa, DATE_FORMAT(v.fecha,'%d/%m/%Y') fecha, v.serie, v.correlativo, tc.nombre comprobante,
                        c.nombre cliente, d.cantidad, d.unidad, d.pventa, d.total 
                FROM venta v INNER JOIN tipocomprobante tc ON v.idtipocomprobante=tc.idtipocomprobante
                INNER JOIN detalle d ON v.idventa=d.idventa
                LEFT JOIN cliente c ON v.idcliente=c.idcliente
                WHERE v.estado=1 AND d.estado=1 AND d.idproducto=:idproducto ";
        $parametros = array(':idproducto'=>$idproducto);
        if($desde!=""){
            $sql.=" AND v.fecha>=:desde ";
            $parametros[':desde']=$desde;
        }
        if($hasta!=""){
            $sql.=" AND v.fecha<=:hasta ";
            $parametros[':hasta']=$hasta;
        }

        $sql.=" ORDER BY v.fecha DESC";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function obtenerComprobante($id){
        $sql = "SELECT * FROM tipocomprobante WHERE idtipocomprobante=?";
        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute(array($id));
        return $pre;        
    }

    function listarMoneda(){
    	$sql = "SELECT * FROM moneda WHERE estado=1 ";
        global $cnx;
        $pre = $cnx->query($sql);
        return $pre;   
    }

   function reporteVentaPorMes($desde, $hasta, $idtipocomprobante, $idusuario){
        $sql = "SELECT 
                    YEAR(fecha) AS 'anio',
                    MONTH(fecha) AS 'mes',
                    SUM(total) AS 'total' 
                FROM
                    venta 
                WHERE
                    estado = 1 ";
        $parametros = array();

        if($desde!=""){
            $sql .= " AND fecha >= :desde ";
            $parametros[':desde'] = $desde;
        }

        if($hasta!=""){
            $sql .= " AND fecha <= :hasta ";
            $parametros[':hasta'] = $hasta;
        }

        if($idtipocomprobante!=""){
            $sql .= " AND idtipocomprobante = :idtipocomprobante ";
            $parametros[':idtipocomprobante'] = $idtipocomprobante;
        }

                    
        if($idusuario!=""){
            $sql .= " AND idusuario = :idusuario ";
            $parametros[':idusuario'] = $idusuario;
        }

                    
        $sql .= "  GROUP BY
                    YEAR ( fecha ),
                    MONTH ( fecha ) 
                ORDER BY
                    YEAR ( fecha ) ASC,
                    MONTH ( fecha ) ASC ";

        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
   }

    function ventasDelDia(){
        $sql = "SELECT formapago, SUM(total) as 'total', COUNT(idventa) as 'nro_ventas' FROM venta WHERE estado=1 AND fecha=CURDATE() GROUP BY formapago ";
        $parametros = array();

        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function productosMasVendidos(){
        $sql="SELECT de.idproducto, pr.nombre, un.descripcion as 'unidad', SUM(de.cantidad) as 'cantidad' FROM venta ve INNER JOIN detalle de ON ve.idventa=de.idventa INNER JOIN producto pr ON de.idproducto=pr.idproducto INNER JOIN unidad un ON de.unidad=un.idunidad WHERE ve.estado=1 AND de.estado=1 AND ve.fecha=CURDATE() GROUP BY pr.idproducto ORDER BY cantidad DESC LIMIT 10;";
        $parametros = array();

        global $cnx;
        $pre = $cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

}
?>