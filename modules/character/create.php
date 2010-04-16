<?
$post = Core::extractPost();

$account = new Account();
$account->load($_SESSION['login'][0], "premdays");	

if($post)
{	
	$character = new Character();

	if(!$post[0] or !$post[1] or !$post[2])
	{
		$error = Lang::Message(LMSG_FILL_FORM);
	}
	elseif(!Strings::canUseName($post[0]))
	{
		$error = Lang::Message(LMSG_WRONG_NAME);
	}
	elseif($character->loadByName($post[0]))
	{
		$error = Lang::Message(LMSG_CHARACTER_NAME_ALREADY_USED);
	}
	elseif(count($account->getCharacterList()) == 10)
	{
		$error = Lang::Message(LMSG_ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS);
	}
	else
	{
		if($post[1] == "male")
			$outfitType = 128;
		else
			$outfitType = 136;
		
		$character->setName($post[0]);
		$character->setAccountId($_SESSION['login'][0]);
		$character->setGroup(1);
		$character->setSex($_sex[$post[1]]);
		$character->setVocation($_vocation[$post[2]]);
		$character->setExperience(4200);
		$character->setLevel(8);
		$character->setMagLevel(0);
		$character->setHealth(185);
		$character->setMana(35);
		$character->setCap(470);
		$character->setTownId(6);
		$character->setLookType($outfitType);
		$character->setConditions(null);
		$character->setGuildNick("");
		$character->setRankId(0);
		$character->setDescription("");
		$character->setCreation(time());
		
		$character->save();
	
		$success = Lang::Message(LMSG_CHARACTER_CREATED, $post[0]);
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
			<label for="player_sex">Vocação</label><br />			
				<input type="radio" name="player_vocation" value="sorcerer" /> Sorcerer<br>
				<input type="radio" name="player_vocation" value="druid" /> Druid<br>
				<input type="radio" name="player_vocation" value="paladin" /> Paladin<br>
				<input type="radio" name="player_vocation" value="knight" /> Knight<br>
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>