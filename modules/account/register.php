<?
if($_POST)
{	
	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$password = $strings->randKey(8, 1, "lower+number");
	
	//argumentos para e-mail
	$_arg = array();
	$_arg[] = stripslashes($_POST['account_name']);
	$_arg[] = $password;
	
	if(!$_POST['account_email'] or !$_POST['account_name'])
	{
		$error = "Voc� deve preencher todos os formularios corretamente.";
	}
	elseif(!$_POST['account_privacypolicy'])
	{
		$error = "Para jogar no ".CONFIG_SITENAME." � necessario aceitar a nossa politica de privacidade.";
	}
	elseif(strlen($_POST['account_name']) < 5 or strlen($_POST['account_name']) > 25)
	{
		$error = "O nome de sua conta deve possuir entre 5 e 25 caracteres.";
	}
	elseif($account->loadByEmail($_POST['account_email']))
	{
		$error = "Este e-mail j� esta em uso por outra conta em nosso banco de dados.";
	}		
	elseif($account->loadByName($_POST['account_name']))
	{
		$error = "Este nome j� esta em uso por outra conta em nosso banco de dados.";
	}			
	elseif(!$strings->validEmail($_POST['account_email']))
	{
		$error = "Este n�o � um e-mail valido.";
	}	
	elseif(!$core->mail(EMAIL_REGISTER, $_POST['account_email'], $_arg))
	{
		$error = "N�o foi possivel enviar o e-mail de valida��o de conta. Tente novamente mais tarde.";
	}
	else
	{
		$account->setEmail($_POST['account_email']);
		$account->setPassword($strings->encrypt($password));
		$account->setName($_POST['account_name']);
		$account->setCreation(time());
		
		$account->save();
	
		$success = "
		<p>Parabens, sua conta foi criada com sucesso!</p>
		<p>Sua senha e outras informa��es foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
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
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>
				<label for="account_email">Endere�o de e-mail</label><br />
				<input id="account_email" name="account_email" size="40" type="text" value="" />
			</p>
			
			<p>
				<label for="account_email">Nome da Conta</label><br />
				<input id="account_email" name="account_name" size="40" type="text" value="" />
			</p>			
				
			<p>
				<input name="account_privacypolicy" id="account_privacypolicy" type="checkbox" value="1" /> Eu concordo com esta pol�tica de privacidade.
			</p>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';

}
?>	
