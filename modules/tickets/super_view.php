<?
	
	include("classes/tickets.php");

	$view = $_GET["id"];
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	$account_id = $account->getId();

	if($account->getGroup() >= 4)
	{			
		$ticket  = new Tickets();
				
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
			
			$success = "Voc� enviou uma resposta ao ticket {$ticket->getTitle()}.";
		}
	
		if($success)	
		{
			Core::sendMessageBox($boxMessage['SUCCESS'], $success);
	
		}
		
		if($_GET["id"])
		{	
			$query = $db->query("SELECT * FROM wb_tickets WHERE id = '{$_GET["id"]}' ORDER by send_date DESC");
			
			$ticket->load($_GET["id"]);		
				
			if($query->numRows() != 0)
			{
				
				if($ticket->getClosed() != 1)
				{
					$str = "<a href='?ref=tickets.close&id={$view}'>Fechar Ticket</a>";
				}
				else 
				{
					$str = "<a href='?ref=tickets.open&id={$view}'>Abrir Ticket</a>";
				}
					
				$module .= "
					<p align='center'> 
						<font color='red'>Aten��o:</font> Voc� est� no modo de visualiza��o para membros de suporte, voc� poder� responder o ticket. Por favor use um portugu�s N�o-Slash nas respostas.
					</p>
					<p align='center'> 
						<a href='?ref=tickets.super_view&id={$view}'>Atualizar Ticket</a> | {$str}
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
							<th> Enviado em  {Core::formatDate($fetch->send_date)} por: {$fetch->by_name} [<a href='?ref=tickets.super_view&kill_reply={$fetch->id}'>Excluir</a>]</th>
						</tr>
						
						<tr>
							<td height='50px'>".nl2br(stripslashes($fetch->text))." </td>
						</tr>
					";
				}
					
				$module .= "
					</table>
				";
			if($ticket->getClosed() == 0)
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
		}
	
		if($_GET["kill_reply"])
		{
			$id2 = $_GET["kill_reply"];
			
			$ticket->killReply($id2);
			Core::redirect("index.php?ref=tickets.super_view&id={$view}");	
			
		}	
	}
	
?>	
