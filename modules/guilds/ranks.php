<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = new Guilds();
	
	if(!$guild->loadByName($_GET['name']))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));
	}	
	else
	{		
		$guild->loadRanks();
		$ranks = $guild->getRanks();
		
		$post = Core::extractPost();
		if($post)
		{
			$haveLongRank = 0;
			$ranklist = array();
			$rankToRemove = array();
			$isFirstNull = false;
			$orderOut = false;
			
			foreach($_POST as $field => $value)
			{
				if($field != "account_password")
				{
					if(strlen($value) > 35)
					{
						$haveLongRank++;
					}
					
					$separator = explode("_", $field);
					
					if($value)
					{
						if($isFirstNull)
						{
							$orderOut = true;
							break;
						}	
							
						$ranklist[$separator[1]] = $value;	
					}			
					else		
					{
						if(count($rankToRemove) == 0)
							$isFirstNull = true;
							
						$rankToRemove[] = $separator[1];	
					}	
				}
			}			
			
			if($account->get("password") != Strings::encrypt($post[6]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}			
			elseif($orderOut)
			{
				$error = Lang::Message(LMSG_GUILD_RANK_WRONG_ORDER);	
			}			
			elseif($haveLongRank != 0)
			{
				$error = Lang::Message(LMSG_GUILD_RANK_WRONG_SIZE);	
			}
			elseif(count($ranklist) < 3)
			{
				$error = Lang::Message(LMSG_GUILD_RANK_MIMINUM_NEEDED);	
			}			
			else
			{		
				$rankHaveCharacter = 0;
				
				foreach($rankToRemove as $ranklvl)
				{
					if($guild->ereaseRank($ranklvl) == 1)
						$rankHaveCharacter++;	
				}				
				
				if($rankHaveCharacter == 0)
				{
					foreach($ranklist as $ranklvl => $rankname)
					{
						$guild->setRank($rankname, $ranklvl);
					}					
					
					$success = Lang::Message(LMSG_GUILD_RANKS_EDITED);
				}
				else
				{
					$error = Lang::Message(LMSG_GUILD_RANK_IN_USE);	
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
				
			$rank_n = 0;
				
			foreach($ranks as $rank_id => $values)
			{
				$rank_n++;
				
				$ranks_show .= "
					<p>
						{$rank_n} <input name='rank_{$rank_n}' size='40' type='text' value='{$values['name']}' />
					</p>				
				";
			}
			
			$rank_dif = 6 - $rank_n;
			
			if($rank_dif > 0)
			{
				for($i = 0; $i < $rank_dif; $i++)
				{		
					$rank_n++;
					
					$ranks_show .= "
						<p>
							{$rank_n} <input name='rank_{$rank_n}' size='40' type='text' value='' />
						</p>				
					";
				}				
			}
			
			$module .=	'
			<form action="" method="post">
				<fieldset>			
					
					'.$ranks_show.'
					
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
	}

}		
?>