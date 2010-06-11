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
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		$this->loggedAcc = new Account();
		$this->loggedAcc->load($_SESSION['login'][0]);
		
		$char_list = $this->loggedAcc->getCharacterList(ACCOUNT_CHARACTERLIST_BY_NAME);
		
		if(count($char_list) == 0 || $this->loggedAcc->getHighLevel() < 20)
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_FORUM_ACCOUNT_NOT_HAVE_CHARACTERS));
			return false;						
		}
		
		$this->_character = new HTML_SelectBox();
		$this->_character->SetName("character");
	
		foreach($char_list as $playerName)
		{
			$this->_character->AddOption($playerName);
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
		$this->user = new Forum_User();
		
		if($this->user->LoadByAccount($_SESSION['login'][0]))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
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
		
		$char_list = $this->loggedAcc->getCharacterList(ACCOUNT_CHARACTERLIST_BY_NAME);
		
		if(!in_array($this->_character->GetPost(), $char_list))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;				
		}
		
		$character = new Character();
		$character->loadByName($this->_character->GetPost());
		
		$user = new Forum_User();
		
		$user->SetAccountId($this->loggedAcc->getId());
		$user->SetPlayerId($character->getId());
		$user->Save();
		
		$this->_message = Lang::Message(LMSG_FORUM_ACCOUNT_REGISTERED);
		return true;
	}
	
	function Draw()
	{
		global $module;

		$module .=	"
		<form action='' method='post'>
			<fieldset>
				
				<p>Atraves deste formulario você poderá registrar sua conta para ultilizar o Forum do Darghos, participando de topicos, respondendo enquetes e tudo mais. Você irá selecionar abaixo o personagem da sua conta que deverá ser usado como usuario, assim em seus posts aparecerá este personagem como autor, junto com um link para o profile de seu personagem.</p>
				
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