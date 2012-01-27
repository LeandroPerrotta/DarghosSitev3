<?
if($_POST)
{	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);		
	
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
	elseif(count($account->getCharacterList()) == 10)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS);
	}
	else
	{
		$vocation = new t_Vocation();
		$vocation->SetByName($_POST["player_vocation"]);
		
		if($vocation->Get() > 4)
			$vocation->Set(1);
		
		
		$_genre_id = t_Genre::GetByString($_POST["player_sex"]);
		
		if($_genre_id == t_Genre::Male)
			$outfitType = 128;
		else
			$outfitType = 136;
			
		$player->setName($_POST["player_name"]);
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
		$player->setTownId(6);
		$player->setLookType($outfitType);
		$player->setConditions(null);
		$player->setComment("");
		$player->setCreation(time());
		
		$player->save();
	
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
	
$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input name="player_name" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="player_sex">Sexo</label><br />			
				<input type="radio" name="player_sex" value="'.t_Genre::GetString(t_Genre::Female).'" /> Feminino<br>
				<input type="radio" name="player_sex" value="'.t_Genre::GetString(t_Genre::Male).'" /> Masculino<br>
		</p>		
		
		<p>
			<label for="player_vocation">Vocação</label><br />			
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