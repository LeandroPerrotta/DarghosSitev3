<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList();	
	
	$guild = new Guilds();
	
	if(!$guild->loadByName($_GET['name']))
	{	
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif($account->getGuildLevel($guild->get("name")) > 2)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));		
	}	
	else
	{						
		$guild->loadRanks();
		$guild->loadMembersList();
		
		$members = $guild->getMembersList();
		$ranks = $guild->getRanks();		
		
		if($_POST)
		{			
			$canEditMember = true;
			$character = new Character();
			
			if($members[$_POST["guild_member"]]['level'] <= $account->getGuildLevel($guild->get("name")))
				$canEditMember = false;
			
			if(in_array($_POST["guild_member"], $character_list))
				$canEditMember = true;		

			if($members[$_POST["guild_member"]])
			{			
				if($canEditMember)
				{
					if($_POST["guild_action"] == "setRank")
					{		
						$character->loadByName($_POST["guild_member"], "account_id, name, rank_id");
						$accountMember = new Account();
						$accountMember->load($character->get("account_id"), "premdays");
						
						if($account->getGuildLevel($guild->get("name")) == 2 and $account->getGuildLevel($guild->get("name")) <=  $ranks[$_POST["member_rank"]]["level"])
						{
							$canNotPromove = true;		
						}
						elseif($members[$_POST["guild_member"]]['level'] == 1)
						{
							$canNotDemote = true;						
						}
						elseif($ranks[$_POST["member_rank"]]["level"] == 2 and $accountMember->get("premdays") == 0)
						{
							$rankOnlyToPremium = true;	
						}
						elseif($ranks[$_POST["member_rank"]]["level"] == 2 and $accountMember->isGuildHighMember())
						{
							$alreadyIsHighMember = true;	
						}
						else
						{
							$character->set("rank_id", $_POST["member_rank"]);
							$character->save();										
						}
					}
					elseif($_POST["guild_action"] == "setTitle")
					{
						if(strlen($_POST["member_nick"]) < 3 or strlen($_POST["member_nick"]) > 15)
						{
							$titleIsLong = true;
						}
						else
						{
							$character->loadByName($_POST["guild_member"], "name, guildnick");
							$character->set("guildnick", $_POST["member_nick"]);
							$character->save();
						}
					}
					elseif($_POST["guild_action"] == "exclude")
					{						
						if($members[$_POST["guild_member"]]['level'] == 1)
						{
							$canNotDemote = true;						
						}			
						elseif ($guild->isOnWar())
						{
							$isWar = true;
						}
						else
						{
							$character->loadByName($_POST["guild_member"], "name, rank_id, guildnick, guild_join_date");
							$character->set("guildnick", "");		
							$character->set("rank_id", 0);
							$character->set("guild_join_date", 0);
							$character->save();
						}
					}
				}
			}	
			else
				$memberInexistente = true;
		
			if($account->get("password") != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}	
			elseif($memberInexistente)		
			{
				$error = Lang::Message(LMSG_GUILD_IS_NOT_MEMBER, $_POST["guild_member"], $_GET['name']);						
			}			
			elseif($rankOnlyToPremium)		
			{
				$error = Lang::Message(LMSG_GUILD_RANK_ONLY_PREMIUM);						
			}				
			elseif(!$canEditMember)
			{
				$error = Lang::Message(LMSG_GUILD_PERMISSION);
			}			
			elseif($titleIsLong)
			{
				$error = Lang::Message(LMSG_GUILD_TITLE_SIZE);
			}		
			elseif($canNotPromove)		
			{
				$error = Lang::Message(LMSG_GUILD_PERMISSION);						
			}
			elseif($canNotDemote)		
			{
				$error = Lang::Message(LMSG_GUILD_PERMISSION);						
			}	
			elseif($alreadyIsHighMember)
			{
				$error = Lang::Message(LMSG_GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK);					
			}	
			else
			{						
				$success = Lang::Message(LMSG_GUILD_MEMBER_EDITED, $_POST["guild_member"]);
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
		
		$lowerRank = $guild->getLowerRank();
		
		foreach($members as $player_name => $member_values)
		{			
			$options .= "<option value='{$player_name}'>{$player_name} ({$member_values['rank']})</option>";
		}
		
		foreach($ranks as $rank_id => $rank_values)
		{
			if($rank_values['level'] >= 2)
			{
				$option_ranks .= "<option ".(($lowerRank == $rank_id) ? "selected='selected'" : "")." value='{$rank_id}'>{$rank_values['level']}: {$rank_values['name']}</option>";
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
							<li><input name="guild_action" type="radio" value="setRank"> Configurar rank para <select name="member_rank">'.$option_ranks.'</select></li>
							<li><input name="guild_action" type="radio" value="setTitle"> Configurar titulo para <input name="member_nick" size="25" type="text" value="" /></li>
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