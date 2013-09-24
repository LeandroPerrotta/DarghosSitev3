<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;
use Framework\Player as PlayerModel;

class Players
{	
	function Search()
	{
		\Core\Main::$isAjax = true;
		
		//Cancela se nada foi escrito no campo de busca
		$name = $_POST["value"];
		if(empty($name))
			return;
	
		//Faz a query e retorna codigo html
		$query = \Core\Main::$DB->query("SELECT `name` FROM `players` WHERE `name` LIKE '" . \Core\Main::$DB->escapeString($name) . "%' AND `deleted` = 0 ORDER BY `level` DESC, `name` ASC LIMIT " . Configs::Get(Configs::eConf()->AJAX_SEARCH_PLAYERS_COUNT));
		if($query && $query->numRows() > 0){
			$msg = "";
				
			while($arr = $query->fetchArray()){
				$msg .= "<li onclick='fillSearchBox(\"{$arr['name']}\")'>{$arr['name']}</li>";
			}
				
			return $msg;
		}
		else{
			return;
		}
	}	
	
	function Checkname()
	{
		\Core\Main::$isAjax = true;
		$name = $_POST["character_name"];
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_FIELD_VERIFY;
		$result["error"] = true;
	
		if(empty($name) || $name == "" || $name == null)
		{
			$result["text"] = "Digite o nome do personagem.";
			return $result;
		}
	
		$monsters = \Framework\Monsters::GetInstance();
	
		if(!\Core\Strings::canUseName($name) || $monsters->loadByName($name))
		{
			$result["text"] = "Nome não permitido.";
			return $result;
		}
	
		$player = new PlayerModel();
	
		if($player->loadByName($name))
		{
			$result["text"] = "Nome já em uso por outro personagem.";
			return $result;
		}
	
		$result["error"] = false;
		$result["text"] = "Sucesso";
	
		return $result;
	}	
	
	function Create()
	{
		\Core\Main::$isAjax = true;
		$name = $_POST["character_name"];
		$world = $_POST["character_world"];
		$genre = $_POST["character_genre"];
		$vocation = $_POST["character_vocation"];
		$town = \t_Towns::IslandOfPeace;
		
		if(!\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_MULTIWORLD))
			$world = \Core\Configs::Get(\Core\Configs::eConf()->DEFAULT_WORLD);		
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_NEXT_STEP;
	
		$characterNameCheck = $this->checkName($name);
	
		if($characterNameCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}
	
		$account = \Framework\Account::loadLogged();
	
		$voc_t = new \t_Vocation();
		$voc_t->SetByName($vocation);
	
		if($voc_t->Get() > 4)
			$voc_t->Set(1);
	
		$_world_id = \t_Worlds::Get($world);
		$_genre_id = \t_Genre::GetByString($genre);
	
		if($_genre_id == \t_Genre::Male)
			$outfitType = 128;
		else
			$outfitType = 136;
	
		$town_id = null;
	
		$town_id =\ t_Towns::Get($town);
			
		$player = new PlayerModel();
			
		$player->setName($name);
		$player->setWorldId($_world_id);
		$player->setAccountId($account->getId());
		$player->setGroup(\t_Group::Player);
		$player->setSex($_genre_id);
		$player->setVocation($voc_t->Get());
		$player->setExperience(4200);
		$player->setLevel(8);
		$player->setMagLevel(0);
		$player->setHealth(185);
		$player->setMana(35);
		$player->setCap(470);
		$player->setTownId($town_id);
		$player->setLookType($outfitType);
		$player->setConditions(null);
		$player->setComment("");
		$player->setCreation(time());
		
		//ugly hack, must be better implemented latter...
		if($player->getWorldId() == \t_Worlds::PvPServer){
		    $player->setTownId(1);              //pvp server has only aracura map
		    $player->setLossExperience(10);     //pvp server uses old losses, this must be 10 then 100 (default)
		}		
	
		$player->save();
	
		$result["error"] = false;
		return $result;
	}	
	
	function Reborn()
	{
		/*
		 * TODO: Precisa ser re-implementado.
		$inputValue = $_POST["inputValue"];
	
		if(empty($inputValue))
			return;
	
		//Faz a query e retorna codigo html
		$query = \Core\Main::$DB->query("
				SELECT
				`id`, `name` FROM `players`
				WHERE
				`name` = '{$inputValue}' AND `online` = '0' AND `level` >= ".Configs::Get(Configs::eConf()->FIRST_REBORN_LEVEL)." AND `vocation` > '4' AND `vocation` <= '8'");
	
		if($query && $query->numRows() > 0){
		\Core\Main::$DB->query("UPDATE `players` SET `level` = '8', `experience` = '4200', `reborn_level` = '1', health` = '185', `healthmax` = '185', `mana` = '35', `manamax` = '35', `cap` = '470', `town_id` = '6', `posx` = '0', `posy` = '0', `posz` = '0', `vocation` = (`vocation` + 4) WHERE `name` = '{$inputValue}'");
	
		$fetch = $query->fetch();
	
		$storage_query = $db->query("SELECT `value` FROM `player_storage` WHERE `player_id` = '{$fetch->id}' AND `key` = '".STORAGE_REBORNS."'");
		if($storage_query && $storage_query->numRows() > 0){
		$db->query("UPDATE `player_storage` SET `value` = '1' WHERE `player_id` = '{$fetch->id}' AND `key` = '".STORAGE_REBORNS."'");
		} else {
		$db->query("INSERT INTO `player_storage` (`player_id`, `key`, `value`) values ('{$fetch->id}','".STORAGE_REBORNS."','1')");
		}
	
		return "1";
		}
		else{
		return;
		}
		*/
	}	
}

?>