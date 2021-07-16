<?php
if($_SESSION['recovery'])
{	
	$player = new \Framework\Player();
	$player->loadByName($_SESSION['recovery']); 
	
	$chkEmail = new \Framework\Account();
	
	$account = new \Framework\Account();
	$account->load($player->get("account_id"));
	$secretkey = $account->getSecretKey();
	
	if($secretkey)
	{
		if($_POST)
		{	
			$postSecretKey = $_POST['recovery_secretkey'];
			$postEmail = $_POST['recovery_email'];
		
			if(\Core\Main::getIpTries() >= 3)
			{
				$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_ARE_BLOCKED);
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				\Core\Main::increaseIpTries();
				
				if(\Core\Main::getIpTries() < 3)
					$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->RECOVERY_WRONG_SECRET_KEY);
				else
					$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_HAS_BLOCKED);
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_EMAIL_ALREADY_USED);
			}
			elseif(!\Core\Strings::validEmail($postEmail))
			{
				$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_EMAIL);
			}
			else
			{
				$account->setEmail($postEmail);
				$account->save();
				
				$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->RECOVERY_EMAIL_CHANGED);	
			}
		}
	
		if($success)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
		}
		else
		{
			if($error)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
			}
			
		global $pages, $buttons;	
			
		$module .= '	
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>			
			
					<p>
						<label for="recovery_name">'.$pages["ACCOUNT.ADVANCED_RECOVERY.CHARACTER_NAME"].'</label><br />
						<input readonly="readonly" name="recovery_name" size="40" type="text" value="'.$_SESSION['recovery'].'" />
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
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->RECOVERY_DISABLED));		
	}		
}
?>