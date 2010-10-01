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
$query = $db->query("SELECT `id`, `name` FROM `players` WHERE `name` = '{$inputValue}' AND `online` = '0' AND `level` >= ".FIRST_REBORN_LEVEL." AND `vocation` > '4' AND `vocation` <= '8'");
if($query && $query->numRows() > 0){	
	$db->query("UPDATE `players` SET `level` = '8', `experience` = '4200', `health` = '185', `healthmax` = '185', `mana` = '35', `manamax` = '35', `cap` = '470', `town_id` = '6', `posx` = '0', `posy` = '0', `posz` = '0', `vocation` = (`vocation` + 4) WHERE `name` = '{$inputValue}'");
	
	$fetch = $query->fetch();
	
	$storage_query = $db->query("SELECT `value` FROM `player_storage` WHERE `player_id` = '{$fetch->id}' AND `key` = '".STORAGE_REBORNS."'");
	if($storage_query && $storage_query->numRows() > 0){
		$db->query("UPDATE `player_storage` SET `value` = '1' WHERE `player_id` = '{$fetch->id}' AND `key` = '".STORAGE_REBORNS."'");
	} else {
		$db->query("INSERT INTO `player_storage` (`player_id`, `key`, `value`) values ('{$fetch->id}','".STORAGE_REBORNS."','1')");
	}
	
	echo "1";
}
else{
	echo "0";
}
?>
