<?php
class View
{
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $guild_war, $memberLevel;	
	
	function View()
	{
		if(!$_GET['value'])
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			//$this->memberLevel = Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId());
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->guild_war = new Guild_War();
		
		if(!$this->guild_war->Load($_GET['value']))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;
		}
		
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		$guild = new Guilds();
		$guild->Load($this->guild_war->GetGuildId());
		
		$opponent = new Guilds();
		$opponent->Load($this->guild_war->GetOpponentId());
		
		$endWar = round(($this->guild_war->GetEndDate() - time()) / (60 * 60 * 24));
	
		$status = "";	
		
		if($this->guild_war->GetStatus() == GUILD_WAR_DISABLED && $this->guild_war->GetReply() > -1)
		{
			$status = "Em negociação.";
		}
		elseif($this->guild_war->GetStatus() == GUILD_WAR_DISABLED && $this->guild_war->GetReply() == -1)
		{
			$status = "Encerrada.";
		}		
		elseif($this->guild_war->GetStatus() == GUILD_WAR_WAITING)
		{
			$status = "A iniciar no proximo server save.";
		}		
		elseif($this->guild_war->GetStatus() == GUILD_WAR_STARTED)
		{
			$status = "Em andamento.";
		}			
		
		$table = new HTML_Table();
		$table->AddDataRow("Detalhes da guerra");
		
		$table->AddField("Status", 35);
		$table->AddField($status);
		$table->AddRow();
		
		$table->AddField("Declarada por");
		$table->AddField("<a href='?ref=guilds.details&name={$guild->GetName()}'>{$guild->GetName()}</a>");	
		$table->AddRow();	
		
		$table->AddField("Contra");
		$table->AddField("<a href='?ref=guilds.details&name={$opponent->GetName()}'>{$opponent->GetName()}</a>");	
		$table->AddRow();	
		
		$table->AddField("Declarada em");
		$table->AddField(Core::formatDate($this->guild_war->GetDeclarationDate()));	
		$table->AddRow();	
		
		$table->AddField("Termina em");
		$table->AddField(Core::formatDate($this->guild_war->GetEndDate()) . " ({$endWar} dias)");	
		$table->AddRow();	
		
		$guildFrags = "{$this->guild_war->GetGuildFrags()} / {$this->guild_war->GetFragLimit()}";
		
		$table->AddField("Declarante Frags");
		$table->AddField($guildFrags);	
		$table->AddRow();	
		
		$opponentFrags = "{$this->guild_war->GetOpponentFrags()} / {$this->guild_war->GetFragLimit()}";
		
		$table->AddField("Oponente Frags");
		$table->AddField($opponentFrags);	
		$table->AddRow();	
		
		$fee = "
			{$this->guild_war->GetGuildFee()} gold coins (declarante)<br> 
			{$this->guild_war->GetOpponentFee()} gold coins (oponentes)
		";
		
		$table->AddField("Pagamento por derrota");
		$table->AddField($fee);	
		$table->AddRow();	
		
		$module .= $table->Draw();
	}
}

$view = new View();
?>