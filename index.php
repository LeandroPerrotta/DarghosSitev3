<?
list($t, $date) = explode(" ", microtime());

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";
$layoutDir = "newlay/";

if(MANUTENTION == 1)
{
	$module .= "
	<p>
	<h3>Todos os nossos servidores est�o desligados para o Principal Game Update. Previs�o para o retorno: 18:00 UTC (Hor�rio de Bras�lia).</h3><br>
	Enquanto aguarda n�o deixe de visitar nosso <a href='http://forum.darghos.com.br/'>Forum</a>, �nico servi�o que ficar� at� a volta depois do Update.
	</p>
	";
	
	include "{$layoutDir}indexsimple.php";
}
else
{	
	include "classes/mysql.php";
	include "classes/core.php";
	
	$db = new MySQL();
	$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);
	
	$core = new Core();
		
	echo $core->formatDate(1240321126);
	
	if(defined('SITE_ROOT_DIR'))
	{	
		if("http://".$_SERVER["HTTP_HOST"].SITE_ROOT_DIR != CONFIG_SITEEMAIL)
		{
			$core->redirect(CONFIG_SITEEMAIL, false); 
		}	
	}
	else
	{		
		if("http://".$_SERVER["HTTP_HOST"] != CONFIG_SITEEMAIL)
		{
			$core->redirect(CONFIG_SITEEMAIL, false); 
		}		
	}	
	
	$strings = $core->loadClass("Strings");
	$tools = $core->loadClass("tools");
	
	include "modules.php";
	include "{$layoutDir}index.php";
}
?>