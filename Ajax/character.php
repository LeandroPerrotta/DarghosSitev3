<?php
class Ajax_character
{
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
		
		$monsters = Monsters::GetInstance();
		
		if(!Strings::canUseName($name) || $monsters->loadByName($name))
		{
			$result["text"] = "Nome não permitido.";		
			return $result;		
		}
		
		$character = new Character();
		
		if($character->loadByName($name))
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
		
		$account = Account::loadLogged();
	
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
			
		$character = new Character();	
			
		$character->setName($name);
		$character->setAccountId($account->getId());
		$character->setGroup(GROUP_PLAYERS);
		$character->setSex($sex->Get());
		$character->setVocation($voc_t->Get());
		$character->setExperience(4200);
		$character->setLevel(8);
		$character->setMagLevel(0);
		$character->setHealth(185);
		$character->setMana(35);
		$character->setCap(470);
		$character->setTownId(6);
		$character->setLookType($outfitType);
		$character->setConditions(null);
		$character->setComment("");
		$character->setCreation(time());
		
		$character->save();
		
		$result["error"] = false;
		return $result;	
	}	
}