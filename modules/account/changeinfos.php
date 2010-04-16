<?
$account = new Account();
$account->load($_SESSION['login'][0], "password, real_name, location, url");

$post = Core::extractPost();
if($post)
{
	if($account->get("password") != Strings::encrypt($post[3]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}			
	elseif(strlen($post[0]) > 25 or strlen($post[1]) > 25 or strlen($post[2]) > 50)
	{
		$error = Lang::Message(LMSG_CHANGEINFOS_WRONG_SIZE);
	}
	else
	{		
		$account->set("real_name", $post[0]);
		$account->set("location", $post[1]);
		$account->set("url", $post[2]);
		
		$account->save();
		
		$success = Lang::Message(LMSG_CHANGEINFOS_SUCCESS);
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