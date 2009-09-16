<?
	
	$core->extractPost();
	$view = $_GET["id"];
	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);
	$account_id = $account->getId();
	
	$ticket  = $core->loadClass("Tickets");
	$string  = $core->loadClass("Strings");

	
	
	$query = $db->query("SELECT * FROM wb_tickets WHERE account = '{$account->getId()}' AND id = '{$_GET["id"]}' ORDER by send_date DESC");
	
	$ticket->load($_GET["id"]);
	
	if(!$_POST['another_ticket'])
	{
		
	}
	else
	{
		$_account 	= $account->getId();
		$_player	= $account->getHighCharacter();
		$_main		= $string->SQLInjection($_POST['another_ticket']);
		$character 	= $core->loadClass("Character");
		$character->loadByName($_player);
		
		if($character->getGroup() >= 4)
		{
			$_name = "Darghos Suporte";
			$is_new	= 0;
		}
		else 
		{
			$_name = $_player;
			$is_new = 1;
		}
		
		$ticket->sendAnotherReply(0, $view, $_main, $_name, time());
		$ticket->setUpdate($view, $is_new);
		
		$success = "Você adicionou uma nova menssagem ao ticket, aguarde algum membro de suporte responder o mesmo. <p>Atenciosamente,<br>Equipe Ultraxsoft. </p>";
	}


	if($success)	
	{
		$core->sendMessageBox($boxMessage['SUCCESS'], $success);

	}
	
	
	if($query->numRows() != 0)
	{
		
			
		$module .= "
			<p align='center'> 
				<a href='?ref=tickets.view&id={$view}'>Atualizar Ticket</a>
			</p>
		";
		
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table' width='100%'>
			
			<tr>
				<th>&nbsp;</th> <th>Ticket ID [{$ticket->getId()}]</th>
			</tr>	
		
			<tr>
				<td width='15%'><b>Titulo</b></td> <td>{$ticket->getTitle()}</td>
			</tr>	
			
			<tr>
				<td width='15%'><b>Enviado:</b></td><td width='85%'>{$core->formatDate($ticket->getSendDate())}</td>
			</tr>
			
			<tr height='50px'>
				<td width='15%'><b>Conteúdo</b></td> <td>{$ticket->getQuestion()}</td>
			</tr>
			
			<tr>
				<td widht='15%'><b>Estado:</b></td> ".($ticket->getClosed() == 1 ? "<td><font color='red'><b>Fechado</b></font></td>" : "<td><font color='green'><b>Aberto</b></font></td>")."
			</tr>
		</table>
		";
		
		$query_resp = $db->query("SELECT id, ticket_id, text, by_name, send_date FROM wb_tickets_answers WHERE ticket_id = $view ORDER by send_date ASC");
		
		$module .= "
			<table cellspacing='0' cellpadding='0' id='table' width='100%'>

		";

		while($fetch = $query_resp->fetch())
		{
			
			
			$module .= "
				<tr>
					<th> Enviado em  {$core->formatDate($fetch->send_date)} por: {$fetch->by_name}</th>
				</tr>
				
				<tr>
					<td height='50px'>{$fetch->text} </td>
				</tr>
			";
		}
			
		$module .= "
			</table>
		";
	if($ticket->getClosed() != 1)
	{	
		$module .= "
		<p>
		<form action='' method='post'>
			<fieldset>	
				<p>
					<label for='ticket_main'>Envie uma nova menssagem:</label><br />
					<textarea id='another_ticket' name='another_ticket' cols='85' rows='7' type='text' value=''/></textarea>
				</p>
				

			<p>
				<input class='button' type='submit' value='{$buttons['SUBMIT']}' />
			</p>

			</fieldset>
		</form>
	
		";
	}
	}
	else 
	{
		$core->sendMessageBox("Erro", "Pagina não encontrada.");
	}
	
	



	
?>	
