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
	
	$contribute->name = $_SESSION['contribute']["order_name"];
	$contribute->email = $_SESSION['contribute']["order_email"];
	$contribute->target = $player->getId();
	$contribute->type = \Framework\Contribute::TYPE_PAGSEGURO;
	$contribute->period = $_SESSION['contribute']["order_period"];
	$contribute->cost = \Framework\Contribute::formatCost($premium["cost"]);
	$contribute->server = \Core\Configs::Get(\Core\Configs::eConf()->SERVER_ID);
	$contribute->generated_by = $_SESSION['login'][0];
	$contribute->generated_in = time();
	$contribute->target_account = $target_account;
	$contribute->email_vendor = Consts::PAGSEGURO_EMAIL;
	
	$contribute->save();

	$module = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ORDER_CREATED, $orderNumber, $contribute->sendUrl());
		
	unset($_SESSION['contribute']);
}
?>