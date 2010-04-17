<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$character_list = $account->getCharacterList(true);	
	
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
		if($_POST)
		{
			$invites_list = explode(";", $_POST["guild_invites"]);
			$invites_limit = true;
			
			$dontExists = array();
			$wasGuild = array();
			$wasInvited = array();
			
			if(count($invites_list) < 20)
			{
				$invites_limit = false;
				
				foreach($invites_list as $player_name)
				{
					$character = new Character();
					
					if(!$character->loadByName($player_name))
					{
						$dontExists[] = $player_name;	
					}	
					else
					{	
						if($character->loadGuild())
							$wasGuild[] = $player_name;
							
						if($character->wasInvitedToGuild())
							$wasInvited[] = $player_name;	
					}	
				}
			}
			
			if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}	
			elseif($invites_limit)
			{				
				$error = Lang::Message(LMSG_GUILD_INVITE_LIMIT);
			}					
			elseif(count($wasGuild) != 0)
			{
				foreach($wasGuild as $name)
				{
					$wasGuild_list .= $name."<br>";
				}
				
				$error = Lang::Message(LMSG_GUILD_INVITE_ALREADY_MEMBER, $wasGuild_list);
			}
			elseif(count($wasInvited) != 0)
			{
				foreach($wasInvited as $name)
				{
					$wasInvited_list .= $name."<br>";
				}
				
				$error = Lang::Message(LMSG_GUILD_INVITE_ALREADY_INVITED, $wasInvited_list);
			}		
			elseif(count($dontExists) != 0)
			{
				foreach($dontExists as $name)
				{
					$dontExists_list .= $name."<br>";
				}
				
				$error = Lang::Message(LMSG_GUILD_INVITE_CHARACTER_NOT_FOUNDS, $dontExists_list);
			}					
			else
			{		
				foreach($invites_list as $player_name)
				{
					$character = new Character();
					
					$character->loadByName($player_name);
					$character->inviteToGuild($guild->get("id"));
				}				
				
				$success = Lang::Message(LMSG_GUILD_INVITEDS);
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
						<label for="guild_invites">Personagem(s)</label><br />
						<textarea name="guild_invites" rows="10" wrap="physical" cols="55"></textarea>
						<em><br><b>Instruções:</b> Lista de personagens a serem convidados a sua guild, ultilize um ; (ponto e virgula) para separar cada personagem. (ex: Slash;Fawkes;Baracs)</em>
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