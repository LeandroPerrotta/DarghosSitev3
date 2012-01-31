<?php
use \Core\Configs;
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
		if(!$_GET['name'] || !Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		$this->_character = new \Framework\HTML\SelectBox();
		$this->_character->SetName("character_name");
		
		$char_list = $this->loggedAcc->getCharacterList(\Framework\Account::PLAYER_LIST_BY_ID);
		
		//listing all account characters and adding to options box the guild members
		foreach($char_list as $player_id)
		{
			if($this->guild->IsMember($player_id))
			{
				$player = new \Framework\Player();
				$player->load($player_id);
				
				$this->_character->AddOption($player->getName());
			}
		}	

		$this->_character->SelectedIndex(0);
		
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

		$this->guild = new \Framework\Guilds();
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}	

		if($this->guild->OnWar())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_IS_ON_WAR, $_GET['name']);
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
		
		$player = new \Framework\Player();
		$player->loadByName($this->_character->GetPost());
		
		if(!$this->guild->IsMember($player->getId()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}
		
		if($player->GetGuildLevel() == \Framework\Guilds::RANK_LEADER)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_CANNOT_LEAVE, $_GET['name']);	
			return false;			
		}
						
		$player->setGuildRankId( \Framework\Guilds::RANK_NO_MEMBER );
		$player->save();
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_LEAVE, $this->_character->GetPost(), $_GET['name']);		
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