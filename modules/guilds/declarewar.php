<?php
use \Core\Configs;
class View
{
	//html fields
	private $_waropponent, $_warfraglimit, $_warenddate, $_warguildfee, /*$_waropponentfee,*/ $_warcomment, $_password;
	
	//variables
	private $_message;
	
	//custom variables
	private $guildList, $loggedAcc, $guild;
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}
		
		if(!$this->_warfraglimit->GetPost() || !$this->_warenddate->GetPost())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
			return false;
		}		
	
		if(!is_numeric($this->_warfraglimit->GetPost()) || !is_numeric($this->_warenddate->GetPost()) || !is_numeric($this->_warguildfee->GetPost()) /*|| !is_numeric($this->_waropponentfee->GetPost())*/)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_NUMERIC_FIELDS);
			return false;
		}
		
		if($this->guild->GetBalance() < $this->_warguildfee->GetPost())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_BALANCE_TOO_LOW);
			return false;			
		}
		
		if($this->_warfraglimit->GetPost() < 10  || $this->_warfraglimit->GetPost() > 1000)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_FRAG_LIMIT);
			return false;
		}
		
		if($this->_warenddate->GetPost() < 7  || $this->_warenddate->GetPost() > 360)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_END_DATE);
			return false;
		}
		
		if($this->_warguildfee->GetPost() < 0  || $this->_warguildfee->GetPost() > 100000000 /*|| $this->_waropponentfee->GetPost() < 0 || $this->_waropponentfee->GetPost() > 100000000*/)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_FEE);
			return false;
		}
		
		if($this->_warcomment->GetPost())
		{
			if(strlen($this->_warcomment->GetPost()) > 500)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_COMMENT_LENGTH);
				return false;				
			}
		}
		
		$opponent = new \Framework\Guilds();
		
		if(!$opponent->LoadByName($this->_waropponent->GetPost()) || $opponent->GetStatus() == \Framework\Guilds::STATUS_FORMATION || $opponent->GetName() == $_GET['name'])
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}
		
		if($this->guild->IsAtWarAgainst($opponent->GetId()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_ALREADY, $this->_waropponent->GetPost());
			return false;			
		}
		
		$this->guild->SetBalance($this->guild->GetBalance() - $this->_warguildfee->GetPost());
		$this->guild->Save();
		
		$guild_war = new \Framework\Guilds\War();
		
		$guild_war->SetGuildId($this->guild->GetId());
		$guild_war->SetOpponentId($opponent->GetId());
		$guild_war->SetFragLimit($this->_warfraglimit->GetPost());
		$guild_war->SetDeclarationDate(time());
		$guild_war->SetEndDate(($this->_warenddate->GetPost() * 60 * 60 * 24) + time ());
		$guild_war->SetGuildFee($this->_warguildfee->GetPost());
		/*$guild_war->SetOpponentFee($this->_waropponentfee->GetPost());*/
		$guild_war->SetComment($this->_warcomment->GetPost());
		$guild_war->Save();
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_DECLARED, $this->guild->GetName(), $opponent->GetName(), $this->_warfraglimit->GetPost(), $this->_warenddate->GetPost(), $this->_warguildfee->GetPost(), $this->_warguildfee->GetPost());
		
		return true;			
	}	
	
	function Prepare()
	{
		$this->loggedAcc = new \Framework\Account();
		$this->loggedAcc->load($_SESSION['login'][0]);
		
		$this->guild = new \Framework\Guilds();
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		if(\Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) != \Framework\Guilds::RANK_LEADER)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}	
		
		if($this->guild->GetStatus() == \Framework\Guilds::STATUS_FORMATION)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NEED_TO_BE_FORMED);
			return false;
		}
		
		$this->guildList = \Framework\Guilds::ActivedGuildsList();
		
		if(!$this->guildList)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_NO_HAVE_OPPONENTS);
			return false;
		}		
		
		return true;
	}
	
	function View()
	{
		if(!$_GET['name'] || !Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
		{
			return;
		}

		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;
		}
		
		$this->_waropponent = new \Framework\HTML\SelectBox();
		$this->_waropponent->SetName("war_opponent");
		
		$first = true;
		
		if($this->guildList)
		{
			foreach($this->guildList as $guild)
			{
				if($guild->GetName() == $_GET["name"])
					continue;
				
				if($first)
				{
					$index = $this->_waropponent->AddOption($guild->GetName());
					$this->_waropponent->SelectedIndex(0);
				}
				else
					$this->_waropponent->AddOption($guild->GetName());
					
				$first = false;	
			}			
		}		
		
		$this->_warfraglimit = new \Framework\HTML\Input();
		$this->_warfraglimit->SetName("war_frag_limit");
		$this->_warfraglimit->SetSize(10);
		$this->_warfraglimit->SetLenght(4);
		
		$this->_warenddate = new \Framework\HTML\Input();
		$this->_warenddate->SetName("war_end_date");
		$this->_warenddate->SetSize(10);
		$this->_warenddate->SetLenght(3);
		
		$this->_warguildfee = new \Framework\HTML\Input();
		$this->_warguildfee->SetName("war_guild_fee");
		$this->_warguildfee->SetSize(10);
		$this->_warguildfee->SetLenght(9);
		
		/*$this->_waropponentfee = new \Framework\HTML\Input();
		$this->_waropponentfee->SetName("war_opponent_fee");
		$this->_waropponentfee->SetSize(10);
		$this->_waropponentfee->SetLenght(9);*/
		
		$this->_warcomment = new \Framework\HTML\Input();
		$this->_warcomment->SetName("war_comment");
		$this->_warcomment->IsTextArea();
		
		$this->_password = new \Framework\HTML\Input();
		$this->_password->SetName("account_password");
		$this->_password->IsPassword();	
		
		if($_POST)
		{
			if(!$this->Post())
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				return true;
			}
		}
		
		$this->Draw();
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		$module .=	"
		<form action='' method='post'>
			<fieldset>			
				
				<p>
					<label for='war_opponent'>Guilda oponente</label><br />
					{$this->_waropponent->Draw()}
				</p>
				
				<p>
					<label for='war_frag_limit'>Limite de mortes para terminar a guerra (<span style='text-decoration: italic;'>de 10 a 1000</span>)</label><br />
					{$this->_warfraglimit->Draw()}
				</p>						
				
				<p>
					<label for='war_end_date'>Limite de tempo para terminar a guerra (<span style='text-decoration: italic;'>numero de dias de 7 a 360</span>)</label><br />
					{$this->_warenddate->Draw()}
				</p>

				<p>
					<label for='war_guild_fee'>Pagamento por derrota (<span style='text-decoration: italic;'>quantidade de gold coins de 0 a 100000000 (100 kk)</span>)</label><br />
					{$this->_warguildfee->Draw()}
				</p>
				
				<p>
					<label for='war_comment'>Comentario que será enviado a guilda oponente (<span style='text-decoration: italic;'>maximo de 500 caracteres</span>)</label><br />
					{$this->_warcomment->Draw()}
				</p>
				
				<p>
					<label for='account_password'>Confirmar senha</label><br />
					{$this->_password->Draw()}
				</p>
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>
			</fieldset>
		</form>";		
	}
}

$view = new View();
?>