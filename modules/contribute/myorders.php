<?
$contribute = new \Framework\Contribute();

$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

if(is_array($oders))
{
	foreach($oders as $orderId)
	{
		$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");
		
		$_status_str = t_PaymentStatus::GetString($contribute->status);
		$status = $_status_str;
		
		if($contribute->status == t_PaymentStatus::Confirmed)
			$status .= " <a href='?ref=contribute.accept&id=".$contribute->id."'>[aceitar]</a>";
		
		$premium = \Framework\Contribute::getPremiumInfoByPeriod($contribute->period, $contribute->generated_in);	
		
		$character_name = "";
		
		if(is_numeric($contribute->target))
		{
			$player = new \Framework\Player();
			$player->load($contribute->target);
			$character_name = $player->getName();
		}
		else
			$character_name = $contribute->target;
			
		$orderList .= "
		<tr>
			<td>
				<span style='float: left'>Pedido <b>{$contribute->id}</b></span> <span class='tooglePlus'></span>
				<br />
				<div style='float: left; width: 100%; padding: 0px; margin: 0px; position: relative;'>
					<table cellspacing='0' cellpadding='0'>
						<tr>
							<td width='30%'><b>Nome</b></td> <td>{$contribute->name}</td>
						</tr>
						<tr>
							<td><b>Personagem</b></td> <td>{$character_name}</td>
						</tr>	
						<tr>
							<td><b>Forma de Contribuição</b></td> <td>{$contribute->type}</td>
						</tr>	
						<tr>
							<td><b>Descrição</b></td> <td> {$premium["text"]}</td>
						</tr>
						<tr>
							<td><b>Custo</b></td> <td> {$contribute->cost}</td>
						</tr>	
						<tr>
							<td><b>Pedido Gerado em</b></td> <td> ".\Core\Main::formatDate($contribute->generated_in)."</td>
						</tr>																									
						<tr>	
							<td><b>Estado Atual</b></td> <td> {$status}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>		
		";		
	}
	
	$module .= "
	<p>
		<table cellspacing='0' cellpadding='0' class='dropdowntable'>
		
			<tr>
				<th colspan='3'>Minhas Contribuições</th>
			</tr>
						
			$orderList
			
		</table>
	</p>
	";
}	
else
{
	\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_HAS_NO_ORDERS));
}
?>