<?php
$post = Core::extractPost();

if($post)
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$list = $account->getCharacterList();	
	
	$character = new Character();
	$character->loadByName($post[0]);
	
	if($account->get("password") != Strings::encrypt($post[1]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}	
	elseif(!$character->deletionStatus())
	{
		$error = Lang::Message(LMSG_CHARACTER_NOT_TO_DELETION);
	}
	elseif(!in_array($post[0], $list))
	{	
		$error = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
	}
	else
	{
		$character->cancelDeletion();
		
		$success = Lang::Message(LMSG_CHARACTER_NO_MORE_DELETED, $post[0]);
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
				<input readonly="readonly" name="character_name" size="40" type="text" value="'.$_GET['name'].'" />
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