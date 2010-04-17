<?php
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$list = $account->getCharacterList();	
	
	$character = new Character();
	$character->loadByName($_POST["player_name"]);
	
	if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}	
	elseif(!$character->deletionStatus())
	{
		$error = Lang::Message(LMSG_CHARACTER_NOT_TO_DELETION);
	}
	elseif(!in_array($_POST["player_name"], $list))
	{	
		$error = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
	}
	else
	{
		$character->cancelDeletion();
		
		$success = Lang::Message(LMSG_CHARACTER_NO_MORE_DELETED, $_POST["player_name"]);
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