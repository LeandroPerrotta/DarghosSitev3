<?
use \Core\Configs;
use \Framework\Guilds;
if(Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
{
	$result = false;
	$message = "";
	
	function proccessPost(&$message)
	{
		$account = new \Framework\Account();
		$account->load($_SESSION["login"][0]);
		
		if(\Core\Strings::encrypt($_POST["account_password"]) != $account->getPassword())
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);	
			return false;
		}
		
		if(!in_array($_POST["guild_owner"], $account->getCharacterList()))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);	
			return false;
		}
		
		$guild = new Guilds();
		
		if($guild->LoadByName($_POST["guild_name"]))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NAME_ALREADY_USED, $_POST["guild_name"]);
			return false;
		}
		
		if($account->getGuildLevel() >= \Framework\Guilds::RANK_VICE)
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_ONLY_ONE_VICE_PER_ACCOUNT);
			return false;
		}
		
		if(!\Core\Strings::canUseName($_POST["guild_name"]))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_NAME);
			return false;
		}
		
		$player = new \Framework\Player();
		$player->loadByName($_POST["guild_owner"]);
	
		if($player->LoadGuild())
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_ALREADY_MEMBER_GUILD);
			return false;
		}
		
		$guild->SetWorldId($player->getWorldId());
		$guild->SetName($_POST["guild_name"]);
		$guild->SetOwnerId($player->getId());
		$guild->SetCreationDate(time());
		$guild->SetMotd("Esta é uma guilda recem criada!");
		$guild->SetStatus((\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_GUILD_IN_FORMATION) ? \Framework\Guilds::STATUS_FORMATION : \Framework\Guilds::STATUS_FORMED ));
		$guild->SetFormationTime(time() + 60 * 60 * 24 * Configs::Get(Configs::eConf()->GUILDS_FORMATION_WAIT_DAYS));
		$guild->SetImage(\Framework\Guilds::DEFAULT_IMAGE);
		
		$guild->Save();
		
		$member_rank_id = 0;
		
		//default ranks (member, vice and leader) are created automaticaly by MySQL Triggers
		//we need search in ranks the rank_id of the leader of guild
		$rank = $guild->SearchRankByLevel(\Framework\Guilds::RANK_LEADER); 
		
		if($rank)
		{
			$member_rank_id = $rank->GetId();
		}
		
		$player->setGuildRankId($member_rank_id);
		$player->setGuildNick("");
		$player->setGuildJoinIn(time());
		
		$player->save();
		
		Guilds::LogMessage("Guild {$guild->GetName()} ({$guild->GetId()}) created by {$player->getName()} ({$player->getId()}).");
		$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_CREATED, $_POST["guild_name"], Configs::Get(Configs::eConf()->GUILDS_VICES_TO_FORMATION), Configs::Get(Configs::eConf()->GUILDS_FORMATION_WAIT_DAYS));
		
		return true;
	}
	
	if($_POST)
	{
		$result = (proccessPost($message)) ? true : false;
	}
	
	if($result)	
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $message);
	}
	else
	{	
		if($_POST)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $message);
		}
	
		$account = new \Framework\Account();
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
}
?>	
