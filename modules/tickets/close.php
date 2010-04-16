<?
	
	Core::extractPost();
	$view = $_GET["id"];
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	$account_id = $account->getId();
	
	$ticket  = new Tickets();

	$query = $db->query("SELECT * FROM wb_tickets WHERE id = '{$_GET["id"]}' ORDER by send_date DESC");
	
	$ticket->load($_GET["id"]);
	
	if($account->getGroup() > 4){
		if($query->numRows() == 0)
		{
			$error = "Pagina n�o encontrada.";
		}
		else
		{
			$_account 	= $account->getId();
			$_player	= $account->getHighCharacter();
			 
			$ticket->changeState($view, 1);
			
			$success = "O Ticket {$ticket->getTitle()} foi fechado com sucesso! <p>Atenciosamente,<br>Equipe Ultraxsoft. </p></ br><a href='?ref=tickets.view&id={$view}'>Voltar</a>";
		}


		if($success)	
		{
			Core::sendMessageBox($boxMessage['SUCCESS'], $success);

		}
	}
	
?>	
