<?
use Core\Tools;
$account = \Framework\Account::loadLogged();

function checkAvailableTows(\Framework\Account $account){
    
    $towns_list = array(); 
    $towns_list[] = \t_Towns::Quendor;
    
    return $towns_list;
}

$available_towns = checkAvailableTows($account);

if($_POST)
{		
	$player = new \Framework\Player();

	$monsters = \Framework\Monsters::GetInstance();
	
	if(!$_POST["player_name"] or !$_POST["player_vocation"] or !$_POST["player_sex"])
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
	}
	elseif(!\Core\Strings::canUseName($_POST["player_name"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_NAME);
	}
	elseif($player->loadByName($_POST["player_name"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NAME_ALREADY_USED);
	}
	elseif($monsters->loadByName($_POST["player_name"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_NAME);
	}
	elseif(count($account->getCharacterList()) == 10 && $account->getGroup() != t_Group::PlayerNonLogout)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS);
	}
	else
	{
		$vocation = new t_Vocation();
		$vocation->SetByName($_POST["player_vocation"]);
		
		$valid_vocations = array(1, 2, 3, 4);
		
		//warriors for now only creable in Darghos realm
		if($player->getWorldId() == t_Worlds::Uniterian)
		    array_push($valid_vocations, 9);
		
		if(!in_array($vocation->Get(), $valid_vocations))
			$vocation->Set(1);
		
		
		$_genre_id = t_Genre::GetByString($_POST["player_sex"]);
		
		if($_genre_id == t_Genre::Male)
			$outfitType = 128;
		else
			$outfitType = 136;
			
		if(!\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_MULTIWORLD))
			$_POST["player_world"] = \Core\Configs::Get(\Core\Configs::eConf()->DEFAULT_WORLD);
		
		$_world_id = t_Worlds::Get($_POST["player_world"]);
		
		$pvp = true;
		
		$town = t_Towns::Quendor;
		
		/*if(!in_array($town_id, $available_towns)){
		    $town_id = $available_towns[0];
		}*/
		
		$player->setName($_POST["player_name"]);
		$player->setWorldId($_world_id);
		$player->setAccountId($_SESSION['login'][0]);
		$player->setGroup(t_Group::Player);
		$player->setSex($_genre_id);
		$player->setVocation($vocation->Get());
        $player->setExperience(4200);
        $player->setLevel(8);
        $player->setMagLevel(0);
        $player->setHealth(185);
        $player->setMana(35);
        $player->setCap(470);
		$player->setTownId($town);
		$player->setLookType($outfitType);
		$player->setConditions(null);
		$player->setComment("");
		$player->setCreation(time());
		$player->setPvp($pvp);
		
        /*
	    if(Tools::isDruid($player->getVocation()) || Tools::isSorcerer($player->getVocation())){
	        $player->setMagLevel(85);
	        $player->setHealth(1145);
	        $player->setMana(5800);
	        $player->setCap(2390);
	    }
	    elseif(Tools::isPaladin($player->getVocation())){
	        $player->setMagLevel(26);
	        $player->setHealth(2105);
	        $player->setMana(2920);
	        $player->setCap(4310);  
	    }
	    elseif(Tools::isKnight($player->getVocation())){
	        $player->setMagLevel(10);
	        $player->setHealth(3065);
	        $player->setMana(1000);
	        $player->setCap(5270);  		    
	    }*/
		
		$player->save();
		
	    /*
        $player->loadSkills();
	    
	    if(Tools::isDruid($player->getVocation()) || Tools::isSorcerer($player->getVocation())){
	        $player->setSkill(t_Skills::Shielding, 30);
	    }
	    elseif(Tools::isPaladin($player->getVocation())){
	        $player->setSkill(t_Skills::Shielding, 85);
	        $player->setSkill(t_Skills::Distance, 105);
	    }
	    elseif(Tools::isKnight($player->getVocation())){
	        $player->setSkill(t_Skills::Shielding, 95);
	        $player->setSkill(t_Skills::Axe, 95);
	        $player->setSkill(t_Skills::Sword, 95);
	        $player->setSkill(t_Skills::Club, 95);
	    }

	    $player->saveSkills();
         */
	
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CREATED, $_POST["player_name"]);
	}
} 

if($success)	
{
	\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
}
else
{
	if($error)	
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
	}
	
$genre_str = "";
$genreNames = array(
		t_Genre::Female => "Feminino"
		,t_Genre::Male => "Masculino"
);

while(t_Genre::ItValid())
{
	$genre_str .= "<input type=\"radio\" name=\"player_sex\" value=\"".t_Genre::GetString(t_Genre::It())."\" /> {$genreNames[t_Genre::It()]}<br>";
	t_Genre::ItNext();
}

$worlds_str = "";
$worldNames = array(
		t_Worlds::Uniterian => "[Uniterian] Open PvP, Progressão (sem resets), Inaugurado Ago/2014"
		,t_Worlds::Tenerian => "[Tenerian] Open PvP, Seasons, Inaugurado Out/2014"
);
if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_MULTIWORLD))
{
	while(t_Worlds::ItValid())
	{
		$worlds_str .= "<input type=\"radio\" name=\"player_world\" value=\"".t_Genre::It()."\" /> {$worldNames[t_Worlds::It()]}<br>";
		t_Worlds::ItNext();
	}
	
	$world_str = '
	<p>
		<label for="player_world">Mundo</label>	
			'.$worlds_str.'		
	</p>	
	';
}

$pvpSelect = new \Framework\HTML\SelectBox();
$pvpSelect->SetName("player_pvp");
$pvpSelect->AddOption("On", "1");
$pvpSelect->AddOption("Off", "0");

\Core\Main::includeJavaScriptSource("views/character_create.js");

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label>
			<input name="player_name" size="40" type="text" value="" />
		</p>
		
		'.$world_str.'	
		
		<p>
			<label for="player_sex">Sexo</label>		
				'.$genre_str.'
		</p>				        
		
		<p>
			<label for="player_vocation">Vocação</label>	
				<input type="radio" name="player_vocation" value="Sorcerer" /> Sorcerer<br>
				<input type="radio" name="player_vocation" value="Druid" /> Druid<br>
				<input type="radio" name="player_vocation" value="Paladin" /> Paladin<br>
				<input type="radio" name="player_vocation" value="Knight" /> Knight<br>
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>