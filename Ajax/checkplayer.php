<?php
//Cancela se pagina foi chamado diretamente pelo usuario
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
	header("Location: http://www.darghos.com.br");
	exit;
}

//Cancela se nada foi escrito no campo de busca
$inputValue = $_POST["inputValue"];
if(empty($inputValue))
	exit;

//Inclui as porcarias necessarias
//Devido a noobice de leandro teremos que incluir arquivos de classes
include "../configs/definitions.php";
include "../configs/databases.php";
include "../classes/mysql.php";

//Conecta ao banco de dados
$db = new MySQL();
$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);

//Faz a query e retorna codigo html
$query = $db->query("SELECT `name` FROM `players` WHERE `name` LIKE '" . $db->escapeString($inputValue) . "%' LIMIT " . CHARACTERS_AJAX_REQUEST);
if($query && $query->numRows() > 0){
	while($arr = $query->fetchArray()){
		echo "<li onclick='fillSearchBox(\"{$arr['name']}\")'>{$arr['name']}</li>";
	}
}
else{
	exit;
}
?>

