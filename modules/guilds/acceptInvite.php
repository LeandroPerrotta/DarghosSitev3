<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	$character_listByName = $account->getCharacterList();	
	
	$guild = new Guilds();
		
	if(!$guild->loadByName($_GET['name']))
	{
		$guildLoad = false;
	}
	else 
	{
		$guildLoad = true;
		$guild->loadInvitesList();
		$invites = $guild->getInvites();	
		
		$validInvites = false;
		$guild_invites = array();
		
		foreach($invites as $player_name => $invite_date)
		{
			if(in_array($player_name, $character_listByName))
			{
				$options .= "<option value='{$player_name}'>{$player_name}</option>";
				$validInvites = true;
			}	
			
			$guild_invites[] = $player_name;
		}			
	}
	
	if(!$guildLoad)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND));	
	}
	elseif(!$validInvites)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));		
	}	
	else
	{		
		$post = Core::extractPost();
		if($post)
		{			
			if($account->get("password") != Strings::encrypt($post[2]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}	
			elseif(!in_array($post[0], $character_listByName))
			{				
				$error = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			}	
			elseif(!in_array($post[0], $guild_invites))
			{				
				$error = Lang::Message(LMSG_GUILD_CHARACTER_NOT_INVITED);
			}											
			else
			{		
				$character = new Character();
				$character->loadByName($post[0], "name, rank_id, guild_join_date");
				
				if($post[1] == "accept")		
				{			
					$character->acceptInvite();
					$character->save();
					
					$success = Lang::Message(LMSG_GUILD_JOIN, $post[0], $guild->get("name"));
				}
				elseif($post[1] == "reject")
				{
					$character->removeInvite();
					
					$success = Lang::Message(LMSG_GUILD_JOIN_REJECT, $guild->get("name"), $post[0]);			
				}
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
						<label for="character_name">Personagem Convidado</label><br />
						<select name="character_name">'.$options.'</select>
					</p>	

					<p>
						<label for="invite_action">Ação</label><br />
						<ul id="pagelist">
							<li><input checked="checked" name="invite_action" type="radio" value="accept"> Aceitar Convite </li>
							<li><input name="invite_action" type="radio" value="reject"> Rejeitar Convite </li>
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