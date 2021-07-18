<?
use \Core\Consts;
if($_SESSION['contribute'])
{
	$contribute = new \Framework\Contribute();
	$orderNumber = $contribute->getNewOrderNumber();
	
	$account = \Framework\Account::loadLogged();
	
	if(!$orderNumber)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ORDER_NUMBER_DUPLICATED);
	}
	
	$contribute->name = $_SESSION['contribute']["order_name"];
	$contribute->email = $account->getEmail();
	$contribute->account_id = $account->getId();
	$contribute->type = \Framework\Contribute::TYPE_PAGSEGURO;
	$contribute->balance = $_SESSION['contribute']["add_balance"];
	$contribute->server = \Core\Configs::Get(\Core\Configs::eConf()->SERVER_ID);
	$contribute->generated_in = time();
	$contribute->email_vendor = Consts::PAGSEGURO_EMAIL;
	
	$contribute->save();

	$module = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ORDER_CREATED, $orderNumber, $contribute->sendUrl());
		
	unset($_SESSION['contribute']);
}
?>