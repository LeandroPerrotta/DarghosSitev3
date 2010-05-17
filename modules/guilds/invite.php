<?php
class View
{
	//html fields
	private $_password, $_invites;	
	
	//variables
	private $_message;	

	//custom variables
	private $loggedAcc, $guild;	
	
	function View()
	{
		if(!$_GET['name'])
			return false;
			
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		$this->_invites = new HTML_Input();
		$this->_invites->SetName("invites_list");
		$this->_invites->IsTextArea();		
			
		$this->_password = new HTML_Input();
		$this->_password->SetName("account_password");
		$this->_password->IsPassword();			
		
		if($_POST)
		{
			if(!$this->Post())
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			}
			else
			{
				Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $this->_message);
				return true;
			}
		}
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->loggedAcc = new Account();
		$this->loggedAcc->load($_SESSION['login'][0]);		

		$this->guild = new Guilds();		
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		if(Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) < GUILD_RANK_VICE)
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;
		}	

		if($this->guild->OnWar())
		{
			$this->_message = Lang::Message(LMSG_GUILD_IS_ON_WAR, $_GET['name']);
			return false;			
		}		
		
		return true;
	}
	
	function Post()
	{
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
				$character = new Character();
				
				if(!$character->loadByName($player_name))
				{
					$dontExists[] = $player_name;	
				}	
				else
				{	
					if($character->LoadGuild())
						$wasGuild[] = $player_name;
						
					if($character->getInvite())
						$wasInvited[] = $player_name;	
				}	
			}
		}
		
		if($this->loggedAcc->getPassword() != Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = Lang::Message(LMSG_WRONG_PASSWORD);
			return false;
		}	
		
		if($invites_limit)
		{				
			$this->_message = Lang::Message(LMSG_GUILD_INVITE_LIMIT);
			return false;
		}					
		
		if(count($wasGuild) != 0)
		{
			foreach($wasGuild as $name)
			{
				$wasGuild_list .= $name."<br>";
			}
			
			$this->_message = Lang::Message(LMSG_GUILD_INVITE_ALREADY_MEMBER, $wasGuild_list);
			return false;
		}
		
		if(count($wasInvited) != 0)
		{
			foreach($wasInvited as $name)
			{
				$wasInvited_list .= $name."<br>";
			}
			
			$this->_message = Lang::Message(LMSG_GUILD_INVITE_ALREADY_INVITED, $wasInvited_list);
			return false;
		}		
		
		if(count($dontExists) != 0)
		{
			foreach($dontExists as $name)
			{
				$dontExists_list .= $name."<br>";
			}
			
			$this->_message = Lang::Message(LMSG_GUILD_INVITE_CHARACTER_NOT_FOUNDS, $dontExists_list);
			return false;
		}					

		foreach($invites_list as $player_name)
		{
			$character = new Character();
			
			$character->loadByName($player_name);
			$character->inviteToGuild($this->guild->GetId());
		}
		
		$this->_message = Lang::Message(LMSG_GUILD_INVITEDS);		

		return true;
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