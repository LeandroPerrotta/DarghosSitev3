<?php
use \Core\Configs;
class View
{
	//html fields
	private $player, $_password, $_html_action, $_html_notification;
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	function View()
	{		
		if(!$_GET['name'])
			return false;		
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}		
		
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
		
		if(!$this->loggedAcc->load($_SESSION['login'][0]))
		{
			\Core\Main::requireLogin();
			return false;			
		}		
		
		$this->player = new \Framework\Player();
		
		if(!$this->player->loadByName($_GET["name"]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_WRONG);
			return false;
		}
		
		if($this->player->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			return false;
		}		
		
		$this->_html_action = new \Framework\HTML\Input();
		$this->_html_action->SetName("remove_skulls");
		$this->_html_action->IsCheackeable();
		$this->_html_action->SetValue("true");
		
		switch($this->player->getSkull())
		{
			case t_Skulls::Red:
				$this->_html_action->SetLabel("Desejo remover a red skull do personagem <b>{$this->player->getName()}</b> por R$ ".number_format(Configs::Get(Configs::eConf()->PREMCOST_REMOVE_RED_SKULL) / 100, 2)." de creditos em minha conta.");
				break;
				
			case t_Skulls::Black:
				$this->_html_action->SetLabel("Desejo remover a black skull do personagem <b>{$this->player->getName()}</b> por R$ ".number_format(Configs::Get(Configs::eConf()->PREMCOST_REMOVE_BLACK_SKULL) / 100, 2)." de creditos em minha conta.");
				break;
				
			default:
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
				return false;
				break;
		}
		
		$this->_html_notification = new \Framework\HTML\Input();
		$this->_html_notification->SetName("notification");
		$this->_html_notification->IsCheackeable();
		$this->_html_notification->SetValue("true");
		$this->_html_notification->SetLabel("Eu estou ciente que este serviço inclui somente e exclusivamente a remoção da skull (caveira) do personagem, <b>porem nenhuma frag!</b>E que portanto, voltar a matar outros jogadores injustificadamente resultará no retorno da skull.");
		
		$this->_password = new \Framework\HTML\Input();
		$this->_password->SetName("password");
		$this->_password->IsPassword();
		$this->_password->SetLabel("Confirmar senha");		
		
		return true;
	}
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->getPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FAIL_LOGIN);
			return false;			
		}
		
		if($this->player->getOnline() == 1)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			return false;				
		}
		
		if(!(bool)$this->_html_action->GetPost() || !(bool)$this->_html_notification->GetPost())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_REMOVE_SKILLS_CONFIRM);
			return false;
		}
		
		$cost = 0;
		$service = 0;
		
		switch($this->player->getSkull())
		{
			case t_Skulls::Red:
				$cost = Configs::Get(Configs::eConf()->PREMCOST_REMOVE_RED_SKULL);
				$service = t_PremdaysServices::ClearSkullRed;
				break;
		
			case t_Skulls::Black:
				$cost = Configs::Get(Configs::eConf()->PREMCOST_REMOVE_BLACK_SKULL);
				$service = t_PremdaysServices::ClearSkullBlack;
				break;
		}		
		
		if($this->loggedAcc->getBalance() == 0 || $this->loggedAcc->getBalance() < $cost)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->STAMINA_NOT_HAVE_PREMDAYS);
			return false;			
		}
	
		$this->loggedAcc->addBalance(-$cost);
		$this->loggedAcc->save();
		
		$this->player->setSkull(t_Skulls::None);
		$this->player->setSkullTime(0);
		$this->player->save();
				
		\Core\Main::addChangeLog($service, $this->player->getId(), $cost);
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REMOVE_SKULL_SUCCESSFULY, $this->player->getName(), $cost);
		return true;
	}
	
	function Draw()
	{
		global $module;

		$module .=	'
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				
				<p>
					'.$this->_html_action->Draw().'
				</p>
				
				<p>
					'.$this->_html_notification->Draw().'
				</p>
				
				<p>
					'.$this->_password->Draw().'
				</p>			
				
				<p id="line"></p>
				
				<p>
					<input class="button" type="submit" value="Enviar" />
				</p>
			</fieldset>
		</form>';
	}
}

$view = new View();
?>