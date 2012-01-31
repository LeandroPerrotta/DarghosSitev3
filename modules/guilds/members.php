<?php
use \Core\Configs;
if($_GET['name'] && Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
{
	$result = false;
	$message = "";		
	
	function proccessPost(&$message, \Framework\Account $account, \Framework\Guilds $guild)
	{
		if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}			
		
		$memberLevel = \Framework\Guilds::GetAccountLevel($account, $guild->GetId());
		$player = $guild->SearchMemberByName($_POST["guild_member"]);
		$player instanceof \Framework\Player;
		
		if(!$player)
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_NOT_MEMBER, $_POST["guild_member"], $_GET['name']);
			return false;
		}
		
		if($player->GetGuildLevel() < \Framework\Guilds::RANK_LEADER)
		{
			if($player->GetGuildLevel() >= $memberLevel)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PERMISSION);
				return false;
			}
		}
		else
		{
			if($memberLevel != \Framework\Guilds::RANK_LEADER)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PERMISSION);
				return false;				
			}
		}
		
		if($_POST["guild_action"] == "setRank")
		{
			if($player->GetGuildLevel() == \Framework\Guilds::RANK_LEADER)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PERMISSION);
				return false;
			}
			
			$selectedRank = $guild->SearchRankByLevel($_POST["member_rank"]);
			
			if(!$selectedRank || $selectedRank->GetLevel() == \Framework\Guilds::RANK_LEADER)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;
			}
			
			if($selectedRank->GetLevel() == \Framework\Guilds::RANK_VICE && !$player->isPremium())
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_ONLY_PREMIUM);
				return false;				
			}

			if($selectedRank->GetLevel() == \Framework\Guilds::RANK_VICE && $player->loadAccount()->getGuildLevel() >= \Framework\Guilds::RANK_VICE)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK);
				return false;
			}			
			
			$player->setGuildRankId($selectedRank->GetId());
		}
		elseif($_POST["guild_action"] == "setNick")
		{
			if(strlen($_POST["member_nick"]) < 3 or strlen($_POST["member_nick"]) > 15)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_TITLE_SIZE);
				return false;
			}
			
			$player->setGuildNick($_POST["member_nick"]);
		}
		elseif($_POST["guild_action"] == "eraseNick")
		{			
			$player->setGuildNick("");
		}
		elseif($_POST["guild_action"] == "exclude")
		{
			if($player->GetGuildLevel() == \Framework\Guilds::RANK_LEADER)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_PERMISSION);
				return false;
			}
			
			if($guild->OnWar())
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_ON_WAR, $_GET['name']);
				return false;			
			}			
			
			$player->setGuildRankId( null );
		}
		
		$player->save();	
		$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_MEMBER_EDITED, $_POST["guild_member"], $_GET['name']);
		
		return true;
	}	
	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	$character_list = $account->getCharacterList();	
	
	$guild = new \Framework\Guilds();
	
	if(!$guild->LoadByName($_GET['name']))
	{	
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']));	
	}
	elseif(\Framework\Guilds::GetAccountLevel($account, $guild->GetId()) < \Framework\Guilds::RANK_VICE)
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
		
			$lowerRank = $guild->SearchRankByLowest();
			
			foreach($guild->Ranks as $rank)
			{
				if($rank->GetLevel() != \Framework\Guilds::RANK_LEADER)
				{
					$option_ranks .= "<option ".(($rank->GetId() == $lowerRank->GetId()) ? "selected='selected'" : "")." value='{$rank->GetLevel()}'>{$rank->GetName()}</option>";
				}	
				
				foreach($rank->Members as $member)
				{
					$options .= "<option value='{$member->getName()}'>{$member->getName()} ({$rank->GetName()})</option>";
				}
			}
			
			$module .=	'
			<form action="" method="post">
				<fieldset>
					
					<p>
						<label for="guild_member">Lista de Membros</label><br />
						<select name="guild_member">'.$options.'</select>
					</p>					
					
					<p>
						<label for="guild_action">Operações:</label><br />
						
						<ul id="pagelist">
							<li><input name="guild_action" type="radio" value="setRank" checked="checked"> Configurar rank para <select name="member_rank">'.$option_ranks.'</select></li>
							<li><input name="guild_action" type="radio" value="setNick"> Configurar titulo para <input name="member_nick" size="25" type="text" value="" /></li>
							<li><input name="guild_action" type="radio" value="eraseNick"> Apagar titulo</li>
							<li><input name="guild_action" type="radio" value="exclude"> Dispençar da Guilda </li>
						</ul>	
					</p>					
					
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