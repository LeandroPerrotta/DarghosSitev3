<?
	
	$view = $_GET["id"];
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	$account_id = $account->getId();
	
	$ticket  = new Tickets();	
	
	$query = $db->query("SELECT * FROM wb_tickets WHERE account = '{$account->getId()}' AND id = '{$_GET["id"]}' ORDER by send_date DESC");
	
	$ticket->load($_GET["id"]);
	
	if(!$_POST['another_ticket'])
	{
		
	}
	else
	{
		$_account 	= $account->getId();
		$_player	= $account->getHighCharacter();
		$_main		= Strings::SQLInjection($_POST['another_ticket']);
		$character 	= new Character();
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
		
		$success = "Voc� adicionou uma nova menssagem ao ticket, aguarde algum membro de suporte responder o mesmo. <p>Atenciosamente,<br>Equipe Ultraxsoft. </p>";
	}


	if($success)	
	{
		Core::sendMessageBox($boxMessage['SUCCESS'], $success);

	}
	
	
	if($query->numRows() != 0)
	{
		
			
		$module .= "
			<p align='center'> 
				<a href='?ref=tickets.view&id={$view}'>Atualizar Ticket</a>
			</p>
		";
		
		$ticketTypeName = Tools::GetTicketTypeName($ticket->getType());
		$ticketAttachment = ($ticket->getAttachment() ? $ticket->getAttachment() : "Nenhum");			
			
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table' width='100%'>
			
			<tr>
				<th>&nbsp;</th> <th>Ticket ID [{$ticket->getId()}]</th>
			</tr>	
		
			<tr>
				<td width='15%'><b>Titulo</b></td> <td>{$ticket->getTitle()}</td>
			</tr>	
			
			<tr>
				<td width='15%'><b>Tipo</b></td> <td>{$ticketTypeName}</td>
			</tr>		

			<tr>
				<td width='15%'><b>Ref�rencia</b></td> <td>{$ticketAttachment}</td>
			</tr>					
			
			<tr>
				<td width='15%'><b>Enviado:</b></td><td width='85%'>{Core::formatDate($ticket->getSendDate())}</td>
			</tr>
			
			<tr height='50px'>
				<td width='15%'><b>Conte�do</b></td> <td>".nl2br(stripslashes($ticket->getQuestion()))."</td>
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
					<th> Enviado em  {Core::formatDate($fetch->send_date)} por: {$fetch->by_name}</th>
				</tr>
				
				<tr>
					<td height='50px'>".nl2br(stripslashes($fetch->text))." </td>
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
		Core::sendMessageBox("Erro", "Pagina n�o encontrada.");
	}
	
	



	
?>	
