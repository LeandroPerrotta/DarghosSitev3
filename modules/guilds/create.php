<?
$account = new Account();
$account->load($_SESSION["login"][0], "password");

$list = $account->getCharacterList();

if($_POST)
{	
	$guild = new Guilds();
	$character = new Character();
	$character->loadByName($_POST["guild_owner"], "rank_id, guild_join_date");
		
	if(!in_array($_POST["guild_owner"], $list))
	{
		$error = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
	}
	elseif($account->get("password") != Strings::encrypt($_POST["account_password"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}	
	elseif($guild->loadByName($_POST["guild_name"]))
	{
		$error = Lang::Message(LMSG_GUILD_NAME_ALREADY_USED);
	}	
	elseif($account->isGuildHighMember())
	{
		$error = Lang::Message(LMSG_GUILD_ONLY_ONE_VICE_PER_ACCOUNT);
	}	
	elseif(!Strings::canUseName($_POST["guild_name"]))
	{
		$error = Lang::Message(LMSG_WRONG_NAME);
	}
	elseif($character->loadGuild())
	{
		$error = Lang::Message(LMSG_CHARACTER_ALREADY_MEMBER_GUILD);
	}
	else
	{
		
		$guild->set("name", $_POST["guild_name"]);
		$guild->set("ownerid", $character->get("id"));
		$guild->set("motd", "");
		$guild->set("image", "default_logo.gif");
		$guild->set("creationdata", time());
		$guild->set("formationTime", time() + 60 * 60 * 24 * GUILDS_FORMATION_DAYS);
		
		$guild->save();
		
		$guild->setRank("Lider", 1);
		$guild->setRank("Vice-Lider", 2);
		$guild->setRank("Membro", 3);
		
		$guild->loadRanks();
		$ranks = $guild->getRanks();
		
		foreach($ranks as $rank_id => $values)
		{
			if($values['level'] == 1)
				$leader_id = $rank_id;
		}
		
		$character->set("guild_join_date", time());
		$character->set("rank_id", $leader_id);
		$character->save();
		
		$success = Lang::Message(LMSG_GUILD_CREATED, $_POST["guild_name"], GUILDS_VICELEADERS_NEEDED, GUILDS_FORMATION_DAYS);
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


$module .=	'
	<form action="" method="post">
		<fieldset>
			
			<p>
				<label for="guild_name">Nome da Guild</label><br />
				<input id="guild_name" name="guild_name" size="40" type="text" value="" />
			</p>
			
			<p>
				<label for="guild_owner">Personagem Lider</label><br />
				<select name="guild_owner">
					';

				if(is_array($list))
				{	
					foreach($list as $pid)
					{
						$module .=	'<option value="'.$pid.'">'.$pid.'</option>';
					}
				}

			$module .=	'
				</select>
			</p>			
			
			<p>
				<label for="account_password">Senha</label><br />
				<input id="account_password" name="account_password" size="40" type="password" value="" />
			</p>			
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';

}
?>	
