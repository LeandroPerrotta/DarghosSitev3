<?
list($t, $date) = explode(" ", microtime());

include "configs.php";

include "classes/mysql.php";
include "classes/core.php";

$core = new Core();

$strings = $core->loadClass("Strings");

$db_tenerian = new MySQL();
$db_tenerian->connect(DB_TENERIAN_HOST, DB_TENERIAN_USER, DB_TENERIAN_PASS, DB_TENERIAN_SCHEMA);

//$db_site = new MySQL();
//$db['elerian'] = new MySQL();

include "modules.php";
include "layout/layout.php";
?>