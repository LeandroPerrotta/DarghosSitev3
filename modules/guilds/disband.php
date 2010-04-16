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
		$post = Core::extractPost();
		if($post)
		{			
			$guild->loadRanks();
			$guild->loadMembersList();
			
			$members = $guild->getMembersList();
			
			if($account->get("password") != Strings::encrypt($post[0]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}	
			elseif(count($members) > 1)
			{
				$error = Lang::Message(LMSG_GUILD_NEED_NO_MEMBERS_DISBAND);			
			}
			else
			{				
				$guild->disband();
				
				$success = Lang::Message(LMSG_GUILD_DISBANDED, $_GET['name']);
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