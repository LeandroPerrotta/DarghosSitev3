<?php
class View
{
	//html fields
	private $_password, $_character;
	
	//variables
	private $_message;		
	
	//custom variables
	private $loggedAcc, $guild;		
	
	function View()
	{
		if(!$_GET['name'] || ENABLE_GUILD_READ_ONLY)
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		$this->_character = new HTML_SelectBox();
		$this->_character->SetName("character_name");
		
		$char_list = $this->loggedAcc->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
		
		//listing all account characters and adding to options box the guild members
		foreach($char_list as $player_id)
		{
			if($this->guild->IsMember($player_id))
			{
				$character = new Character();
				$character->load($player_id);
				
				$this->_character->AddOption($character->getName());
			}
		}	

		$this->_character->SelectedIndex(0);
		
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

		if($this->guild->OnWar())
		{
			$this->_message = Lang::Message(LMSG_GUILD_IS_ON_WAR, $_GET['name']);
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
		
		$character = new Character();
		$character->loadByName($this->_character->GetPost());
		
		if(!$this->guild->IsMember($character->getId()))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;
		}
		
		$character->LoadGuild();
		
		if($character->GetGuildLevel() == GUILD_RANK_LEADER)
		{
			$this->_message = Lang::Message(LMSG_GUILD_CANNOT_LEAVE, $_GET['name']);	
			return false;			
		}
						
		$character->setGuildRankId( null );
		$character->save();
		
		$this->_message = Lang::Message(LMSG_GUILD_LEAVE, $this->_character->GetPost(), $_GET['name']);		
		return true;		
	}
	
	function Draw()
	{
		global $module;		
		
		$module .= "
		<form action='' method='post'>
			<fieldset>

				<p>
					<label for='character_name'>Selecione o Personagem</label><br />		
					{$this->_character->Draw()}
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