<?php
if($_GET['value'] == "log")
{
	include "../configs/databases.php";
	include "../classes/mysql.php";
	
	$db = new MySQL();
	$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);	
	
	$query = $db->query("INSERT INTO wb_pingtest VALUES ('{$_GET['pingavg']}', '{$_SERVER['REMOTE_ADDR']}', '".time()."')");
	
	echo $_GET['value'];
}
else
{
	echo $_GET['value'];
}	
?>