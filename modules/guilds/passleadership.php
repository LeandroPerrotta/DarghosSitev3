<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = new Guilds();
	
	if(!$guild->loadByName($_GET['name']))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
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
			$ranks = $guild->getRanks();
			
			foreach($members as $member_name => $member_values)
			{
				$members_list[] = $member_name;
			}
			
			if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}		
			elseif(!in_array($_POST["member_candidate"], $members_list))
			{
				$error = Lang::Message(LMSG_GUILD_IS_NOT_MEMBER, $_POST["member_candidate"], $_GET['name']);			
			}
			else
			{			
				$leader_id = 0;
				$vice_id = 0;
				
				foreach($ranks as $rank_id => $rank_values)
				{
					if($rank_values['level'] == 1)
						$leader_id = $rank_id;
						
					if($rank_values['level'] == 2)
						$vice_id = $rank_id;
				}
				
				$newLeader_char = new Character();
				$newLeader_char->loadByName($_POST["member_candidate"], "name, rank_id");
				$newLeader_char->set("rank_id", $leader_id);
				$newLeader_id = $newLeader_char->get("id");
				$newLeader_char->save();
				
				$oldLeader_char = new Character();
				$oldLeader_char->load($guild->get("ownerid"), "name, rank_id");
				$oldLeader_char->set("rank_id", $vice_id);
				$oldLeader_name = $oldLeader_char->get("name");
				$oldLeader_char->save();
				
				$guild->set("ownerid", $newLeader_id);
				$guild->save();
				
				$success = Lang::Message(LMSG_GUILD_PASSLEADERSHIP, $_GET['name'], $oldLeader_name, $_POST["member_candidate"]);
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
			
			$vices = 0;
			
			foreach($members as $member_name => $member_values)
			{
				if($member_values['level'] == 2)
				{
					$options .= "<option value='{$member_name}'>{$member_name}</option>";
					$vices++;
				}
			}
			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

			';

				if($vices > 0)
				{
					$module .=	'
					<p>
						<label for="member_candidate">Membros Cadidatos</label><br />		
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
					';
				}
				else
				{
					$module .=	'
					<p>
						É necessario possuir ao menos 1 vice lider disponivel para que seja possivel a  transferencia a liderança de uma guilda.
					</p>	
					';			
				}
				
				$module .=	'
				</fieldset>
			</form>';	
		}	
	}

}		
?>