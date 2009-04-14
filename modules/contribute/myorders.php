<?
$contribute = $core->loadClass("Contribute");

$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

if(is_array($oders))
{
	foreach($oders as $orderId)
	{
		$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");
		$status = $_contribution['status'][$contribute->get("status")];
		
		if($contribute->get("status") == 1)
			$status = $_contribution['status'][$contribute->get("status")].". <a href='?ref=contribute.accept&id=".$contribute->get("id")."'>[aceitar]</a>";
		
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
							<td><b>Periodo</b></td> <td> Contribuição de {$contribute->get("period")} dias de Conta Premium.</td>
						</tr>
						<tr>
							<td><b>Custo</b></td> <td> {$contribute->get("cost")}</td>
						</tr>	
						<tr>
							<td><b>Pedido Gerado em</b></td> <td> {$core->formatDate($contribute->get("generated_in"))}</td>
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
	$module .= 'Não existe nenhum pedido gerado por sua conta.';
}
?>