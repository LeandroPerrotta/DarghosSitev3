<p id="urlnavigation"><? echo $patch['urlnavigation']; ?></p>
<div id="line1"></div>

<?
if($_POST)
{
	include "libs/phpmailer/class.phpmailer.php";
	
	$account = $core->loadClass("Account");
	$number = $account->getNumber();

	if(!$_POST['account_email'])
	{
		$error = "Você deve preencher todos os formularios corretamente.";
	}
	elseif(!$_POST['account_privacypolicy'])
	{
		$error = "Para jogar no ".CONFIG_SITENAME." é necessario aceitar a nossa politica de privacidade.";
	}
	elseif(!$strings->validEmail($_POST['account_email']))
	{
		$error = "Este não é um e-mail valido.";
	}	
	elseif(!$number)
	{
		$error = "Caro jogador, não existe um numero de conta disponivel. Tente novamente mais tarde.";
	}
	else
	{
		$success = "
		<p>Parabens, sua conta foi criada com sucesso!</p>
		<p>O numero de sua conta é: 111111.</p>
		<p>Sua senha e outras informações foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
		<p>Tenha um bom jogo!</p>
		";
	}
}
?>

	<?
if($success)	
{
	echo '
		
	<div id="sucesso">
		<h2>'.$success.'</h2>
	</div>
	
	';
}
else
{
	if($error)	
	{
		echo '
		
		<div id="error">
			<h2>'.$error.'</h2>
		</div>
		
		';
	}
	?>

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
				<input type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>
<?
}
?>	
