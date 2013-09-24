<?
use Core\Tools;
$account = \Framework\Account::loadLogged();

function checkAvailableTows(\Framework\Account $account){
    
    $players_list = $account->getCharacterList(\Framework\Account::PLAYER_LIST_BY_ID);
    
    $towns_list = array();
    
    $towns_list[] = \t_Towns::IslandOfPeace;
    
    $was_leave_out_iop = false;
    
    foreach($players_list as $id){
        $player = new \Framework\Player();
        $player->load($id);
        
        if($player->getTownId() != \t_Towns::IslandOfPeace){
            $was_leave_out_iop = true;
            break;
        }
    }
    
    if($was_leave_out_iop){
        $towns_list[] = \t_Towns::Quendor;
        $towns_list[] = \t_Towns::Thorn;
        
        if($account->getPremDays() > 0){
            $towns_list[] = \t_Towns::Aracura;
            $towns_list[] = \t_Towns::Aaragon;
            $towns_list[] = \t_Towns::Salazart;
            $towns_list[] = \t_Towns::Northrend;
            $towns_list[] = \t_Towns::Kashmir;
        }
    }
    
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
		if($player->getWorldId() == t_Worlds::Darghos)
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
		
		$town_id = t_Towns::Get($_POST["player_town"]);
		
		if(!in_array($town_id, $available_towns)){
		    $town_id = $available_towns[0];
		}
		
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
		$player->setTownId($town_id);
		$player->setLookType($outfitType);
		$player->setConditions(null);
		$player->setComment("");
		$player->setCreation(time());
		
		//ugly hack, must be better implemented latter...
		if($player->getWorldId() == t_Worlds::PvPServer){
		    $player->setTownId(1);              //pvp server has only aracura map
		    $player->setLossExperience(10);     //pvp server uses old losses, this must be 10 then 100 (default)
		    
		    $player->setLevel(55);
		    $player->setExperience(2485800);
		    
		    if(Tools::isDruid($player->getVocation()) || Tools::isSorcerer($player->getVocation())){
		        $player->setMagLevel(40);
		        $player->setHealth(420);
		        $player->setMana(1450);
		        $player->setCap(940);
		    }
		    elseif(Tools::isPaladin($player->getVocation())){
		        $player->setMagLevel(13);
		        $player->setHealth(665);
		        $player->setMana(745);
		        $player->setCap(1410);	        
		    }
		    elseif(Tools::isKnight($player->getVocation())){
		        $player->setMagLevel(5);
		        $player->setHealth(890);
		        $player->setMana(275);
		        $player->setCap(1645);		    
		    }
		}
		
		$player->save();
		
		//more ugly hack
		if($player->getWorldId() == t_Worlds::PvPServer){
		    $player->loadSkills();
		    
		    if(Tools::isDruid($player->getVocation()) || Tools::isSorcerer($player->getVocation())){
		        $player->setSkill(t_Skills::Shielding, 20);
		    }
		    elseif(Tools::isPaladin($player->getVocation())){
		        $player->setSkill(t_Skills::Shielding, 50);
		        $player->setSkill(t_Skills::Distance, 75);
		    }
		    elseif(Tools::isKnight($player->getVocation())){
		        $player->setSkill(t_Skills::Shielding, 70);
		        $player->setSkill(t_Skills::Axe, 70);
		        $player->setSkill(t_Skills::Sword, 70);
		        $player->setSkill(t_Skills::Club, 70);
		    }

		    $player->saveSkills();
		}
	
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
		t_Worlds::Darghos => "Darghos (Open PvP, inaugurado jul/2013)"
		,t_Worlds::PvPServer => "PvP Beta (Hardcore PvP, novo, inaugurado set/2013)"
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
	

$townsGlobal_str = "";

$townsSelect = new \Framework\HTML\SelectBox();
$townsSelect->SetName("player_town");

$townsSelect->AddOption("");

foreach($available_towns as $town){
    $townsSelect->AddOption(t_Towns::GetString($town), $town);
}

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
			<label for="player_town">Cidade (somente para o mundo Darghos)</label>
			'.$townsSelect->Draw().'
		</p>		
		
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
				<input type="radio" name="player_vocation" value="Warrior" /> Warrior (somente mundo Darghos)<br>
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>