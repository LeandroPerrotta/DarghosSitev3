<?
$account = new \Framework\Account();
$account->load($_SESSION['login'][0]);

if($_POST)
{
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}			
	elseif(strlen($_POST["account_realname"]) > 25 or strlen($_POST["account_location"]) > 25 or strlen($_POST["account_url"]) > 50)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEINFOS_WRONG_SIZE);
	}
	else
	{		
		$account->setRealName($_POST["account_realname"]);
		$account->setLocation($_POST["account_location"]);
		$account->setUrl($_POST["account_url"]);
		
		$account->save();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEINFOS_SUCCESS);
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
			<label for="account_realname">Nome Real</label><br />
			<input name="account_realname" size="40" type="text" value="'.$account->get("real_name").'" />
			<em>Limpe para deletar.</em>
		</p>
		
		<p>
			<label for="account_location">Localidade</label><br />
			<input name="account_location" size="40" type="text" value="'.$account->get("location").'" />
			<em>Limpe para deletar.</em>
		</p>

		<p>
			<label for="account_url">Website</label><br />
			<input name="account_url" size="40" type="text" value="'.$account->get("url").'" />
			<em>Limpe para deletar.</em>
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