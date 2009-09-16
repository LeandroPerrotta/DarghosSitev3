<?
	
	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);

	if($account->getGroup() >= 4){
	
	$ticket  = $core->loadClass("Tickets");
	$string  = $core->loadClass("Strings");
	
	$query = $db->query("SELECT id FROM wb_tickets WHERE closed = 0 AND last_update = 1 ORDER by send_date DESC");
	

	$module .=	"

		<p>
			Tickets aguardando resposta. Use um português adequado para responder o mesmo.
		</p>
	";



	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th width='45%'>Titulo</th> <th width='20%'>Enviado</th> <th width='15%'>Categoria</th> <th>Autor</th>
		</tr>	
	";	
	
while($fetch = $query->fetch())
{
	$ticket = $core->loadClass("Tickets");
	$ticket->load($fetch->id);
	
	if($ticket->getType() == 1)
	{
		$type = "Website";	
	}
	elseif($ticket->getType() == 2)
	{
		$type = "Jogo";
	}
	else
	{
		$type = "Premium";
	}	

	
	if(strlen($ticket->getTitle()) > 32)
	{
		$formated = substr($ticket->getTitle(),0,31);
		$titulo = $formated."...";
	}
	else 
	{
		$titulo = $ticket->getTitle();
	}
	
	$module .= "
		<tr>
			<td class='name'><a href='?ref=tickets.super_view&id={$ticket->getID()}'>{$titulo}</a></td><td>{$core->formatDate($ticket->getSendDate())}</td><td>$type</td><td>{$ticket->getPlayer()}</td>
		</tr>
	";
}

$module .= "
</table>
";
	}
	
?>	
