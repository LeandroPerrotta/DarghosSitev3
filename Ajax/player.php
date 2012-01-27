<?php
use \Core\Configs;
class Ajax_player
{
	static function search()
	{
		//Cancela se nada foi escrito no campo de busca
		$name = $_POST["name"];
		if(empty($name))
			return;
		
		//Faz a query e retorna codigo html
		$query = \Core\Main::$DB->query("SELECT `name` FROM `players` WHERE `name` LIKE '" . \Core\Main::$DB->escapeString($name) . "%' LIMIT " . Configs::Get(Configs::eConf()->AJAX_SEARCH_PLAYERS_COUNT));
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
	
	static function checkName()
	{
		$name = $_POST["character_name"];
		
		$result = array();
		$result["response"] = RESPONSE_FIELD_VERIFY;
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
		
		$player = new \Framework\Player();
		
		if($player->loadByName($name))
		{
			$result["text"] = "Nome já em uso por outro personagem.";		
			return $result;			
		}
		
		$result["error"] = false;
		$result["text"] = "Sucesso";			
		
		return $result;		
	}
	
	static function create()
	{
		$name = $_POST["character_name"];
		$genre = $_POST["character_genre"];
		$vocation = $_POST["character_vocation"];
		
		$result = array();
		$result["response"] = RESPONSE_NEXT_STEP;	
		
		$characterNameCheck = self::checkName($name);	
		
		if($characterNameCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}	
		
		$account = \Framework\Account::loadLogged();
	
		$voc_t = new t_Vocation();
		$voc_t->SetByName($vocation);
		
		if($voc_t->Get() > 4)
			$voc_t->Set(1);
		
		$sex = new t_Sex();
		$sex->SetByName($genre);		
		
		if($sex->GetByName() == "male")
			$outfitType = 128;
		else
			$outfitType = 136;
			
		$player = new \Framework\Player();	
			
		$player->setName($name);
		$player->setAccountId($account->getId());
		$player->setGroup(e_Groups::Player);
		$player->setSex($sex->Get());
		$player->setVocation($voc_t->Get());
		$player->setExperience(4200);
		$player->setLevel(8);
		$player->setMagLevel(0);
		$player->setHealth(185);
		$player->setMana(35);
		$player->setCap(470);
		$player->setTownId(6);
		$player->setLookType($outfitType);
		$player->setConditions(null);
		$player->setComment("");
		$player->setCreation(time());
		
		$player->save();
		
		$result["error"] = false;
		return $result;	
	}	
	
	static function reborn()
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