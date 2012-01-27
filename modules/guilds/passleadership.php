<?php
use \Core\Configs;
if($_GET['name'] && Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
{
	$result = false;
	$message = "";	

	function proccessPost(&$message, Account $account, Guilds $guild)
	{
		if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}			

		$member = $guild->SearchMemberByName($_POST["member_candidate"]);
		
		if(!$member)
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_NOT_MEMBER, $_POST["guild_member"], $_GET['name']);
			return false;			
		}
		
		$member->LoadGuild();
		
		if($member->GetGuildLevel() != GUILD_RANK_VICE)
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PERMISSION);
			return false;
		}
		
		$leader_rank = $guild->SearchRankByLevel(GUILD_RANK_LEADER);
		$vice_rank = $guild->SearchRankByLevel(GUILD_RANK_VICE);

		$member->setGuildRankId($leader_rank->GetId());
		$member->save();		
		
		$old_owner = new \Framework\Player();
		$old_owner->load($guild->GetOwnerId());
		$old_owner->LoadGuild();
		$old_owner->setGuildRankId($vice_rank->GetId());
		$old_owner->save();
		
		$guild->SetOwnerId($member->getId());
		$guild->Save();		
		
		$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PASSLEADERSHIP, $_GET['name'], $old_owner->getName(), $_POST["member_candidate"]);
		return true;
	}
	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	$guild = new \Framework\Guilds();
	
	if(!$guild->LoadByName($_GET['name']))
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif(\Framework\Guilds::GetAccountLevel($account, $guild->GetId()) != GUILD_RANK_LEADER)
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT));
	}	
	else
	{		
		if($_POST)
		{
			$result = (proccessPost($message, $account, $guild)) ? true : false;		
		}
			
		if($result)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $message);
		}
		else
		{
			if($_POST)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $message);
			}			
						
			$vices = $guild->SearchRankByLevel(GUILD_RANK_VICE);
			
			foreach($vices->Members as $member)
			{
				$options .= "<option value='{$member->getName()}'>{$member->getName()}</option>";
			}			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

			';

			if(count($vices->Members) != 0)
			{
				$module .=	'
				<p>
					<label for="member_candidate">Membros Cadidatos</label><br />		
					<select name="member_candidate">'.$options.'</select>
				</p>	

				<p>
					<label for="account_password">Senha</label><br />
					<input name="account_password" size="40" type="password" value="" />
				</p>						
				
				<div id="line1"></div>
				
				<p>
					<input class="button" type="submit" value="Enviar" />
				</p>					
				';
			}
			else
			{
				$module .=	'
				<p>
					É necessario possuir ao menos 1 vice lider disponivel para que seja possivel a  transferencia a liderança de uma guilda.
				</p>	
				';			
			}
			
			$module .=	'
			</fieldset>
			</form>';	
		}	
	}

}		
?>