<?
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	$checkName = \Framework\Account::checkName();
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST['account_password']))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}
	elseif($checkName["error"])
	{
		$error = $checkName["text"];
	}	
	elseif($account->getName() == $_POST['account_name'])
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_SETNAME_SAME_ID);
	}	
	else
	{		
		$account->setName($_POST['account_name']);
		$account->save();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_SETNAME_SUCCESS);
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