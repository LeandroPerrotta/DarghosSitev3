<?php
if($_SESSION['recovery'])
{
	$post = $core->extractPost();
	
	$character = $core->loadClass("Character");
	$character->loadByName($_SESSION['recovery'][0], "account_id"); 
	
	$chkEmail = $core->loadClass("Account");
	
	$account = $core->loadClass("Account");
	$account->load($character->get("account_id"), "password, email");
	$secretkey = $account->getSecretKey();
	
	if($secretkey)
	{
		if($post)
		{	
			$postSecretKey = $_POST['recovery_secretkey'];
			$postEmail = $_POST['recovery_email'];
		
			if($core->getIpTries() >= 3)
			{
				$error = $boxMessage['OPERATION_BLOCKED_STATE'] ;
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				$core->increaseIpTries();
				
				if($core->getIpTries() < 3)
					$error = $boxMessage['INCORRECT_SECRET_KEY'];
				else
					$error = $boxMessage['MANY_ATTEMPS_OPERATION_BLOCKED'];
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = $boxMessage['EMAIL_ALREADY_IN_USE'];
			}
			elseif(!$strings->validEmail($postEmail))
			{
				$error = $boxMessage['INVALID_EMAIL'];
			}
			else
			{
				$account->setEmail($postEmail);
				$account->save();
				
				$success = $boxMessage['SUCCESS.CHANGE_EMAIL_USING_RECOVERY_KEY'];	
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
						<label for="recovery_name">'.$pages["ACCOUNT.ADVANCED_RECOVERY.CHARACTER_NAME"].'</label><br />
						<input readonly="readonly" name="recovery_name" size="40" type="text" value="'.$_SESSION['recovery'][0].'" />
					</p>		
					
					';
			
					if($secretkey["lembrete"] != "default")
					{
						$module .= '
						<p>
							<label for="recovery_character">'.$pages["ACCOUNT.ADVANCED_RECOVERY.REMINDER"].'</label><br />
							<input readonly="readonly" name="recovery_lembrete" size="40" type="text" value="'.$secretkey["lembrete"].'" /> <br><em>('.$pages["ACCOUNT.ADVANCED_RECOVERY.REMINDER_DESC"].')</em>
						</p>';			
					}			
					
					$module .= '
					<p>
						<label for="recovery_secretkey">'.$pages["ACCOUNT.ADVANCED_RECOVERY.SECRETKEY"].'</label><br />
						<input name="recovery_secretkey" size="40" type="password" value="" /> <br><em>('.$pages["ACCOUNT.ADVANCED_RECOVERY.SECRETKEY_DESC"].')</em>
					</p>
					
					<p>
						<label for="recovery_email">'.$pages["ACCOUNT.ADVANCED_RECOVERY.NEW_EMAIL"].'</label><br />
						<input name="recovery_email" size="40" type="text" value="" /> <br><em>('.$pages["ACCOUNT.ADVANCED_RECOVERY.NEW_EMAIL_DESC"].')</em>
					</p>			
					
					<div id="line1"></div>				
					
					<p>
						<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />					
					</p>
			</fieldset>
		</form>';
		}
	}
	else
	{
		$core->sendMessageBox("Erro!", $boxMessage['ACCOUNT_NOT_HAVE_SECRET_KEY']);		
	}		
}
?>