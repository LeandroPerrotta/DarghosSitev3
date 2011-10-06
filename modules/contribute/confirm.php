<?
if($_SESSION['contribute'])
{
	$contribute = new Contribute();
	$orderNumber = $contribute->getNewOrderNumber();
	
	$character = new Character();
	$character->loadByName($_SESSION['contribute']["order_target"]);
	$target_account = $character->get("account_id");
	
	if(!$orderNumber)
	{
		$error = Lang::Message(LMSG_CONTR_ORDER_NUMBER_DUPLICATED);
	}
	
	$premium = Contribute::getPremiumInfoByPeriod($_SESSION['contribute']["order_period"]);
	
	$contribute->set("name", $_SESSION['contribute']["order_name"]);
	$contribute->set("email", $_SESSION['contribute']["order_email"]);
	$contribute->set("target", $character->getId());
	$contribute->set("type", Contribute::TYPE_PAGSEGURO);
	$contribute->set("period", $_SESSION['contribute']["order_period"]);
	$contribute->set("cost", Contribute::formatCost($premium["cost"]));
	$contribute->set("server", SERVER_ID);
	$contribute->set("generated_by", $_SESSION['login'][0]);
	$contribute->set("generated_in", time());
	$contribute->set("target_account", $target_account);
	$contribute->set("email_vendor", $_contribution['emailadmin']);
	
	$contribute->save();

	$module = Lang::Message(LMSG_CONTR_ORDER_CREATED, $orderNumber, $contribute->sendUrl());
		
	unset($_SESSION['contribute']);
}
?>