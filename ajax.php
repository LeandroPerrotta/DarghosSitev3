<?
include "configs/index.php";
include "classes/mysql.php";
include "classes/core.php";

$db = new MySQL();
$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);

$core = new Core();

//$strings = $core->loadClass("Strings");

$script = $_GET["script"];
$value = $_GET["value"];

switch($script)
{
	case "ping":
		$validGet = true;
	break;		
}

if($validGet)
	include("ajax/{$script}.php");

?>