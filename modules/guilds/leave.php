<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
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
		
		if($_POST)
		{									
			foreach($members as $member_name => $member_values)
			{
				$members_list[] = $member_name;
			}
			
			if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}
			//caso o personagem não for da guild?? e se ele for membro normal?? acho que é isso... nao sei kkkkkk
			elseif(!in_array($_POST["member_candidate"], $members_list) or $members[$_POST["member_candidate"]]['level'] == 1)
			{
				$error = Lang::Message(LMSG_REPORT);				
			}
			else
			{						
				$character = new Character();
				$character->loadByName($_POST["member_candidate"]);
				$character->set("rank_id", 0);
				$character->set("guildnick", "");
				$character->set("guild_join_date", 0);
				$character->save();
				
				$success = Lang::Message(LMSG_GUILD_LEAVE, $_POST["member_candidate"], $_GET['name']);
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