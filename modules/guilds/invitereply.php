<?php
use \Core\Configs;
use \Framework\Guilds;
class View
{
	//html fields
	private $_password, $_reply;	
	
	//variables
	private $_message;	

	//custom variables
	private $loggedAcc, $player;	
	
	function View()
	{
		if(!$_GET['name'] || !Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
			return false;
			
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		$this->_reply = new \Framework\HTML\SelectBox();
		$this->_reply->SetName("reply");
		$this->_reply->AddOption("Aceitar");		
		$this->_reply->AddOption("Rejeitar");		
		$this->_reply->SelectedIndex(0);		
			
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

		$character_list = $this->loggedAcc->getCharacterList(\Framework\Account::PLAYER_LIST_BY_ID);
		
		$this->player = new \Framework\Player();		
		
		if(!$this->player->loadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		if(!in_array($this->player->getId(), $character_list))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
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
		
		$invite = $this->player->getInvite();
		
		if(!$invite)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_CHARACTER_NOT_INVITED, $_GET['name']);
			return false;
		}		
		
		list($guild_id, $invite_date) = $invite;
		
		$guild = new Guilds();
		$guild->Load($guild_id);	

		if($guild->OnWar())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_ON_WAR, $_GET['name']);
			return false;			
		}		
		
		if($this->_reply->GetPost() == "Aceitar")		
		{			
			$this->player->acceptInvite();	
			Guilds::LogMessage("The player {$this->player->getName()} ({$this->player->getId()}) has joined to guild {$guild->GetName()} ({$guild->GetId()}).");
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_JOIN, $_GET['name'], $guild->GetName());
		}
		elseif($this->_reply->GetPost() == "Rejeitar")
		{
			Guilds::LogMessage("The player {$this->player->getName()} ({$this->player->getId()}) has declined to join to guild {$guild->GetName()} ({$guild->GetId()}).");
			$this->player->removeInvite();
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_JOIN_REJECT, $guild->GetName(), $_GET['name']);		
		}	
		
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		$module .= "
		<form action='' method='post'>
			<fieldset>

				<p>
					<label for='invite_reply'>Resposta:</label><br />
					{$this->_reply->Draw()}
				</p>					
				
				<p>
					<label for='account_password'>Senha</label><br />
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