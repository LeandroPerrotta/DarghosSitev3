<?
$contribute = new \Framework\Contribute();

$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

if(is_array($oders))
{
	foreach($oders as $orderId)
	{
		$contribute->load($orderId, "id, name, account_id, type, balance, generated_in, status");
		
		$_status_str = t_PaymentStatus::GetString($contribute->status);
		$status = $_status_str;
		
		//if($contribute->status == t_PaymentStatus::Confirmed)
		//	$status .= " <a href='?ref=contribute.accept&id=".$contribute->id."'>[aceitar]</a>";
		
			
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
							<td><b>Forma de Contribuição</b></td> <td>{$contribute->type}</td>
						</tr>	
						<tr>
							<td><b>Saldo adicionado</b></td> <td>R$ ".number_format($contribute->balance / 100, 2)."</td>
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
				<th colspan='3'>Minhas adições de saldo</th>
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