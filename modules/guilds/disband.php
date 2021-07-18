<?php
use \Core\Configs;
use \Framework\Guilds;
if($_GET['name'] && Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
{
	$result = false;
	$message = "";		
	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);	
	
	function proccessPost(&$message, \Framework\Account $account, Guilds $guild)
	{		
		if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}	
		
		if($guild->MembersCount() > 1 || $guild->InvitesCount() != 0)
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NEED_NO_MEMBERS_DISBAND);			
			return false;
		}
					
		Guilds::LogMessage("Guild {$guild->GetName()} ({$guild->GetId()}) disbanded account id {$account->getId()}.");
		$guild->Delete();
		
		$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_DISBANDED, $_GET['name']);	
		return true;
	}
	
	$guild = new Guilds();

	if(!$guild->LoadByName($_GET['name']))
	{	
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']));		
	}
	elseif(!\Framework\Guilds::IsAccountGuildOwner($account, $guild))
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT));	
	}	
	else
	{		
		if($_POST)
		{
			$result = (proccessPost($message, $account, $guild)) ? true : false;		
		}
			
		if($result)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $message);
		}
		else
		{
			if($_POST)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $message);
			}
			
		$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						Para desmanchar sua guild é necessario não possuir nenhum membro em atividade, expulsando todos membros da guilda, exepto o proprio dono.			
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