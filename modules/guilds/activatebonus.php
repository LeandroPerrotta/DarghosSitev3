<?php
use \Core\Configs;
use \Framework\Guilds;
use Framework\Player;
class View
{
	//html fields
	private $_password;	
	
	//variables
	private $_message;	

	//custom variables
	private $loggedAcc, $guild, $bonus, $charlist, $playerlist;	
	
	function View()
	{
		if(!$_GET['name'] || !Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
			return false;
			
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
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
		
		$this->Draw();
			
		return true;		
	}
	
	function Prepare()
	{
		$this->loggedAcc = new \Framework\Account();
		$this->loggedAcc->load($_SESSION['login'][0]);		

		$this->guild = new Guilds();
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}			
		
		if($this->cancel && \Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId()) != \Framework\Guilds::RANK_LEADER)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}		
		
		if(!($_GET["id"] >= 1 && $_GET["id"] <= 4)){
		    $this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
		    return false;
		}
		
		if($this->guild->HasAchiev($_GET["id"])){
		    $this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
		    return false;		    
		}
		
		foreach($this->guild->GuildBonus as $bonus){
		    if($bonus["id"] == $_GET["id"]){
		        $this->bonus = $bonus;
		    }
		}
		$chars = "";
		
		$this->playerlist = array();
		
		foreach($this->guild->Ranks as $rank)
		{
		    foreach($rank->Members as $member)
		    {
		        if($member->getLevel() >= $this->bonus["min-level"] && $member->canReceiveGuildPoints($this->bonus["id"])){
		            $this->bonus["members"]++;
		            $this->charlist .= "{$member->getName()}\n";
		            array_push($this->playerlist, $member);
		        }
		    }
		}
		
		if($this->bonus["members"] < $this->bonus["min-members"]){
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
		
		foreach($this->playerlist as $player){
		    $player instanceof Player;
		    
		    $player->onReceiveGuildPoints($this->bonus["id"], $this->guild->GetId());
		    $account = $player->loadAccount();
		    $account->addGuildPoints($this->bonus["reward"]);
		    $account->save();
		}
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_RECEIVED_BONUS);		

		return true;
	}
	
	function Draw()
	{
		global $module;
		
		$msg = "<span>
					Parabens por progredir sua guild. Os membros qualificados de sua guild receberam os seguintes beneficios:</br><br>
					Progressão alcançada: <strong>{$this->bonus["title"]}</strong></br>
					Descrição: <strong>{$this->bonus["desc"]}</strong><br>
					Guild Points que cada membro irá receber: <strong>{$this->bonus["reward"]}</strong><br>
				</span>	";
		
		\Core\Main::sendMessageBox("Entrega de Guild Points", $msg);
		
		$module .= "
		<form action='' method='post'>
			<fieldset>
				
                <p>
				    <label for='account_password'>Personagens que receberam os Guild Points</label>
				    <textarea disabled='1' style='margin: 4px 0px 0px; height: 235px; width: 139px;'>{$this->charlist}</textarea>
				</p>	
				
				<p>
					<label for='account_password'>Senha</label>
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