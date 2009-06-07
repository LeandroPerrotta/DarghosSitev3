<?php
$post = $core->extractPost();

if($_GET['key'])
{
	$account = $core->loadClass("Account");
	
	if(!$account->checkChangePasswordKey($_GET['key']))
	{
		$error = "Chave de recuperação inexistente.";
	}
	else
	{
		$password = $strings->randKey(8, 1, "lower+number");

		//argumentos para e-mail
		$_arg = array();
		$_arg[] = $password;		
		
		if(!$core->mail(EMAIL_RECOVERY_PASSWORD, $account->getEmail(), $_arg))
		{
			$error = "Não foi possivel enviar o e-mail de validação de conta. Tente novamente mais tarde.";
		}		
		else	
		{		
			$account->setPassword($strings->encrypt($password));
			$account->save();
			
			$success = "
			<p>Caro jogador, a nova senha de sua conta foi enviada ao seu e-mail com sucesso!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
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
		$core->sendMessageBox("Erro!", $error);
	}	
}
else
{
	if($post)
	{	
		$account = $core->loadClass("Account");
		$character = $core->loadClass("Character");
		
		$loadEmail = $account->loadByEmail($_POST['recovery_email']);
		$characterList = $account->getCharacterList();
		
		if(!is_array($characterList))
		{
			$characterList[] = "";
		}
		
		if($_POST['recovery_information'] != 4 and (!$_POST['recovery_name'] or !$_POST['recovery_email']))
		{
			$error = "Preencha todos campos do formulario corretamente.";
		}	
		elseif($_POST['recovery_information'] != 4 and !$loadEmail)
		{
			$error = "Não existe nenhuma conta registrada neste e-mail em nosso banco de dados.";
		}
		elseif($_POST['recovery_information'] != 4 and !in_array($_POST['recovery_name'], $characterList))
		{
			//echo print_r($characterList);
			$error = "O personagem não pertence a conta do e-mail informado.";
		}
		else
		{		
			/* RECUPERAÇÃO DO NUMERO DA CONTA */
			if($_POST['recovery_information'] == 1)
			{
				//argumentos para e-mail
				$_arg = array();
				$_arg[] = $account->getName();
				
				if(!$core->mail(EMAIL_RECOVERY_ACCOUNT, $account->getEmail(), $_arg))
				{
					$error = "Não foi possivel enviar o e-mail de validação de conta. Tente novamente mais tarde.";
				}		
				else	
				{				
					$success = "
					<p>Caro jogador, o número de sua conta foi enviado ao seu e-mail com sucesso!</p>
					<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
					<p>Tenha um bom jogo!</p>
					";				
				}
			}
			/* RECUPERAÇÃO DA SENHA DA CONTA */
			elseif($_POST['recovery_information'] == 2)
			{
				$key = $strings->randKey(8, 1, "number");
				
				//argumentos para e-mail
				$_arg = array();
				$_arg[] = $key;			
				
				if(!$core->mail(EMAIL_RECOVERY_PASSWORDKEY, $account->getEmail(), $_arg))
				{
					$error = "Não foi possivel enviar o e-mail de validação de conta. Tente novamente mais tarde.";
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = "
					<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com as informações necessarias para você gerar uma nova senha para sua conta!</p>
					<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
					<p>Tenha um bom jogo!</p>
					";				
				}			
			}
			/* RECUPERAÇÃO DO NUMERO E SENHA DA CONTA */
			elseif($_POST['recovery_information'] == 3)
			{
				$key = $strings->randKey(8, 1, "number");
				
				//argumentos para e-mail
				$_arg = array();
				$_arg[] = $account->getName();
				$_arg[] = $key;			
				
				if(!$core->mail(EMAIL_RECOVERY_BOTH, $account->getEmail(), $_arg))
				{
					$error = "Não foi possivel enviar o e-mail de validação de conta. Tente novamente mais tarde.";
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = "
					<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com o número de sua conta e as informações necessarias para você gerar uma nova senha para sua conta!</p>
					<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
					<p>Tenha um bom jogo!</p>
					";				
				}			
			}	
			elseif($_POST['recovery_information'] == 4)
			{
				if(!$_POST['recovery_name'])
				{
					$error = "Para efetuar esta operação é necessario informar ao menos o nome de um personagem da conta que deseja recuperar.";
				}					
				elseif(!$character->loadByName($_POST['recovery_name']))
				{
					$error = "Este personagem não existe em nosso banco de dados.";
				}
				else
				{
					$_SESSION['recovery'][] = $_POST['recovery_name'];
					$core->redirect("index.php?ref=account.advanced_recovery");	
				}		
			}
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
		
				<p>
					<label for="recovery_name">Nome do Personagem</label><br />
					<input name="recovery_name" size="40" type="text" value="" /> <br><em>(preencha com o nome de um personagem da conta em que você quer recuperar)</em>
				</p>		
				
				<p>
					<label for="recovery_email">E-mail da Conta</label><br />
					<input name="recovery_email" size="40" type="text" value="" /> <br><em>(preencha com o e-mail registrado na conta de seu personagem)</em>
				</p>				
				
				<p>
					<label for="order_days">Dado a se Recuperar</label><br />
					
					<ul id="pagelist">
						<li><input name="recovery_information" type="radio" value="1"> Eu quero receber o nome de minha conta em meu e-mail. </li>
						<li><input name="recovery_information" type="radio" value="2"> Eu quero receber uma nova senha para minha conta em meu e-mail. </li>
						<li><input name="recovery_information" type="radio" value="3"> Eu quero receber o nome e uma nova senha para minha conta em meu e-mail. </li>
					</ul>	
				</p>
				
				<div id="line1"></div>
				
				<p>
					<label for="order_days">Recuperação Avançada com minha Chave Secreta</label><br />
					
					<ul id="pagelist">
						<li><input name="recovery_information" type="radio" value="4"> Eu não consegui recuperar minha conta com as informações acima e desejo recupera-la usando a minha chave secreta. </li>
					</ul>	
				</p>
				
				<div id="line1"></div>				
				
				<p>
					<input class="button" type="submit" value="Proximo" />					
				</p>
		</fieldset>
	</form>';
	}
}
?>