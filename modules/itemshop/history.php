<?php
class View
{
	//html fields 
	private $_log_table, $_character, $_daysago;
	
	//variables
	private $_message, $playerDaysAgo = array("7", "15", "30", "60", "90");	
	
	//custom variables
	private $loggedAcc, $isAdmin = false;	
	
	function View()
	{				
		if($_SESSION['login'])
		{
			$this->loggedAcc = new Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			if($this->loggedAcc->getGroup() == GROUP_ADMINISTRATOR)
			{
				$this->isAdmin = true;
			}			
		}
		else
		{
			return false;
		}		
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;
		}	
		
		if($_POST)
		{
			if(!$this->Post())
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			}
		}			
		
		if($this->isAdmin)
		{
			$this->DrawAdmin();
			return true;	
		}
		
		$this->Draw();
		return true;
	}
	
	function Prepare()
	{				
		if($this->isAdmin)
		{
			$this->_character = new HTML_Input();
			$this->_character->SetName("character_name");
			
			$this->_daysago = new HTML_Input();
			$this->_daysago->SetName("days_ago");
			
			return true;
		}
		
		$this->_daysago = new HTML_SelectBox();
		$this->_daysago->SetName("days_ago");
		
		foreach($this->playerDaysAgo as $key => $days)
		{
			$this->_daysago->AddOption("{$days} dias", $days);
		}
		
		$this->buildTable($this->loggedAcc->getItemShopPurchasesQuery($this->playerDaysAgo[0]));
		return true;
	}
	
	function getPurchases($daysago = null)
	{
		if($daysago)
		{
			$limit = "WHERE `log`.`date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * {$daysago})";
		}
		
		$query = Core::$DB->query("
		SELECT 
			`log`.`date`,
			`players`.`name` as `player_name`,
			`shop`.`name`,
			`shop`.`price`,
			`use`.`date` as `use_date`,
			`player_use`.`name` as `player_use`
		FROM 
			`".Tools::getSiteTable("itemshop_log")."` `log` 
		LEFT JOIN
			`".Tools::getSiteTable("itemshop")."` `shop`
		ON
			`shop`.`id` = `log`.`shop_id`
		LEFT JOIN
			`players`
		ON
			`players`.`id` = `log`.`player_id`
		LEFT JOIN
			`".Tools::getSiteTable("itemshop_use_log")."` `use`
		ON
			`use`.`log_id` = `log`.`id`
		LEFT JOIN
			`players` `player_use`
		ON
			`player_use`.`id` = `use`.`player_id`
		{$limit}
		ORDER BY 
			`date` DESC");
		
		$query instanceof Query;
		return $query;			
	}
	
	function buildTable($query)
	{
		$query instanceof Query;
		
		$this->_log_table = new HTML_Table();
		$this->_log_table->AddField("Historico Item Shop");
		$this->_log_table->AddRow();	
		
		$this->_log_table->AddField("<b>Personagem</b>", "30%");
		$this->_log_table->AddField("<b>Item</b>", "35%");
		$this->_log_table->AddField("<b>Custo</b>", "10%");
		$this->_log_table->AddField("<b>Data</b>");
		$this->_log_table->AddRow();			
		
		while($row = $query->fetchAssocArray())
		{				
			$this->_log_table->AddField("<a href='?ref=character.view&name={$row["player_name"]}'>{$row["player_name"]}</a>");
			
			$item = $row["name"];
			
			if($row["player_use"])
			{
				$item .= " usado por:
				<br><a href='?ref=character.view&name={$row["player_use"]}'>{$row["player_use"]}</a><br> em ".Core::formatDate($row["use_date"])."";
			}
			
			$this->_log_table->AddField($item);
			$this->_log_table->AddField($row["price"]);
			$this->_log_table->AddField(Core::formatDate($row["date"]));
			$this->_log_table->AddRow();	
		}		
	}
	
	function Post()
	{
		if($this->isAdmin)
			return $this->PostAdmin();
			
		$daysago = $this->_daysago->GetPost();
		if(!in_array($daysago, $this->playerDaysAgo))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;			
		}
		
		$this->buildTable($this->loggedAcc->getItemShopPurchasesQuery($daysago));
		return true;
	}
	
	function PostAdmin()
	{
		$tmp_char = $this->_character->GetPost();
		
		if(!$tmp_char)
		{
			$this->_message = Lang::Message(LMSG_FILL_FORM);
			return false;
		}
		
		$daysago = $this->_daysago->GetPost();
		
		if($tmp_char != "*")
		{
			$character = new Character();
					
			if(!$character->loadByName($tmp_char))
			{
				$this->_message = Lang::Message(LMSG_REPORT);
				return false;
			}
			
			$tmp_account = new Account();
			$tmp_account->load($character->getAccountId());
			
			$query = $tmp_account->getItemShopPurchasesQuery($daysago);
		}
		else
			$query = $this->getPurchases($daysago | null);
			
		$this->buildTable($query);		
		return true;		
	}	
	
	function Draw()
	{
		global $module;		
				
		$module .= "		
		<fieldset>

			<p>Nesta pagina você pode visualizar os itens adquiridos em nosso shop para algum personagem em sua conta.</p>			

			<form action='{$_SERVER['REQUEST_URI']}' method='post'>
				<fieldset>
					<p>
						<label> Limite de Dias<br>
							{$this->_daysago->Draw()}
						</label>
					</p>		

					<p>
						<input class='button' type='submit' value='Enviar' />
					</p>					
				</fieldset>
			</form>
			
			<p id='line'></p>
			
			{$this->_log_table->Draw()}
		</fieldset>";					
	}
	
	function DrawAdmin()
	{
		global $module;		
				
		$infos = "";
		
		if($_POST)
		{
			$target = ($this->_character->GetPost() == "*") ? " de todas as contas" : "da conta do jogador {$this->_character->GetPost()}";
			
			$infos = "
			<p>Exibindo items obtidos nos ultimos {$this->_daysago->GetPost()} dias {$target}.</p>
			{$this->_log_table->Draw()}
			";
		}
		
		$module .= "		
		<fieldset>

			<p>Nesta pagina você pode checar os itens obtidos no shop por qualquer conta de qualquer personagem.</p>					

			<form action='{$_SERVER['REQUEST_URI']}' method='post'>
				<fieldset>
					<p>
						<label> Personagem<br>
							{$this->_character->Draw()}
						</label>
					</p>

					<p>
						<label> Limite de Dias<br>
							{$this->_daysago->Draw()}
						</label>
					</p>	

					<p>
						<input class='button' type='submit' value='Enviar' />
					</p>					
				</fieldset>	
			</form>
			
			<p id='line'></p>
				
			{$infos}
			
		</fieldset>";				
	}
}

$view = new View();
?>