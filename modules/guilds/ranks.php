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
		
		//faremos as verificações primarias de todos os ranks do formulario
		
		if(!$_POST["leader"] || !$_POST["vice"] || !$_POST["member_1"])
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_MIMINUM_NEEDED);
			return false;
		}		
		
		if(strlen($_POST["leader"]) > 35 || strlen($_POST["vice"]) > 35 || strlen($_POST["member_1"]) > 35 )
		{
			$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_SIZE);
			return false;
		}
		
		$rank_opt_3 = $guild->SearchRankByLevel(\Framework\Guilds::RANK_MEMBER_OPT_3);
		if($_POST["member_2"])
		{
			if(strlen($_POST["member_2"]) > 35)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_SIZE);
				return false;			
			}
			
			if(!$rank_opt_3)
			{
				$rank_opt_3 = new \Framework\Guilds\Rank();
				$rank_opt_3->SetGuildId($guild->GetId());			
			}			
			
			$rank_opt_3->SetName($_POST["member_2"]);
			$rank_opt_3->SetLevel(\Framework\Guilds::RANK_MEMBER_OPT_3);
		}
		else
		{			
			if($rank_opt_3)
			{
				if($rank_opt_3->MemberCount() > 0)
				{
					$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_IN_USE);
					return false;
				}
				
				$guild->AddRankToDelete($rank_opt_3);
			}		
		}
		
		$rank_opt_2 = $guild->SearchRankByLevel(\Framework\Guilds::RANK_MEMBER_OPT_2);
		if($_POST["member_3"])
		{
			if(strlen($_POST["member_3"]) > 35)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_SIZE);
				return false;					
			}
			
			if(!$_POST["member_2"])
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_ORDER);
				return false;					
			}
			
			if(!$rank_opt_2)
			{
				$rank_opt_2 = new \Framework\Guilds\Rank();
				$rank_opt_2->SetGuildId($guild->GetId());			
			}			
			
			$rank_opt_2->SetName($_POST["member_3"]);
			$rank_opt_2->SetLevel(\Framework\Guilds::RANK_MEMBER_OPT_2);
		}
		else
		{
			if($rank_opt_2)
			{
				if($rank_opt_2->MemberCount() > 0)
				{
					$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_IN_USE);
					return false;
				}
				
				$guild->AddRankToDelete($rank_opt_2);
			}			
		}
		
		$rank_opt_1 = $guild->SearchRankByLevel(\Framework\Guilds::RANK_MEMBER_OPT_1);
		if($_POST["member_4"])
		{
			if(strlen($_POST["member_4"]) > 35)
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_SIZE);
				return false;					
			}
			
			if(!$_POST["member_2"] || !$_POST["member_3"])
			{
				$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_WRONG_ORDER);
				return false;					
			}
			
			if(!$rank_opt_1)
			{
				$rank_opt_1 = new \Framework\Guilds\Rank();
				$rank_opt_1->SetGuildId($guild->GetId());			
			}
			
			$rank_opt_1->SetName($_POST["member_4"]);
			$rank_opt_1->SetLevel(\Framework\Guilds::RANK_MEMBER_OPT_1);				
		}
		else
		{		
			if($rank_opt_1)
			{
				if($rank_opt_1->MemberCount() > 0)
				{
					$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANK_IN_USE);
					return false;
				}
				
				$guild->AddRankToDelete($rank_opt_1);
			}			
		}
		
		//tudo verificado aqui, iniciando as operações
		
		//deletamos os ranks marcados para serem deletados (que não foram preenchidos)
		$guild->DeleteRanks();
		
		//alteramos os nomes dos ranks primarios
		$rank = $guild->SearchRankByLevel(\Framework\Guilds::RANK_LEADER);
		$rank->SetName($_POST["leader"]);
		$rank->Save();
		
		$rank = $guild->SearchRankByLevel(\Framework\Guilds::RANK_VICE);
		$rank->SetName($_POST["vice"]);	
		$rank->Save();	
		
		$rank = $guild->SearchRankByLevel(\Framework\Guilds::RANK_MEMBER);
		$rank->SetName($_POST["member_1"]);	
		$rank->Save();

		if($_POST["member_2"])
			$rank_opt_3->Save();
		
		if($_POST["member_3"])
			$rank_opt_2->Save();
		
		if($_POST["member_4"])
			$rank_opt_1->Save();
		
		$message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RANKS_EDITED);
		return true;	
	}
	
	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
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
				
			$rank_pos = 3;
			$rank_n = 0;
			$member_n = 0;
			
			$memberLevel = \Framework\Guilds::GetAccountLevel($account, $guild->GetId());
			
			//pegamos os ranks existentes na guilda e montamos o view
			foreach($guild->Ranks as $rank)
			{				
				$rank_n++;
				
				$readOnly = "";
				
				if($rank_pos == 3)
				{
					$rank_name = "leader";
					$rank_pos--;
					
					if(!\Framework\Guilds::IsAccountGuildOwner($account, $guild))
						$readOnly = "readonly='readonly'";
				}
				elseif($rank_pos == 2)
				{
					$rank_name = "vice";
					$rank_pos--;
					
					if($memberLevel == \Framework\Guilds::RANK_VICE)
						$readOnly = "readonly='readonly'";				
				}
				elseif($rank_pos == 1)
				{
					$member_n++;
					
					$rank_name = "member_{$member_n}";
				}	
				
				$ranks_show .= "
					<p>
						{$rank_n} <input name='{$rank_name}' {$readOnly} size='40' type='text' value='{$rank->GetName()}' />
					</p>				
				";
			}
			
			//verificamos se sobra slots (há um limite de 6 ranks por guilda)
			$rank_dif = 6 - $rank_n;
			
			if($rank_dif > 0)
			{
				//há sobra de slots, então preenchemos os novos views vazio para o jogador preencher (se ele quiser)
				for($i = 0; $i < $rank_dif; $i++)
				{		
					$rank_n++;
					
					$member_n++;
					
					$rank_name = "member_{$member_n}";					
					
					$ranks_show .= "
						<p>
							{$rank_n} <input name='{$rank_name}' size='40' type='text' value='' />
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