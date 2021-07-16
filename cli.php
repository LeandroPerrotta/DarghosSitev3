<?php
if(!defined('STDIN'))
{
	header("HTTP/1.0 404 Not Found");
	return;
}

ini_set("display_errors", "0");
error_reporting(E_ALL^E_NOTICE);
$date = date("d-m-y");

$patch = "/var/log/php/darghos_{$date}.log";

if(!file_exists($patch))
{
	$handle = fopen($patch, "x+");
	fwrite($handle, "Error file log generated.\n");
}

ini_set ("error_log", $patch);

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

include "classes/Core/Main.php";
spl_autoload_register("Core\\Main::autoLoad");

Core\Main::Initialize(true, $argv);
Core\Main::$DB->close();
?>
