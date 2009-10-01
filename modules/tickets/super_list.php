<?
	include("classes/tickets.php");	

	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);

	if($account->getGroup() >= 4){
	
		$string  = $core->loadClass("Strings");
		$get 	 = $_GET["state"];
		
		if($get == "closeds")
		{
			$query = $db->query("SELECT id FROM wb_tickets WHERE closed = 1 ORDER by send_date DESC");
			$central = "<p><a href='?ref=tickets.super_list&state=opens'>Ver tickets Abertos</a><p>Tickets fechados.</p>";
		}
		else 
		{
			$query = $db->query("SELECT id FROM wb_tickets WHERE closed = 0 ORDER by send_date DESC");
			$central = "<p><a href='?ref=tickets.super_list&state=closeds'>Ver tickets Fechados</a></p><p>Tickets aguardando resposta. Use um português adequado para responder o mesmo.</p>";
		}
		$module .=	"
			
		{$central}
	
		";
	
	
	
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th width='45%'>Titulo</th> <th width='20%'>Enviado</th> <th width='15%'>Categoria</th> <th>Autor</th>
			</tr>	
		";	
		
		while($fetch = $query->fetch())
		{
			$ticket = new Tickets();
			$ticket->load($fetch->id);
			
			$type = Tools::GetTicketTypeName($ticket->getType());		
			
			if(strlen($ticket->getTitle()) > 32)
			{
				$formated = substr($ticket->getTitle(),0,31);
				$titulo = $formated."...";
			}
			else 
			{
				$titulo = $ticket->getTitle();
			}
			
			$player_id = $ticket->getPlayerId();
			
			//verificamos se existe um personagem definido para o ticket
			if($player_id != 0)
			{
				$player = new Character();
				
				$player->load($player_id);
				$player_name = $player->getName();
			}
			//não há um personagem, então usamos o personagem com nivel mais alto da conta
			else
			{
				$_ticketAccount = new Account();
				$_ticketAccount->load($ticket->getAccount());
				
				$player_name = $_ticketAccount->getHighCharacter();
			}
			
			$url = ($ticket->getLastUpdate() == 1 ? "<a href='?ref=tickets.super_view&id={$ticket->getID()}'>{$titulo}</a>" : "<a style='font-weight: normal;' href='?ref=tickets.super_view&id={$ticket->getID()}'>{$titulo}</a>");
			
			$module .= "
				<tr>
					<td class='name'>{$url}</td><td>{$core->formatDate($ticket->getSendDate())}</td><td>$type</td><td>{$player_name}</td>
				</tr>
			";
		}
	
		$module .= "
		</table>
		";
	}
	
?>	
