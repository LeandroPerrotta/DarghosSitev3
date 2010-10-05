<?
if($_POST)
{	
	$account = new Account();
	$account->load($_SESSION['login'][0]);		
	
	$character = new Character();

	$monsters = Monsters::GetInstance();
	
	if(!$_POST["player_name"] or !$_POST["player_vocation"] or !$_POST["player_sex"])
	{
		$error = Lang::Message(LMSG_FILL_FORM);
	}
	elseif(!Strings::canUseName($_POST["player_name"]))
	{
		$error = Lang::Message(LMSG_WRONG_NAME);
	}
	elseif($character->loadByName($_POST["player_name"]))
	{
		$error = Lang::Message(LMSG_CHARACTER_NAME_ALREADY_USED);
	}
	elseif($monsters->load($_POST["player_name"]))
	{
		$error = Lang::Message(LMSG_WRONG_NAME);
	}
	elseif(count($account->getCharacterList()) == 10)
	{
		$error = Lang::Message(LMSG_ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS);
	}
	else
	{
		$vocation = new t_Vocation();
		$vocation->SetByName($_POST["player_vocation"]);
		
		$sex = new t_Sex();
		$sex->SetByName($_POST["player_sex"]);		
		
		if($sex->GetByName() == "male")
			$outfitType = 128;
		else
			$outfitType = 136;
			
		$character->setName($_POST["player_name"]);
		$character->setAccountId($_SESSION['login'][0]);
		$character->setGroup(1);
		$character->setSex($sex->Get());
		$character->setVocation($vocation->Get());
		$character->setExperience(4200);
		$character->setLevel(8);
		$character->setMagLevel(0);
		$character->setHealth(185);
		$character->setMana(35);
		$character->setCap(470);
		$character->setTownId(6);
		$character->setLookType($outfitType);
		$character->setConditions(null);
		$character->setDescription("");
		$character->setCreation(time());
		
		$character->save();
	
		$success = Lang::Message(LMSG_CHARACTER_CREATED, $_POST["player_name"]);
	}
} 

if($success)	
{
	Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $success);
}
else
{
	if($error)	
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
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
				<input type="radio" name="player_sex" value="female" /> Feminino<br>
				<input type="radio" name="player_sex" value="male" /> Masculino<br>
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