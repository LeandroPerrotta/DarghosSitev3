<?php
class View
{
	//html fields
	private $_password, $_reply;	
	
	//variables
	private $_message;	

	//custom variables
	private $loggedAcc, $character;	
	
	function View()
	{
		if(!$_GET['name'])
			return false;
			
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		$this->_reply = new HTML_SelectBox();
		$this->_reply->SetName("reply");
		$this->_reply->AddOption("Aceitar");		
		$this->_reply->AddOption("Rejeitar");		
		$this->_reply->SelectedIndex(0);		
			
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

		$character_list = $this->loggedAcc->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
		
		$this->character = new Character();		
		
		if(!$this->character->loadByName($_GET['name']))
		{
			$this->_message = Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		if(!in_array($this->character->getId(), $character_list))
		{
			$this->_message = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			return false;
		}	
		
		return true;
	}
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != Strings::encrypt($this->_password->GetPost()))
		{
			$this->_message = Lang::Message(LMSG_WRONG_PASSWORD);
			return false;
		}	
		
		$invite = $this->character->getInvite();
		
		if(!$invite)
		{
			$this->_message = Lang::Message(LMSG_GUILD_CHARACTER_NOT_INVITED, $_GET['name']);
			return false;
		}		
		
		list($guild_id, $invite_date) = $invite;
		
		$guild = new Guilds();
		$guild->Load($guild_id);	

		if($guild->OnWar())
		{
			$this->_message = Lang::Message(LMSG_GUILD_IS_ON_WAR, $_GET['name']);
			return false;			
		}		
		
		if($this->_reply->GetPost() == "Aceitar")		
		{			
			$this->character->acceptInvite();	
			$this->_message = Lang::Message(LMSG_GUILD_JOIN, $_GET['name'], $guild->GetName());
		}
		elseif($this->_reply->GetPost() == "Rejeitar")
		{
			$this->character->removeInvite();
			$this->_message = Lang::Message(LMSG_GUILD_JOIN_REJECT, $guild->GetName(), $_GET['name']);		
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