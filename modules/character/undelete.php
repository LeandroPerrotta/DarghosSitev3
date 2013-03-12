<?php
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	$list = $account->getCharacterList();	
	
	$player = new \Framework\Player();
	$player->loadByName($_POST["player_name"]);
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}	
	elseif(!$player->deletionStatus())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_TO_DELETION);
	}
	elseif(!in_array($_POST["player_name"], $list))
	{	
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
	}
	else
	{
		$player->cancelDeletion();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NO_MORE_DELETED, $_POST["player_name"]);
	}
}

if($success)	
{
	\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
}
else
{
	if($error)	
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
	}

$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>

			<p>
				<label for="character_name">Personagem</label><br />
				<input readonly="readonly" name="player_name" size="40" type="text" value="'.$_GET['name'].'" />
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
?>