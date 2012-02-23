<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;

class Misc
{
	function ping()
	{
		if(Configs::Get(Configs::eConf()->STATUS_SHOW_PING))
		{
			//TODO: Precisa ser re-implementado se for voltar a usar.
			/*
				$query = \Core\Main::$DB->query("INSERT INTO `wb_pingtest` VALUES ('{$_POST['pingavg']}', '{$_SERVER['REMOTE_ADDR']}', '".time()."')");
				
			if(!$query)
				echo "mysql_error";
			else
				echo $_POST['value'];
			*/
		}
	}
}
?>