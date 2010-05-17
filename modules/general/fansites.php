<?php
class View
{
	private $_name, $_comment, $_url, $_logo, $_owner;

	private $_canSee = false;
	
	function View()
	{	
		if($_SESSION['login'])
		{
			$loggedAcc = new Account();
			$loggedAcc->load($_SESSION['login'][0]);	

			if($loggedAcc->getGroup() >= 5)
			{
				$this->_canSee = true;
				
				$this->_name = new HTML_Input();
				$this->_name->SetName("name");
						
				$this->_comment = new HTML_Input();	
				$this->_comment->SetName("comment");
				$this->_comment->IsTextArea();	
		
				$this->_url = new HTML_Input();	
				$this->_url->SetName("url");
			
				$this->_logo = new HTML_Input();
				$this->_logo->SetName("logo");				
			
				$this->_owner = new HTML_Input();
				$this->_owner->SetName("owner");				
				
				if($_POST)
					$this->Post();
				
				if($_GET["value"])
				{
					$this->EditPage();
					return;
				}
			}
		}
		
		$this->Draw();	
	}
	
	function Post()
	{
		global $module;
		
		$id = $_GET["value"];
		
		$owner = new Character();
		$owner->loadByName($this->_owner->GetPost());			
		
		if($id > 0)
		{			
			Core::$DB->query("UPDATE ".DB_WEBSITE_PREFIX."fansites SET 
				`name` = '{$this->_name->GetPost()}',
				`comment` = '{$this->_comment->GetPost()}',
				`url` = '{$this->_url->GetPost()}',
				`logo` = '{$this->_logo->GetPost()}',
				`player_id` = '{$owner->getId()}' WHERE `id` = '{$id}'");
		}
		else
		{
			Core::$DB->query("INSERT INTO ".DB_WEBSITE_PREFIX."fansites (`name`, `comment`, `url`, `logo`, `added_in`, `player_id`) 
			values ('{$this->_name->GetPost()}', '{$this->_comment->GetPost()}', '{$this->_url->GetPost()}', '{$this->_logo->GetPost()}', '".time()."', '{$owner->getId()}')"); 			
		}
		
		Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), "Mudanças efetuadas com sucesso!");
	}
	
	function EditPage()
	{
		global $module;
		
		$id = $_GET["value"];
		
		if($id > 0)
		{
			$query = Core::$DB->query("SELECT `name`, `comment`, `url`, `logo`, `added_in`, `player_id` FROM ".DB_WEBSITE_PREFIX."fansites WHERE `id` = '{$id}'");
			
			if($query->numRows() != 1)
			{
				return;	
			}
			
			$fetch = $query->fetch();
			
			$owner = new Character();
			$owner->load($fetch->player_id);
			
			$this->_name->SetValue($fetch->name);
			$this->_comment->SetValue($fetch->comment);
			$this->_url->SetValue($fetch->url);
			$this->_logo->SetValue($fetch->logo);	
			$this->_owner->SetValue($owner->getName());	
		}
		
		$module .= "		
		<form action='' method='post'>
			<fieldset>			
				
				<p>
					<label for='name'>Nome do fansite</label><br />
					{$this->_name->Draw()}
				</p>
				
				<p>
					<label for='comment'>Comentario (max 255 caracteres)</label><br />
					{$this->_comment->Draw()}
				</p>
				
				<p>
					<label for='url'>Endereço para o fansite</label><br />
					{$this->_url->Draw()}
				</p>
				
				<p>
					<label for='logo'>Logo para o fansite</label><br />
					{$this->_logo->Draw()}
				</p>
				
				<p>
					<label for='owner'>Personagem dono</label><br />
					{$this->_owner->Draw()}
				</p>
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>				
			</fieldset>
		</form>";		
	}
	
	function Draw()
	{
		global $module;

		if($this->_canSee)
		{		
			$module .= "<p><a href='?ref=general.fansites&value=new'>[Inserir novo fansite]</a></p>";
		}
		
		$query = Core::$DB->query("SELECT `id`, `name`, `comment`, `url`, `logo`, `added_in`, `player_id` FROM ".DB_WEBSITE_PREFIX."fansites ORDER BY `added_in`");
		
		$table = new HTML_Table();
		$table->AddDataRow("Lista de Fansites oficiais");
		
		$string = "Esta é a lista de fansites oficialmente reconhecidos pelo Darghos. Atraves deste(s) fansites é possivel participar de comunidades ativas de fans de nosso trabalho, participar de artigos, obter informações sobre o Darghos e tudo mais.<br>
		<br>
		<span style='color: red;'>Observações:</span> Nós não nos responsabilizamos por nenhum destes fansites, sendo eles completamente independentes do Darghos.";
		$style = "font-weight: bold;";
		
		$table->AddField($string, null, $style, 2);
		$table->AddRow();		
		
		if($query->numRows() != 0)
		{
			for($i = 0; $i < $query->numRows(); ++$i)
			{
				$fetch = $query->fetch();

				$owner = new Character();
				$owner->load($fetch->player_id);				
				
				$table->AddField("<img src='{$fetch->logo}' height='100' width='150' />", 30);
				
				$string = "";
				
				if($this->_canSee)
				{
					$string .= "<p><a href='?ref=general.fansites&value={$fetch->id}'>[Editar]</a></p>";
				}
				
				$string .= "
					<a href='{$fetch->url}'>{$fetch->name}</a>
					<p>{$fetch->comment}<p>
					<p>Fansite desde: ".Core::formatDate($fetch->added_in)."</p>
					<p>Contato: <a href='?ref=character.view&name={$owner->getName()}'>{$owner->getName()}</a></p>
				";
				
				$style = "vertical-align: middle; height: 50px;";
				
				$table->AddField($string, null, $style);
				
				$table->AddRow();		
			}
		}
		else
		{
			$table->AddField("Nós ainda não possuimos nenhum fansite :(");
			$table->AddRow();
		}
		
		$module .= $table->Draw();	
	}
}

$view = new View();
?>