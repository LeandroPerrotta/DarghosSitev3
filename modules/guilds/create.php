<?
$result = false;
$message = "";

function proccessPost(&$message)
{
	$account = new Account();
	$account->load($_SESSION["login"][0]);
	
	if(Strings::encrypt($_POST["account_password"]) != $account->getPassword())
	{
		$message = Lang::Message(LMSG_WRONG_PASSWORD);	
		return false;
	}
	
	if(!in_array($_POST["guild_owner"], $account->getCharacterList()))
	{
		$message = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);	
		return false;
	}
	
	$guild = new Guilds();
	
	if($guild->LoadByName($_POST["guild_name"]))
	{
		$message = Lang::Message(LMSG_GUILD_NAME_ALREADY_USED, $_POST["guild_name"]);
		return false;
	}
	
	if($account->getGuildLevel() >= GUILD_RANK_VICE)
	{
		$message = Lang::Message(LMSG_GUILD_ONLY_ONE_VICE_PER_ACCOUNT);
		return false;
	}
	
	if(!Strings::canUseName($_POST["guild_name"]))
	{
		$message = Lang::Message(LMSG_WRONG_NAME);
		return false;
	}
	
	$character = new Character();
	$character->loadByName($_POST["guild_owner"]);

	if($character->LoadGuild())
	{
		$message = Lang::Message(LMSG_CHARACTER_ALREADY_MEMBER_GUILD);
		return false;
	}
	
	$guild->SetName($_POST["guild_name"]);
	$guild->SetOwnerId($character->getId());
	$guild->SetCreationDate(time());
	$guild->SetMotd("Aqui está a descrição da guilda {$_POST["guild_name"]}!");
	$guild->SetStatus(GUILD_STATUS_IN_FORMATION);
	$guild->SetFormationTime(time() + 60 * 60 * 24 * GUILDS_FORMATION_DAYS);
	$guild->SetImage(GUILD_DEFAULT_IMAGE);
	
	$guild->Save();
	
	$member_rank_id = 0;
	
	//default ranks (member, vice and leader) are created automaticaly by MySQL Triggers
	//we need search in ranks the rank_id of the leader of guild
	$rank = $guild->SearchRankByLevel(GUILD_RANK_LEADER); 
	
	if($rank)
	{
		$member_rank_id = $rank->GetId();
	}
	
	$character->setGuildRankId($member_rank_id);
	$character->setGuildNick("");
	$character->setGuildJoinIn(time());
	
	$character->save();
	
	$message = Lang::Message(LMSG_GUILD_CREATED, $_POST["guild_name"], GUILDS_VICELEADERS_NEEDED, GUILDS_FORMATION_DAYS);
	
	return true;
}

if($_POST)
{
	$result = (proccessPost($message)) ? true : false;
}

if($result)	
{
	Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $message);
}
else
{	
	if($_POST)	
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $message);
	}

	$account = new Account();
	$account->load($_SESSION["login"][0]);
	
	$char_list = $account->getCharacterList();
	
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

				if(is_array($char_list))
				{	
					foreach($char_list as $player_name)
					{
						$module .=	'<option value="'.$player_name.'">'.$player_name.'</option>';
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
