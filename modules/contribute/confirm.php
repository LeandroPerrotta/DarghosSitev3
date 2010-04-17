<?
if($_SESSION['contribute'])
{
	$contribute = new Contribute();
	$orderNumber = $contribute->getNewOrderNumber();
	
	$character = new Character();
	$character->loadByName($_SESSION['contribute'][2]);
	$target_account = $character->get("account_id");
	
	if(!$orderNumber)
	{
		$error = Lang::Message(LMSG_CONTR_ORDER_NUMBER_DUPLICATED);
	}
	
	$contribute->set("name", $_SESSION['contribute'][0]);
	$contribute->set("email", $_SESSION['contribute'][1]);
	$contribute->set("target", $_SESSION['contribute'][2]);
	$contribute->set("type", $_SESSION['contribute'][3]);
	$contribute->set("period", $_SESSION['contribute'][4]);
	$contribute->set("cost", $_contribution[$_SESSION['contribute'][3]][$_SESSION['contribute'][4]]);
	$contribute->set("server", SERVER_ID);
	$contribute->set("generated_by", $_SESSION['login'][0]);
	$contribute->set("generated_in", time());
	$contribute->set("target_account", $target_account);
	$contribute->set("email_vendor", $_contribution['emailadmin']);
	
	$contribute->save();

	$module = Lang::Message(LMSG_CONTR_ORDER_CREATED, $_SESSION['contribute'][3], $orderNumber, $contribute->sendUrl());
		
	unset($_SESSION['contribute']);
}
?>