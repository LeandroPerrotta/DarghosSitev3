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

include_once "configs/index.php";
include "classes/Core/Main.php";
Core\Main::Initialize();
	
define('RESPONSE_FIELD_VERIFY', 1);
define('RESPONSE_NEXT_STEP', 2);

list($class, $function) = explode("_", $action);

$_class = "Ajax_" . $class;
if(file_exists("Ajax/{$class}.php"))
{
	include_once("Ajax/{$class}.php");
	if(method_exists($_class, $function))
	{
		eval("\$ret = Ajax_{$class}::{$function}();");
		
		if(is_array($ret))
		{
			echo json_encode($ret);
		}
		else
		{
			echo $ret;
		}
	}
}

Core\Main::$DB->close();
?>
