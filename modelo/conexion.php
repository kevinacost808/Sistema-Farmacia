<?php
session_start();
if(!isset($_SESSION['idusuario'])){
	if($_POST['accion']!="INICIAR_SESION"){
  		header('Location: index.php');
	}
}

$manejador = 'mysql';
$servidor = 'localhost';
$usuario = 'root';
$pass = '';
$base = 'farmacia';

$cadena = "$manejador:host=$servidor;dbname=$base";

$cnx = new PDO($cadena, $usuario, $pass, array(PDO::ATTR_PERSISTENT => "true", PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

// $filtro = "1; INSERT INTO usuario VALUES(NULL, 'Antonio', 'antonio','123456',1,1)";
// $sql = "SELECT * FROM producto WHERE estado=:valor";
// $parametros = array(':valor'=>$filtro);
// $pre = $cnx->prepare($sql);
// $pre->execute($parametros);

// $resultado = $pre;


// $resultado = $cnx->query($sql);

// $listado = $resultado->fetchAll(PDO::FETCH_NAMED);

// echo '<pre>';
// print_r($listado);
// echo '</pre>';

// foreach($listado as $indice=>$valor){
// 	echo $valor['nombre'].' - '.$valor['codigobarra'].'<br>';
// }


?>