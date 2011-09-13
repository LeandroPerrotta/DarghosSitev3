<?
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$checkName = Ajax_account::checkName();
	
	if($account->getPassword() != Strings::encrypt($_POST['account_password']))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}
	elseif($checkName["error"])
	{
		$error = $checkName["text"];
	}	
	elseif($account->getName() == $_POST['account_name'])
	{
		$error = Lang::Message(LMSG_ACCOUNT_SETNAME_SAME_ID);
	}	
	else
	{		
		$account->setName($_POST['account_name']);
		$account->save();
		
		$success = Lang::Message(LMSG_ACCOUNT_SETNAME_SUCCESS);
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

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="account_name">Nome da Conta</label><br />
			<input name="account_name" size="40" type="text" value="" />
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