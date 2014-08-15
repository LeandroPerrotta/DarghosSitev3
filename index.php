<?
ini_set("display_errors", "On");
error_reporting(E_ERROR);
/*$date = date("d-m-y");

$patch = "/var/log/php/darghos_{$date}.log";

if(!file_exists($patch))
{
	$handle = fopen($patch, "x+");
	fwrite($handle, "Error file log generated.\n");
}

ini_set ("error_log", $patch);*/

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

$module = "";

$menu = array();
$buttons = array();
$pages = array();

include "classes/Core/Main.php";
spl_autoload_register("Core\\Main::autoLoad");

Core\Main::Initialize();

function tr($string){
    return call_user_func_array("Core\Main::translateString", func_get_args());
    //return Core\Main::translateString($string);
}

use \Core\Configs as g_Configs;

if(!Core\Main::$FoundController)
	include "modules.php";

Core\Main::drawTemplate();
Core\Main::onFinish();
?>
