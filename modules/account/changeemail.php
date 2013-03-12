<?
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->get("password") != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}
	elseif($account->loadByEmail($_POST["account_newemail"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_EMAIL_ALREADY_USED);
	}			
	elseif(!\Core\Strings::validEmail($_POST["account_newemail"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_EMAIL);
	}
	elseif(is_array($newemail = $account->getEmailToChange()))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEEMAIL_ALREADY_HAVE_REQUEST);
	}
	else
	{		
		$account->addEmailToChange($_POST["account_newemail"]);
		$newemail = $account->getEmailToChange();
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEEMAIL_SCHEDULED);
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

global $pages, $buttons;

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="account_newemail">'.$pages["ACCOUNT.CHANGE_EMAIL.NEW_EMAIL"].'</label><br />
			<input name="account_newemail" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="account_password">'.$pages["ACCOUNT.CHANGE_EMAIL.PASSWORD"].'</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>