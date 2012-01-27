<?php
use \Core\Configs;
class View
{
	//html fields
	private $_password, $_invites;	
	
	//variables
	private $_message;	

	//custom variables
	private $cancel = false, $loggedAcc, $guild;	
	
	function View()
	{
		if(!$_GET['name'] || !Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
			return false;
			
		if($_GET["c"] && $_GET["c"] == "t")
		{
			$this->cancel = true;			
		}
			
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		$this->_invites = new \Framework\HTML\Input();
		$this->_invites->SetName("invites_list");	
		
		if($this->cancel)
		{
			$this->_invites->SetValue($_GET['name']);
			$this->_invites->IsNotWritable();
		}
		else
		{
			$this->_invites->IsTextArea();	
		}
			
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
		
		if($this->cancel)
			$this->ShowCancel();
		else
			$this->Draw();
			
		return true;		
	}
	
	function Prepare()
	{
		$this->loggedAcc = new \Framework\Account();
		$this->loggedAcc->load($_SESSION['login'][0]);		

		$this->guild = new \Framework\Guilds();
		
		if($this->cancel)
		{
			$player = new \Framework\Player();
			$player->loadByName($_GET['name']);
			
			$invite = $player->getInvite();
			
			if(!$invite)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;				
			}
			
			list($guild_id, $date) = $invite;
			
			if(!$this->guild->Load($guild_id))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;
			}			
		}
		else
		{
			if(!$this->guild->LoadByName($_GET['name']))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
				return false;
			}			
		}
		
		if(!$this->cancel && \Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) < GUILD_RANK_VICE)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}	
		
		if($this->cancel && \Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) != GUILD_RANK_LEADER)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}	

		if($this->guild->OnWar())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_ON_WAR, $_GET['name']);
			return false;			
		}		
		
		return true;
	}
	
	function PostCancel()
	{
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}	

		$player = new \Framework\Player();
		$player->loadByName($_GET['name']);
		$player->removeInvite();
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITE_CANCEL, $player->getName());
		return true;		
	}
	
	function Post()
	{
		if($this->cancel)
		{
			if($this->PostCancel())
				return true;

			return false;	
		}
		
		$invites_list = explode(";", $this->_invites->GetPost());
		$invites_limit = true;
		
		$dontExists = array();
		$wasGuild = array();
		$wasInvited = array();
		
		if(count($invites_list) < 20)
		{
			$invites_limit = false;
			
			foreach($invites_list as $player_name)
			{
				$player = new \Framework\Player();
				
				if(!$player->loadByName($player_name))
				{
					$dontExists[] = $player_name;	
				}	
				else
				{	
					if($player->LoadGuild())
						$wasGuild[] = $player_name;
						
					if($player->getInvite())
						$wasInvited[] = $player_name;	
				}	
			}
		}
		
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}
		
		if($invites_limit)
		{				
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITE_LIMIT);
			return false;
		}					
		
		if(count($wasGuild) != 0)
		{
			foreach($wasGuild as $name)
			{
				$wasGuild_list .= $name."<br>";
			}
			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITE_ALREADY_MEMBER, $wasGuild_list);
			return false;
		}
		
		if(count($wasInvited) != 0)
		{
			foreach($wasInvited as $name)
			{
				$wasInvited_list .= $name."<br>";
			}
			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITE_ALREADY_INVITED, $wasInvited_list);
			return false;
		}		
		
		if(count($dontExists) != 0)
		{
			foreach($dontExists as $name)
			{
				$dontExists_list .= $name."<br>";
			}
			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITE_CHARACTER_NOT_FOUNDS, $dontExists_list);
			return false;
		}					

		foreach($invites_list as $player_name)
		{
			$player = new \Framework\Player();
			
			$player->loadByName($player_name);
			$player->inviteToGuild($this->guild->GetId());
		}
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_INVITEDS);		

		return true;
	}
	
	function ShowCancel()
	{
		global $module;
		
		$module .= "
		<form action='' method='post'>
			<fieldset>
				
				<p>
					<label for='guild_invites'>Cancelar convite de</label><br />
					{$this->_invites->Draw()}
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
	
	function Draw()
	{
		global $module;
		
		$module .= "
		<form action='' method='post'>
			<fieldset>
				
				<p>
					<label for='guild_invites'>Personagem(s)</label><br />
					{$this->_invites->Draw()}
					<em><br><b>Instruções:</b> Lista de personagens a serem convidados a sua guild, ultilize um ; (ponto e virgula) para separar cada personagem. (ex: Slash;Fawkes;Baracs)</em>
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