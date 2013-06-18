<?php
class View
{
	//html fields
	private $_character, $_password;
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	function View()
	{		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		$this->loggedAcc = new \Framework\Account();
		$this->loggedAcc->load($_SESSION['login'][0]);
		
		$char_list = $this->loggedAcc->getCharacterList(Framework\Account::PLAYER_LIST_BY_NAME);
		
		if(count($char_list) == 0 || $this->loggedAcc->getHighLevel() < 20)
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_ACCOUNT_NOT_HAVE_CHARACTERS));
			return false;						
		}
		
		$this->_character = new \Framework\HTML\SelectBox();
		$this->_character->SetName("character");
	
		foreach($char_list as $playerName)
		{
			$this->_character->AddOption($playerName);
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
		$this->user = new \Framework\Forums\User();
		
		if($this->user->LoadByAccount($_SESSION['login'][0]))
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
		
		$char_list = $this->loggedAcc->getCharacterList(Framework\Account::PLAYER_LIST_BY_NAME);
		
		if(!in_array($this->_character->GetPost(), $char_list))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;				
		}
		
		$player = new \Framework\Player();
		$player->loadByName($this->_character->GetPost());
		
		$user = new \Framework\Forums\User();
		
		$user->InsertExternalForum($this->loggedAcc, $player, $this->_password->GetPost());
		$user->SetAccountId($this->loggedAcc->getId());
		$user->SetPlayerId($player->getId());
		$user->Save();
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_ACCOUNT_REGISTERED);
		return true;
	}
	
	function Draw()
	{
		global $module;

		$module .=	"
		<form action='' method='post'>
			<fieldset>
				
				<p>Atraves deste formulario você poderá registrar sua conta para ultilizar o Forum do Darghos, participando de topicos, respondendo enquetes e tudo mais. Você irá selecionar abaixo o personagem da sua conta que deverá ser usado como usuario no Forum.</p>
				
				<p>
					<label for='character'>Selecionar personagem</label><br />
					{$this->_character->Draw()}
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