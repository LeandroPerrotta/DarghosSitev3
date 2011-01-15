<?
$contribute = new Contribute();

$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

if(is_array($oders))
{
	foreach($oders as $orderId)
	{
		$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");
		$status = $_contribution['status'][$contribute->get("status")];
		
		if($contribute->get("status") == 1)
			$status = $_contribution['status'][$contribute->get("status")].". <a href='?ref=contribute.accept&id=".$contribute->get("id")."'>[aceitar]</a>";
		
		$contrStr = "Contribuição de {$contribute->get("period")} dias de Conta Premium";	
			
		$promocaoStart = mktime("0", "0", "0", "12", "14", "2010");
		$promocaoEnd = mktime("0", "0", "0", "1", "15", "2011");
		
		if($contribute->get("period") > 30 && $contribute->get("generated_in") >= $promocaoStart && $contribute->get("generated_in") < $promocaoEnd)
		{
			$contrStr = "Contribuição de <span class='cortado'>{$contribute->get("period")}</span> <span class='promocao'>".($contribute->get("period") * 2)."</span> dias de Conta Premium";
		}	
			
		$orderList .= "
		<tr>
			<td>
				<span style='float: left'>Pedido <b>{$contribute->get("id")}</b></span> <span class='tooglePlus'></span>
				<br />
				<div style='float: left; width: 100%; padding: 0px; margin: 0px; position: relative;'>
					<table cellspacing='0' cellpadding='0'>
						<tr>
							<td width='30%'><b>Nome</b></td> <td>{$contribute->get("name")}</td>
						</tr>
						<tr>
							<td><b>Personagem</b></td> <td>{$contribute->get("target")}</td>
						</tr>	
						<tr>
							<td><b>Forma de Contribuição</b></td> <td>{$contribute->get("type")}</td>
						</tr>	
						<tr>
							<td><b>Periodo</b></td> <td> {$contrStr}</td>
						</tr>
						<tr>
							<td><b>Custo</b></td> <td> {$contribute->get("cost")}</td>
						</tr>	
						<tr>
							<td><b>Pedido Gerado em</b></td> <td> ".Core::formatDate($contribute->get("generated_in"))."</td>
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
	Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_ACCOUNT_HAS_NO_ORDERS));
}
?>