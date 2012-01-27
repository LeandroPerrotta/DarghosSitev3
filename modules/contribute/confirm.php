<?
use \Core\Consts;
if($_SESSION['contribute'])
{
	$contribute = new \Framework\Contribute();
	$orderNumber = $contribute->getNewOrderNumber();
	
	$player = new \Framework\Player();
	$player->loadByName($_SESSION['contribute']["order_target"]);
	$target_account = $player->get("account_id");
	
	if(!$orderNumber)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ORDER_NUMBER_DUPLICATED);
	}
	
	$premium = \Framework\Contribute::getPremiumInfoByPeriod($_SESSION['contribute']["order_period"]);
	
	$contribute->set("name", $_SESSION['contribute']["order_name"]);
	$contribute->set("email", $_SESSION['contribute']["order_email"]);
	$contribute->set("target", $player->getId());
	$contribute->set("type", \Framework\Contribute::TYPE_PAGSEGURO);
	$contribute->set("period", $_SESSION['contribute']["order_period"]);
	$contribute->set("cost", \Framework\Contribute::formatCost($premium["cost"]));
	$contribute->set("server", 1);
	$contribute->set("generated_by", $_SESSION['login'][0]);
	$contribute->set("generated_in", time());
	$contribute->set("target_account", $target_account);
	$contribute->set("email_vendor", Consts::PAGSEGURO_EMAIL);
	
	$contribute->save();

	$module = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ORDER_CREATED, $orderNumber, $contribute->sendUrl());
		
	unset($_SESSION['contribute']);
}
?>