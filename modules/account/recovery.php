<?php
$post = $core->extractPost();

if($_GET['key'])
{
	$account = $core->loadClass("Account");
	
	if(!$account->checkChangePasswordKey($_GET['key']))
	{
		$error = $boxMessage['CHANGE_PASSWORD_KEY_NOT_FOUND'];
	}
	else
	{
		$password = $strings->randKey(8, 1, "lower+number");

		//argumentos para e-mail
		$_arg = array();
		$_arg[] = $password;		
		
		if(!$core->mail(EMAIL_RECOVERY_PASSWORD, $account->getEmail(), $_arg))
		{
			$error = $boxMessage['FAIL_SEND_EMAIL'];
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
			$error = $boxMessage['INCOMPLETE_FORM'];
		}	
		elseif($_POST['recovery_information'] != 4 and !$loadEmail)
		{
			$error = $boxMessage['NONE_ACCOUNT_FOR_THIS_EMAIL'];
		}
		elseif($_POST['recovery_information'] != 4 and !in_array($_POST['recovery_name'], $characterList))
		{
			//echo print_r($characterList);
			$error = $boxMessage['CHARACTER_NOT_FROM_EMAIL'];
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
					$error = $boxMessage['FAIL_SEND_EMAIL'];
				}		
				else	
				{				
					$success = $boxMessage['SUCCESS.ACCOUNT_NAME_SENDED'];		
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
					$error = $boxMessage['FAIL_SEND_EMAIL'];
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = $boxMessage['SUCCESS.PASSWORD_SENDED'];			
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
					$error = $boxMessage['FAIL_SEND_EMAIL'];
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = $boxMessage['SUCCESS.BOTH_SENDED'];		
				}			
			}	
			elseif($_POST['recovery_information'] == 4)
			{
				if(!$_POST['recovery_name'])
				{
					$error = $boxMessage['CHARACTER_NAME_NEEDED'];
				}					
				elseif(!$character->loadByName($_POST['recovery_name']))
				{
					$error = $boxMessage['CHARACTER_NOT_FOUND'];
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
					<label for="recovery_name">'.$pages["ACCOUNT.RECOVERY.CHARACTER_NAME"].'</label><br />
					<input name="recovery_name" size="40" type="text" value="" /> <br><em>'.$pages["ACCOUNT.RECOVERY.CHARACTER_NAME_INFO"].'</em>
				</p>		
				
				<p>
					<label for="recovery_email">'.$pages["ACCOUNT.RECOVERY.EMAIL"].'</label><br />
					<input name="recovery_email" size="40" type="text" value="" /> <br><em>'.$pages["ACCOUNT.RECOVERY.EMAIL_INFO"].'</em>
				</p>				
				
				<p>
					<label for="order_days">'.$pages["ACCOUNT.RECOVERY.DATA_TO_RECOVERY"].'</label><br />
					
					<ul id="pagelist">
						<li><input name="recovery_information" type="radio" value="1"> '.$pages["ACCOUNT.RECOVERY.RECOVERY_ACCOUNT_NAME"].'</li>
						<li><input name="recovery_information" type="radio" value="2"> '.$pages["ACCOUNT.RECOVERY.RECOVERY_PASSWORD"].'</li>
						<li><input name="recovery_information" type="radio" value="3"> '.$pages["ACCOUNT.RECOVERY.RECOVERY_BOTH"].'</li>
					</ul>	
				</p>
				
				<div id="line1"></div>
				
				<p>
					<label for="order_days">'.$pages["ACCOUNT.RECOVERY.ADVANCED_RECOVERY"].'</label><br />
					
					<ul id="pagelist">
						<li><input name="recovery_information" type="radio" value="4"> '.$pages["ACCOUNT.RECOVERY.USE_ADVANCED_RECOVERY"].'</li>
					</ul>	
				</p>
				
				<div id="line1"></div>				
				
				<p>
					<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />					
				</p>
		</fieldset>
	</form>';
	}
}
?>