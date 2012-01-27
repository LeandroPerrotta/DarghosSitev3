<?
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	/*
	 * Hack
	 */
	include_once("Ajax/account.php");
	$checkPassword = Ajax_account::checkPassword();
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password_current"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}
	elseif($checkPassword["error"])
	{
		$error = $checkPassword["text"];
	}
	elseif(\Core\Strings::encrypt($_POST["account_password"]) == $account->getPassword())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEPASS_SAME_PASSWORD);
	}
	else
	{
		$account->setPassword(\Core\Strings::encrypt($_POST["account_password"]));
		$account->save();
		
		$_SESSION["login"] = array();
		
		$_SESSION["login"][] = $account->getId();
		$_SESSION["login"][] = $account->getPassword();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_PASSWORD_CHANGED);
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
			<label for="account_password">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD"].'</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_confirm_password">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD_CONFIRM"].'</label><br />
			<input name="account_confirm_password" size="40" type="password" value="" />
		</p>	

		<p>
			<label for="account_password_current">'.$pages["ACCOUNT.CHANGE_PASSWORD.CURRENT_PASSWORD"].'</label><br />
			<input name="account_password_current" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>