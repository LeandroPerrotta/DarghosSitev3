<?php
if($_SESSION['recovery'])
{	
	$character = new Character();
	$character->loadByName($_SESSION['recovery'][0], "account_id"); 
	
	$chkEmail = new Account();
	
	$account = new Account();
	$account->load($character->get("account_id"), "password, email");
	$secretkey = $account->getSecretKey();
	
	if($secretkey)
	{
		if($_POST)
		{	
			$postSecretKey = $_POST['recovery_secretkey'];
			$postEmail = $_POST['recovery_email'];
		
			if(Core::getIpTries() >= 3)
			{
				$error = Lang::Message(LMSG_OPERATION_ARE_BLOCKED);
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				Core::increaseIpTries();
				
				if(Core::getIpTries() < 3)
					$error = Lang::Message(LMSG_RECOVERY_WRONG_SECRET_KEY);
				else
					$error = Lang::Message(LMSG_OPERATION_HAS_BLOCKED);
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = Lang::Message(LMSG_ACCOUNT_EMAIL_ALREADY_USED);
			}
			elseif(!Strings::validEmail($postEmail))
			{
				$error = Lang::Message(LMSG_WRONG_EMAIL);
			}
			else
			{
				$account->setEmail($postEmail);
				$account->save();
				
				$success = Lang::Message(LMSG_RECOVERY_EMAIL_CHANGED);	
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
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_RECOVERY_DISABLED));		
	}		
}
?>