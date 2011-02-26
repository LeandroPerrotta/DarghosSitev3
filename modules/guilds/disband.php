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
		
		if($guild->MembersCount() > 1 || $guild->InvitesCount() != 0)
		{
			$message = Lang::Message(LMSG_GUILD_NEED_NO_MEMBERS_DISBAND);			
			return false;
		}
					
		$guild->Delete();
		
		$message = Lang::Message(LMSG_GUILD_DISBANDED, $_GET['name']);	
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
			
		$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						Para desmanchar sua guild é  necessario não possuir nenhum membro em atividade, expulsando todos membros da guilda, exepto o proprio líder.			
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