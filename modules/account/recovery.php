<?php
if($_GET['key'])
{
	$account = new Account();
	
	if(!$account->checkChangePasswordKey($_GET['key']))
	{
		$error = Lang::Message(LMSG_RECOVERY_WRONG_KEY);
	}
	else
	{
		$password = Strings::randKey(8, 1, "lower+number");		
		if(!Emails::send($account->getEmail(), Emails::EMSG_RECOVERY_ACCOUNT_NEW_PASSWORD, array($password)))
		{
			$error = Lang::Message(LMSG_FAIL_SEND_EMAIL);
		}		
		else	
		{		
			$account->setPassword(Strings::encrypt($password));
			$account->save();
			
			$success = Lang::Message(LMSG_RECOVERY_NEWPASS_SEND);				
		}	
	}
	
	if($success)	
	{
		Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $success);
	}
	else
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
	}	
}
else
{
	if($_POST)
	{	
		$account = new Account();
		$character = new Character();
		
		$accCharacter = $account->loadByCharacterName($_POST['recovery_name']);
		
		if($_POST['recovery_information'] != 4 and (!$_POST['recovery_name'] or !$_POST['recovery_email']))
		{
			$error = Lang::Message(LMSG_FILL_FORM);
		}	
		elseif($_POST['recovery_information'] != 4 and (!$accCharacter or $account->getEmail() != $_POST['recovery_email']))
		{
			$error = Lang::Message(LMSG_RECOVERY_UNKNOWN_CHARACTER);
		}
		else
		{		
			/* RECUPERAÇÃO DO NUMERO DA CONTA */
			if($_POST['recovery_information'] == 1)
			{				
				if(!Emails::send($account->getEmail(), Emails::EMSG_RECOVERY_ACCOUNT_NAME, array($account->getName())))
				{
					$error = Lang::Message(LMSG_FAIL_SEND_EMAIL);
				}		
				else	
				{				
					$success = Lang::Message(LMSG_RECOVERY_ACCOUNT_NAME_SEND);		
				}
			}
			/* RECUPERAÇÃO DA SENHA DA CONTA */
			elseif($_POST['recovery_information'] == 2)
			{
				$key = Strings::randKey(8, 1, "number");		
				
				if(!Emails::send($account->getEmail(), Emails::EMSG_RECOVERY_ACCOUNT_PASSWORD, array($key)))
				{
					$error = Lang::Message(LMSG_FAIL_SEND_EMAIL);
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = Lang::Message(LMSG_RECOVERY_PASSWORD_SEND);			
				}			
			}
			/* RECUPERAÇÃO DO NUMERO E SENHA DA CONTA */
			elseif($_POST['recovery_information'] == 3)
			{
				$key = Strings::randKey(8, 1, "number");		
				
				if(!Emails::send($account->getEmail(), Emails::EMSG_RECOVERY_ACCOUNT_BOTH, array($account->getName(), $key)))
				{
					$error = Lang::Message(LMSG_FAIL_SEND_EMAIL);
				}		
				else	
				{
					$account->setPasswordKey($key);
					
					$success = Lang::Message(LMSG_RECOVERY_BOTH_SEND);		
				}			
			}	
			elseif($_POST['recovery_information'] == 4)
			{
				if(!$_POST['recovery_name'])
				{
					$error = Lang::Message(LMSG_RECOVERY_FILL_CHARACTER_NAME);
				}					
				elseif(!$character->loadByName($_POST['recovery_name']))
				{
					$error = Lang::Message(LMSG_CHARACTER_WRONG);
				}
				else
				{
					$_SESSION['recovery'] = $_POST['recovery_name'];
					Core::redirect("index.php?ref=account.advanced_recovery");	
				}		
			}
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