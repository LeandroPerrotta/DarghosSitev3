<?
if($_POST)
{
	$account = new \Framework\Account();
	
	if(($account->loadByName($_POST["login_name"])) and ($account->getPassword() == \Core\Strings::encrypt($_POST["login_password"])))
	{
		$_SESSION['login'][] = $account->getId();
		$_SESSION['login'][] = \Core\Strings::encrypt($_POST["login_password"]);
		
		if(!$_SESSION["login_redirect"])
			\Core\Main::redirect("?ref=account.main");	
		else
		{
			$url = trim($_SESSION["login_redirect"], "/");
			unset($_SESSION["login_redirect"]);
			\Core\Main::redirect($url);
		}
	}
	else
	{
		$error = tr("O nome de conta ou senha informados estão incorretos.");
	}
}

if($error)	
{
	\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
}

$require_login_str = "";

if($_SESSION["login_redirect"] != "")
{
	$require_login_str = "
	<p>
		".tr("A pagina que você está tentando acessar requer que você esteja logado em sua conta.")."
	</p>
	";
}

global $pages, $buttons;
$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		'.$require_login_str.'
	
		<p>
			<label for="account_name">'.tr("Login").'</label><br />
			<input name="login_name" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_password">'.tr("Senha").'</label><br />
			<input name="login_password" size="40" type="password" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.tr("Enviar").'" />
		</p>
	</fieldset>
</form>';
?>