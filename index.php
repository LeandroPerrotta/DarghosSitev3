<?
list($t, $date) = explode(" ", microtime());

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";

include "classes/mysql.php";
include "classes/core.php";

$db_tenerian = new MySQL();
$db_tenerian->connect(DB_TENERIAN_HOST, DB_TENERIAN_USER, DB_TENERIAN_PASS, DB_TENERIAN_SCHEMA);

$core = new Core();

$strings = $core->loadClass("Strings");
	
$layoutDir = "newlay/";

include "modules.php";
include "{$layoutDir}index.php";
?>