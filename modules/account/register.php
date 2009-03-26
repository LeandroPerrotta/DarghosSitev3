<?
if($_POST)
{	
	$account = $core->loadClass("Account");
	$number = $account->getNumber();
	$password = $strings->randKey(8, 1, "lower+number");
	
	//argumentos para e-mail
	$_arg = array();
	$_arg[] = $number;
	$_arg[] = $password;
	
	if(!$_POST['account_email'])
	{
		$error = "Você deve preencher todos os formularios corretamente.";
	}
	elseif(!$_POST['account_privacypolicy'])
	{
		$error = "Para jogar no ".CONFIG_SITENAME." é necessario aceitar a nossa politica de privacidade.";
	}
	elseif($account->loadByEmail($_POST['account_email']))
	{
		$error = "Este e-mail já esta em uso por outra conta em nosso banco de dados.";
	}		
	elseif(!$strings->validEmail($_POST['account_email']))
	{
		$error = "Este não é um e-mail valido.";
	}	
	elseif(!$number)
	{
		$error = "Caro jogador, não existe um numero de conta disponivel. Tente novamente mais tarde.";
	}
	elseif(!$core->mail(EMAIL_REGISTER, $_POST['account_email'], $_arg))
	{
		$error = "Não foi possivel enviar o e-mail de validação de conta. Tente novamente mais tarde.";
	}
	else
	{
		$account->set("email", $_POST['account_email']);
		$account->set("password", $strings->encrypt($password));
		//$account->set("name", $number);
		$account->set("creation", time());
		
		$account->save();
	
		$success = "
		<p>Parabens, sua conta foi criada com sucesso!</p>
		<p>O numero de sua conta é: ".$number.".</p>
		<p>Sua senha e outras informações foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
		<p>Tenha um bom jogo!</p>
		";
	}
}

if($success)	
{
	$core->sendMessageBox("Sucesso!", $success);
}
else
{
	if($error)	
	{
		$core->sendMessageBox("Erro!", $error);
	}


$module .=	'
	<form action="" method="post">
		<fieldset>
			
			<p>
				<label for="account_email">Endereço de e-mail</label><br />
				<input id="account_email" name="account_email" size="40" type="text" value="" />
			</p>
				
			<p>
				<input name="account_privacypolicy" id="account_privacypolicy" type="checkbox" value="1" /> Eu concordo com esta política de privacidade.
			</p>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';

}
?>	
