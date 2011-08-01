<?php
if($_GET['name'] && !ENABLE_GUILD_READ_ONLY)
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

		$member = $guild->SearchMemberByName($_POST["member_candidate"]);
		
		if(!$member)
		{
			$message = Lang::Message(LMSG_GUILD_IS_NOT_MEMBER, $_POST["guild_member"], $_GET['name']);
			return false;			
		}
		
		$member->LoadGuild();
		
		if($member->GetGuildLevel() != GUILD_RANK_VICE)
		{
			$message = Lang::Message(LMSG_GUILD_PERMISSION);
			return false;
		}
		
		$leader_rank = $guild->SearchRankByLevel(GUILD_RANK_LEADER);
		$vice_rank = $guild->SearchRankByLevel(GUILD_RANK_VICE);
		
		$member->setGuildRankId($leader_rank->GetId());
		$member->save();
		
		$guild->SetOwnerId($member->getId());
		$guild->Save();
		
		$old_owner = new Character();
		$old_owner->load($guild->GetOwnerId());
		$old_owner->LoadGuild();
		$old_owner->setGuildRankId($vice_rank->GetId());
		$old_owner->save();
		
		$message = Lang::Message(LMSG_GUILD_PASSLEADERSHIP, $_GET['name'], $old_owner->getName(), $_POST["member_candidate"]);
		return true;
	}
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$guild = new Guilds();
	
	if(!$guild->LoadByName($_GET['name']))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif(Guilds::GetAccountLevel($account, $guild->GetId()) != GUILD_RANK_LEADER)
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
						
			$vices = $guild->SearchRankByLevel(GUILD_RANK_VICE);
			
			foreach($vices->Members as $member)
			{
				$options .= "<option value='{$member->getName()}'>{$member->getName()}</option>";
			}			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

			';

			if(count($vices->Members) != 0)
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