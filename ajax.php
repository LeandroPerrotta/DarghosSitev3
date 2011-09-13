<?
ini_set("display_errors", 1 );
error_reporting(E_ERROR | E_WARNING); 

//Cancela se pagina foi chamado diretamente pelo usuario
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
	header("Location: http://www.darghos.com.br");
	exit;
}

$action = $_POST["action"];
if(empty($action))
	exit;

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";

include "classes/core.php";
spl_autoload_register("Core::autoLoad");

include "classes/mysql.php";

include "classes/account.php";
include "classes/character.php";
include "classes/tools.php";
include "classes/strings.php";
include "classes/monsters.php";

include "libs/phpmailer/class.phpmailer.php";
	
define('RESPONSE_FIELD_VERIFY', 1);
define('RESPONSE_NEXT_STEP', 2);

Emails::init();

try
{
	$db = new MySQL();
	$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);	
	
	Core::$DB = $db;
	Core::InitPOT();
	
	Strings::filterInputs(true);
}
catch (Exception $e)
{
	echo "Impossivel se conectar ao banco de dados.";
}

list($class, $function) = explode("_", $action);

$_class = "Ajax_" . $class;
if(method_exists($_class, $function))
{
	eval("\$ret = Ajax_{$class}::{$function}();");
	echo json_encode($ret);
}

$db->close();
?>
