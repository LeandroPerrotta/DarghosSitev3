<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);
	
	$charsList = $account->getCharacterList(true);
	$charsLevel100 = 0;
	
	$premtest = $account->checkPremiumTest();
	
	if(!$premtest)
	{
		if(is_array($charsList))
		{
			foreach($charsList as $charId)
			{
				$character = $core->loadClass("Character");
				$character->load($charId);
				
				if($character->getLevel() >= 100)
				{
					$charsLevel100++;
				}
			}
		}
		
		$contribute = $core->loadClass("Contribute");
		$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);
		$havePremiums = 0;
		
		if(is_array($oders))
		{	
			foreach($oders as $orderId)
			{
				$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");
				
				if($contribute->get("status") == 1 OR $contribute->get("status") == 2)
				{
					$havePremiums++;
				}
			}
		}
	}
	
	if($account->getPassword() != $strings->encrypt($_POST['account_password']))
	{
		$error = "Confirmação da senha falhou.";
	}
	elseif($premtest)
	{
		$error = "A sua conta já recebeu este beneficio no dia {$core->formatDate($premtest)}. Somente é permitido receber este beneficio uma vez por conta.";
	}	
	elseif($havePremiums != 0)
	{
		$error = "Você já obteve uma Conta Premium da forma tradicional. Este recurso só está disponivel a jogadores que jamais possuiram uma Conta Premium.";
	}
	elseif($charsLevel100 == 0)
	{
		$error = "Para receber este recurso é necessario que você tenha atingido o level 100 em algum dos personagens de sua conta.";
	}			
	else
	{		
		$account->activePremiumTest();
		
		$success = "
		<p>Caro jogador,</p>
		<p>A sua conta recebeu o beneficio de Premium Test com sucesso! Agora você possui ".PREMTEST_DAYS." gratuitos para conhecer o Darghos a vontade!</p>
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

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>Este é um recurso especial que permite que você, que tenha um personagem level 100 ou superior e nunca tenha obtido uma Conta Premium receba '.PREMTEST_DAYS.' dias premium totalmente gratuitos para que você possa conhecer o Darghos. Confirme sua senha abaixo para receber o beneficio.</p>	
		
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