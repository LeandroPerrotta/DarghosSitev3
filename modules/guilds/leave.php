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
	elseif(!$account->getGuildLevel($guild->get("name")))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));		
	}	
	else
	{		
		$guild->loadRanks();
		$guild->loadMembersList();
		
		$members = $guild->getMembersList();		
		
		$post = Core::extractPost();
		if($post)
		{									
			foreach($members as $member_name => $member_values)
			{
				$members_list[] = $member_name;
			}
			
			if($account->get("password") != Strings::encrypt($post[1]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}
			//caso o personagem não for da guild?? e se ele for membro normal?? acho que é isso... nao sei kkkkkk
			elseif(!in_array($post[0], $members_list) or $members[$post[0]]['level'] == 1)
			{
				$error = Lang::Message(LMSG_REPORT);				
			}
			else
			{						
				$character = new Character();
				$character->loadByName($post[0], "name, rank_id, guildnick, guild_join_date");
				$character->set("rank_id", 0);
				$character->set("guildnick", "");
				$character->set("guild_join_date", 0);
				$character->save();
				
				$success = Lang::Message(LMSG_GUILD_LEAVE, $post[0], $_GET['name']);
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
			
			foreach($members as $member_name => $member_values)
			{
				if(in_array($member_name, $character_list) and $member_values['level'] > 1)
				{
					$options .= "<option value='{$member_name}'>{$member_name}</option>";
				}
			}
			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						<label for="member_candidate">Selecione o Personagem</label><br />		
						<select name="member_candidate">'.$options.'</select>
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