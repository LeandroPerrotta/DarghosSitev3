<?php
if($_GET['name'])
{
	$result = false;
	$message = "";		
	
	function proccessPost(&$message, Account $account, Guilds $guild)
	{
		if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
		{
			$message = Lang::Message(LMSG_WRONG_PASSWORD);
			return false;
		}			
		
		$memberLevel = Guilds::GetAccountLevel($account, $guild->GetId());
		$character = $guild->SearchMemberByName($_POST["guild_member"]);
		
		if(!$character)
		{
			$message = Lang::Message(LMSG_GUILD_IS_NOT_MEMBER, $_POST["guild_member"], $_GET['name']);
			return false;
		}
		
		$character->LoadGuild();
		
		if($character->GetGuildLevel() < GUILD_RANK_LEADER)
		{
			if($character->GetGuildLevel() >= $memberLevel)
			{
				$message = Lang::Message(LMSG_GUILD_PERMISSION);
				return false;
			}
		}
		else
		{
			if($memberLevel != GUILD_RANK_LEADER)
			{
				$message = Lang::Message(LMSG_GUILD_PERMISSION);
				return false;				
			}
		}
		
		if($_POST["guild_action"] == "setRank")
		{
			if($character->GetGuildLevel() == GUILD_RANK_LEADER)
			{
				$message = Lang::Message(LMSG_GUILD_PERMISSION);
				return false;
			}
			
			$selectedRank = $guild->SearchRankByLevel($_POST["member_rank"]);
			
			if(!$selectedRank || $selectedRank->GetLevel() == GUILD_RANK_LEADER)
			{
				$message = Lang::Message(LMSG_REPORT);
				return false;
			}
			
			if($selectedRank->GetLevel() == GUILD_RANK_VICE && !$character->isPremium())
			{
				$message = Lang::Message(LMSG_GUILD_RANK_ONLY_PREMIUM);
				return false;				
			}

			if($selectedRank->GetLevel() == GUILD_RANK_VICE && $character->loadAccount()->getGuildLevel() >= GUILD_RANK_VICE)
			{
				$message = Lang::Message(LMSG_GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK);
				return false;
			}			
			
			$character->setGuildRankId($selectedRank->GetId());
		}
		elseif($_POST["guild_action"] == "setNick")
		{
			if(strlen($_POST["member_nick"]) < 3 or strlen($_POST["member_nick"]) > 15)
			{
				$message = Lang::Message(LMSG_GUILD_TITLE_SIZE);
				return false;
			}
			
			$character->setGuildNick($_POST["member_nick"]);
		}
		elseif($_POST["guild_action"] == "exclude")
		{
			if($character->GetGuildLevel() == GUILD_RANK_LEADER)
			{
				$message = Lang::Message(LMSG_GUILD_PERMISSION);
				return false;
			}
			
			if($guild->OnWar())
			{
				$message = Lang::Message(LMSG_GUILD_IS_ON_WAR, $_GET['name']);
				return false;			
			}			
			
			$character->setGuildRankId( null );
		}
		
		$character->save();	
		$message = Lang::Message(LMSG_GUILD_MEMBER_EDITED, $_POST["guild_member"], $_GET['name']);
		
		return true;
	}	
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$character_list = $account->getCharacterList();	
	
	$guild = new Guilds();
	
	if(!$guild->LoadByName($_GET['name']))
	{	
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif(Guilds::GetAccountLevel($account, $guild->GetId()) < GUILD_RANK_VICE)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));		
	}	
	else
	{								
		if($_POST)
		{
			$result = (proccessPost($message, $account, $guild)) ? true : false;		
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
		
			$lowerRank = $guild->SearchRankByLowest();
			
			foreach($guild->Ranks as $rank)
			{
				if($rank->GetLevel() != GUILD_RANK_LEADER)
				{
					$option_ranks .= "<option ".(($rank->GetId() == $lowerRank->GetId()) ? "selected='selected'" : "")." value='{$rank->GetLevel()}'>{$rank->GetName()}</option>";
				}	
				
				foreach($rank->Members as $member)
				{
					$options .= "<option value='{$member->getName()}'>{$member->getName()} ({$rank->GetName()})</option>";
				}
			}
			
			$module .=	'
			<form action="" method="post">
				<fieldset>
					
					<p>
						<label for="guild_member">Lista de Membros</label><br />
						<select name="guild_member">'.$options.'</select>
					</p>					
					
					<p>
						<label for="guild_action">Operações:</label><br />
						
						<ul id="pagelist">
							<li><input name="guild_action" type="radio" value="setRank" checked="checked"> Configurar rank para <select name="member_rank">'.$option_ranks.'</select></li>
							<li><input name="guild_action" type="radio" value="setNick"> Configurar titulo para <input name="member_nick" size="25" type="text" value="" /></li>
							<li><input name="guild_action" type="radio" value="exclude"> Dispençar da Guilda </li>
						</ul>	
					</p>					
					
					<p>
						<label for="account_password">Senha</label><br />
						<input name="account_password" size="40" type="password" value="" />
					</p>						
					
					<div id="line1"></div>
					
					<p>
						<input class="button" type="submit" value="Enviar" />
					</p>
				</fieldset>
			</form>';	
		}	
	}

}		
?>