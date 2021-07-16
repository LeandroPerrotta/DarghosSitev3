<?php
use \Core\Configs;
class View
{
	//html fields
	private $_password;
	private $_replywar, $_replywaroptions; //reply
	private $_warfraglimit, $_warenddate, $_warguildfee, /*$_waropponentfee,*/ $_warcomment; //negotiate
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $guild_war, $guild, $memberLevel, $opponent, $replyIsOpponent = true;		
	
	function View()
	{
		if(!$_GET["value"] || !is_numeric($_GET["value"]) || !Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
		{	
			return;
		}
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}		
		
		//repply
		$this->_replywaroptions = new \Framework\HTML\SelectBox();
		$this->_replywaroptions->SetName("replywar_options");
		$this->_replywaroptions->AddOption("Responder", "war_reply");
		$this->_replywaroptions->AddOption("Negociar", "war_negotiate");		
		$this->_replywaroptions->SelectedIndex(0);		
		
		$this->_replywar = new \Framework\HTML\SelectBox();
		$this->_replywar->SetName("reply_war");
		$this->_replywar->AddOption("Aceitar");
		$this->_replywar->AddOption("Rejeitar");
		$this->_replywar->SelectedIndex(0);
		
		//negotiate
		$this->_warfraglimit = new \Framework\HTML\Input();
		$this->_warfraglimit->SetName("war_frag_limit");
		$this->_warfraglimit->SetSize(10);
		$this->_warfraglimit->SetLenght(4);
		$this->_warfraglimit->SetValue($this->guild_war->GetFragLimit());
		
		$this->_warenddate = new \Framework\HTML\Input();
		$this->_warenddate->SetName("war_end_date");
		$this->_warenddate->SetSize(10);
		$this->_warenddate->SetLenght(3);

		$this->_warguildfee = new \Framework\HTML\Input();
		$this->_warguildfee->SetName("war_guild_fee");
		$this->_warguildfee->SetSize(10);
		$this->_warguildfee->SetLenght(9);
		$this->_warguildfee->SetValue($this->guild_war->GetGuildFee());
		
		/*$this->_waropponentfee = new \Framework\HTML\Input();
		$this->_waropponentfee->SetName("war_opponent_fee");
		$this->_waropponentfee->SetSize(10);
		$this->_waropponentfee->SetLenght(9);
		$this->_waropponentfee->SetValue($this->guild_war->GetOpponentFee());*/
		
		$this->_warcomment = new \Framework\HTML\Input();
		$this->_warcomment->SetName("war_comment");
		$this->_warcomment->IsTextArea();
		
		//geneneral
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
	
	function Prepare()
	{
		$this->loggedAcc = new \Framework\Account();
		$this->loggedAcc->load($_SESSION['login'][0]);					
		
		$this->guild_war = new \Framework\Guilds\War();
		
		if(!$this->guild_war->Load($_GET['value']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}
		
		$this->guild = new \Framework\Guilds();
		$this->opponent = new \Framework\Guilds();
		
		if($this->guild_war->GetReply() == 0)
		{
			if( !$this->guild->Load( $this->guild_war->GetOpponentId() ) )
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;			
			}			
			
			if(!$this->opponent->Load($this->guild_war->GetGuildId()))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;						
			}					
		}
		elseif($this->guild_war->GetReply() == 1)
		{
			$this->replyIsOpponent = false;
			
			if( !$this->guild->Load( $this->guild_war->GetGuildId() ) )
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;			
			}		

			if(!$this->opponent->Load($this->guild_war->GetOpponentId()))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;						
			}				
		}
		else
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}
		
		if(\Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) != \Framework\Guilds::RANK_LEADER)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}
		
		if( $this->guild_war->GetStatus() != \Framework\Guilds::WAR_DISABLED )
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}		
		
		return true;		
	}
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}		
		
		if($this->_replywaroptions->GetPost() == "war_reply")
		{
			
			if($this->_replywar->GetPost() == "Aceitar")
			{
				if($this->guild->GetBalance() < $this->guild_war->GetGuildFee())
				{
					$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_BALANCE_TOO_LOW);
					return false;						
				}
				
				$this->guild_war->SetReply( -1 );
				$this->guild_war->SetStatus( \Framework\Guilds::WAR_STARTED );
				$this->guild_war->Save();
				
				$this->guild->SetBalance($this->guild->GetBalance() - $this->guild_war->GetGuildFee());
				$this->guild->Save();
				
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_ACCEPTED, $this->guild->GetName(), $this->opponent->GetName());
				
				return true;
			}
			elseif($this->_replywar->GetPost() == "Rejeitar")
			{
				$this->guild_war->SetReply( -1 );
				$this->guild_war->Save();
				
				$this->opponent->SetBalance($this->opponent->GetBalance() + $this->guild_war->GetGuildFee());
				$this->opponent->Save();
				
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_REJECTED, $this->opponent->GetName());
				
				return true;
			}
		}
		elseif($this->_replywaroptions->GetPost() == "war_negotiate")
		{
			if(!$this->_warfraglimit->GetPost() || !$this->_warguildfee->GetPost() /*|| !$this->_waropponentfee->GetPost()*/)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
				return false;
			}		
		
			if(!is_numeric($this->_warfraglimit->GetPost()) || !is_numeric($this->_warguildfee->GetPost()) /*|| !is_numeric($this->_waropponentfee->GetPost())*/)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_NUMERIC_FIELDS);
				return false;
			}
			
			if($this->_warfraglimit->GetPost() < 10  || $this->_warfraglimit->GetPost() > 1000)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_FRAG_LIMIT);
				return false;
			}

			if($this->_warenddate->GetPost() || is_numeric($this->_warenddate->GetPost()))
			{
				if($this->_warenddate->GetPost() < 7 || $this->_warenddate->GetPost() > 360)
				{
					$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_WRONG_END_DATE);
					return false;
				}
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
	
			if($this->guild->GetBalance() < $this->_warguildfee->GetPost())
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_BALANCE_TOO_LOW);
				return false;						
			}				
			
			$this->opponent->SetBalance($this->opponent->GetBalance() + $this->guild_war->GetGuildFee());
			$this->opponent->Save();
			
			$this->guild->SetBalance($this->guild->GetBalance() - $this->_warguildfee->GetPost());
			$this->guild->Save();			
			
			$guildFee = $this->_warguildfee->GetPost();
			/*$opponentFee = $this->_waropponentfee->GetPost();*/
			$replyValue = 0;
			
			if($this->replyIsOpponent)
			{
				$replyValue = 1;
				$guildFee = $this->_warguildfee->GetPost();
				/*$opponentFee = $this->_warguildfee->GetPost();*/				
			}
			
			$this->guild_war->SetReply($replyValue);
			$this->guild_war->SetFragLimit($this->_warfraglimit->GetPost());
			$this->guild_war->SetGuildFee($guildFee);
			/*$this->guild_war->SetOpponentFee($opponentFee);*/
			
			if($this->_warenddate->GetPost())
				$this->guild_war->SetEndDate(($this->_warenddate->GetPost() * 60 * 60 * 24) + time());
			
			if($this->_warcomment->GetPost())
				$this->guild_war->SetComment($this->_warcomment->GetPost());
				
			$this->guild_war->Save();	
			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_WAR_NEGOTIATE_SEND);
			
			return true;
		}

		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
		return false;	
	}
	
	function Draw()
	{
		global $module;
		
		$endWar = round(($this->guild_war->GetEndDate() - time()) / (60 * 60 * 24));
		
		$oponentName = $this->guild->GetName();
		$guildName = $this->opponent->GetName();
		
		if(!$this->replyIsOpponent)
		{
			$oponentName = $this->opponent->GetName();
			$guildName = $this->guild->GetName();
		}
		
		$reply = "
		<div title='war_reply' class='viewable' style='margin: 0px; padding: 0px;'>
			<fieldset>		
				<p><h3>Termos da guerra:</h3></p>
	
				<p>
					<span style='font-weight: bold;'>Guilda oponente:</span> <a href='?ref=guilds.details&name={$this->opponent->GetName()}'>{$this->opponent->GetName()}</a>
				</p>	
							
				<p>
					<span style='font-weight: bold;'>Fim da Guerra:</span> Em {$endWar} dias ou {$this->guild_war->GetFragLimit()} mortes.
				</p>				
							
				<p>
					<span style='font-weight: bold;'>Pagamento por derrota:</span> {$this->guild_war->GetOpponentFee()} gold coins
				</p>
							
				<p>
					<span style='font-weight: bold;'>Comentario do líder da guilda oponente:</span> {$this->guild_war->GetComment()}
				</p>
				
				<p>
					<label for='war_reply'>Resposta:</label><br />
					{$this->_replywar->Draw()}
				</p>
						
			</fieldset>
		</div>	
		";
		
		$negotiate = "
		<div title='war_negotiate' style='margin: 0px; padding: 0px;'>
			<fieldset>						
				<p>
					<label for='war_frag_limit'>Limite de mortes para terminar a guerra (<span style='text-decoration: italic;'>de 10 a 1000</span>)</label><br />
					{$this->_warfraglimit->Draw()}
				</p>						
				
				<p>
					<label for='war_end_date'>Limite de tempo para terminar a guerra (<span style='text-decoration: italic;'>numero de dias de 7 a 360</span>)</label><br />
					{$this->_warenddate->Draw()} Este campo em branco peservara o tempo de guerra original.
				</p>
				
				<p>
					<label for='war_guild_fee'>Pagamento por derrota (<span style='text-decoration: italic;'>quantidade de gold coins de 0 a 100000000 (100 kk)</span>)</label><br />
					{$this->_warguildfee->Draw()}
				</p>
				
				<p>
					<label for='war_comment'>Comentario que será enviado a guilda oponente (<span style='text-decoration: italic;'>maximo de 500 caracteres</span>)</label><br />
					{$this->_warcomment->Draw()}
				</p>							
			</fieldset>
		</div>			
		";
		
		$module .= "
		<form action='' method='post'>
			<fieldset>
				<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
					{$this->_replywaroptions->Draw()}			
					<p class='line'></p>
				</div>		
				
				{$reply}
				{$negotiate}
				
				<p>
					<label for='account_password'>Senha</label><br />
					{$this->_password->Draw()}
				</p>

				<p class='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>					
			</fieldset>	
		</form>		
		";		
	}
}

$view = new View();
?>