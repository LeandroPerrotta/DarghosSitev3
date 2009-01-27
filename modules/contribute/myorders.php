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
		
		$module .= '
		<ul id="pagelist">
			<p>Pedido Numero: '.$contribute->get("id").'</p>
			<li><b>Nome: </b> '.$contribute->get("name").'.</li>
			<li><b>Personagem: </b> '.$contribute->get("target").'.</li>
			<li><b>Forma de Contribuição: </b> '.$contribute->get("type").'.</li>
			<li><b>Periodo: </b> Contribuição de '.$contribute->get("period").' dias de Conta Premium.</li>
			<li><b>Custo: </b> '.$contribute->get("cost").'.</li>
			<li><b>Pedido Gerado em: </b> '.$core->formatDate($contribute->get("generated_in")).'.</li>
			<li><b>Estado Atual: </b> '.$status.'</li>
			
		</ul>		
			';
	}
}	
else
{
	$module .= 'Não existe nenhum pedido gerado por sua conta.';
}
?>