<?
namespace Framework;
use \Core\Consts;
class Contribute extends \Core\MySQL
{		
	static public $premiums = array(
		array("period" => 10, "product" => "Contribuicao para 10 dias de Conta Premium", "text" => "10 dias.", "cost" => 3.50),
		array("period" => 30, "product" => "Contribuicao para 30 dias de Conta Premium", "text" => "30 dias.", "cost" => 9.00),
		array("period" => 60, "product" => "Contribuicao para 60 dias de Conta Premium", "text" => "60 dias (2 meses).", "cost" => 17.00),
		array("period" => 90, "product" => "Contribuicao para 90 dias de Conta Premium", "text" => "90 dias (3 meses).", "cost" => 24.50),
		array("period" => 180, "product" => "Contribuicao para 180 dias de Conta Premium", "text" => "180 dias (6 meses).", "cost" => 45.00)
	);
	
	static public $premiumsPromotions = array(
		array("period" => 180, "product" => "Contribuicao para 180 dias de Conta Premium + Outfit Ticket"
			,"text" => "<span class='promocao'>Esp. Dia das Crianças!</span> 180 dias (6 meses) + 1 Ticket para Yalaharian Outfit!"
			,"cost" => 44.90, "start" => "05/10/2011", "end" => "15/10/2011", "onAccept" => "diadascriancas"),
			
		array("period" => 30, "product" => "Contribuição para 30 dias de Conta Premium + Brinde de Natal"
			,"text" => "<span class='promocao'>Esp. Natal!</span> 30 dias + 1 brinde de natal com items!"
			,"cost" => 7.50 ,"start" => "06/12/2011", "end" => "31/12/2011", "onAccept" => "natal"),
			
		array("period" => 60, "product" => "Contribuição para 60 dias de Conta Premium + Brinde de Natal"
			,"text" => "<span class='promocao'>Esp. Natal!</span> 60 dias (2 meses) + 1 brinde de natal com items!"
			,"cost" => 15.00, "start" => "06/12/2011", "end" => "31/12/2011", "onAccept" => "natal"),	
			
		array("period" => 90, "product" => "Contribuição para 90 dias de Conta Premium + Brinde de Natal"
			,"text" => "<span class='promocao'>Esp. Natal!</span> 90 dias (3 meses) + 1 brinde de natal com items!"
			,"cost" => 22.50,"start" => "06/12/2011", "end" => "31/12/2011", "onAccept" => "natal"),	
			
		array("period" => 180, "product" => "Contribuição para 180 dias de Conta Premium + Brinde de Natal"
			,"text" => "<span class='promocao'>Esp. Natal!</span> 180 dias (6 meses) + 1 brinde de natal com items!"
			,"cost" => 44.90,"start" => "06/12/2011", "end" => "31/12/2011", "onAccept" => "natal")	
	);

	static public $specialOffersNotes = array(
		array("note" => "A promoção do brinde Yalaharian Outfit Ticket é valida apénas para pedidos para conta premium de 180 dias e gerados até 23:59 do dia 15/10."
			,"start" => "05/10/2011", "end" => "15/10/2011")
		,array("note" => "O outfit ticket brinde para o Dia das Criança concede apénas uma unica parte do Outfit ou seus Addon que você não possua, na ordem: Outfit, 1o Addon, 2o Addon." 
			,"start" => "05/10/2011", "end" => "15/10/2011")
		,array("note" => "O outfit ticket será recebido no primeiro login do personagem escolhido acima para receber a Conta Premium após esta liberada e aceita."
			,"start" => "05/10/2011", "end" => "15/10/2011")
		,array("note" => "O brinde de natal é valido para todos os pedidos gerados até as 23:59 do dia 31/12/2011."
			,"start" => "06/12/2011", "end" => "31/12/2011")
		,array("note" => "O brinde será recebido no primeiro login do personagem escolhido acima para receber a Conta Premium após esta liberada e aceita."
			,"start" => "06/12/2011", "end" => "31/12/2011")
		,array("note" => "O brinde concede items como equipamentos, armas, outfit tickets e acessórios de forma aleatoria."
			,"start" => "06/12/2011", "end" => "31/12/2011")
		,array("note" => "Seguindo a tradição natalina para presentes, os presentes somente poderão ser abertos a partir das 00:00 do dia 25/12/2011."
			,"start" => "06/12/2011", "end" => "31/12/2011")
		,array("note" => "Somente os brindes para conta premiums de 180 dias, gerados, pagos e recebidos no jogo até 23:59 do dia 24/12/2011 poderão participar do sorteio do premio especial."
			,"start" => "06/12/2011", "end" => "31/12/2011")
	);
	
	const
		TYPE_PAGSEGURO = "PagSeguro"
		;

	static function natal(Contribute $contribute, &$error)
	{
		$player = new \Framework\Player();
		$player->load($contribute->get("target"));
		
		if($player->getOnline())
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			return false;
		}		
		
		/* 
		 * ID 72 = Presente de Natal Verde (30 dias)
		 * ID 73 = Presente de Natal Azul (60 dias)
		 * ID 74 = Presente de Natal Vermelho (90 dias)
		 * ID 75 = Presente de Natal Grande (180 dias)
		*/
		
		$NATAL_SHOP_ID = false;
		
		switch($contribute->get("period"))
		{
			case 30:
				$NATAL_SHOP_ID = 72;
				break;
				
			case 60:
				$NATAL_SHOP_ID = 73;
				break;
				
			case 90:
				$NATAL_SHOP_ID = 74;
				break;	

			case 180:
				$NATAL_SHOP_ID = 75;
				break;				
		}
		
		if(!$NATAL_SHOP_ID)
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}

		$item = new ItemShop();
		$item->load($NATAL_SHOP_ID);
		
		$item->logItemPurchase($player->getId());
		return true;
	}
	
	static function diadascriancas(Contribute $contribute, &$error)
	{
		$player = new \Framework\Player();
		$player->load($contribute->get("target"));
		
		if($player->getOnline())
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			return false;
		}
		
		$YALAHARIAN_SHOP_ID = 65;
		
		$item = new ItemShop();
		$item->load($YALAHARIAN_SHOP_ID);
		
		$item->logItemPurchase($player->getId());
		return true;
	}
	
	static function formatCost($cost, $toPrint = true)
	{
		$str = "";
		
		if($toPrint)
			$str .= "R$ ";
			
		$str .= ($toPrint) ? number_format($cost, 2, ",", ".") : number_format($cost, 2);
		
		return $str;
	}
	
	static function getPremiumInfoByPeriod($period, $date = NULL)
	{
		if($date == NULL)
			$date = time();
		
		foreach(self::$premiums as $k => $premium)
		{
			if($premium["period"] == $period)
			{
				$promotion = self::getPromotion($period, $date);
				return ($promotion) ? $promotion : $premium;
			}
		}		
		
		return NULL;
	}
	
	static function getPromotion($period, $date)
	{
		foreach(self::$premiumsPromotions as $k => $premium)
		{
			if($premium["period"] == $period)
			{				
				list($start_day, $start_month, $start_year) = explode("/", $premium["start"]);
				list($end_day, $end_month, $end_year) = explode("/", $premium["end"]);
				
				$now = getdate($date);
				
				if($now["mday"] >= $start_day && $now["mon"] >= $start_month && $now["year"] >= $start_year
					&& $now["mday"] <= $end_day && $now["mon"] <= $end_month && $now["year"] <= $end_year)	
				{		
					return $premium;
				}		
			}		
		}	

		return NULL;
	}
	
	static function isValidPeriod($period)
	{
		foreach(self::$premiums as $k => $premium)
		{
			if($premium["period"] == $period)
				return true;
		}
		
		return false;
	}
	
	private	$tableStr;
	private $db, $data = array();	
	
	public	
		$id
		,$name
		,$email
		,$target
		,$type
		,$period
		,$cost
		,$server
		,$generated_by
		,$generated_in
		,$status
		,$lastupdate_in
		,$target_account
		,$auth
		,$email_vendor
		;
	
	function __construct()
	{
		if(\Core\Configs::Get(\Core\Configs::eConf()->SQL_ORDERS_DATABASE))
			$this->tableStr = "`" . \Core\Configs::Get(\Core\Configs::eConf()->SQL_ORDERS_DATABASE) . "`.";
		
		$this->tableStr .= "`".\Core\Tools::getSiteTable("orders")."`";
			
		$this->db = \Core\Main::$DB;
	}	
	
	function getOrdersListByAccount($account_id)
	{
		$query = $this->db->query("SELECT `id` FROM {$this->tableStr} WHERE `target_account` = '".$account_id."' and `status` < 3 and `server` = '".\Core\Configs::Get(\Core\Configs::eConf()->SERVER_ID)."' ORDER BY `generated_in` DESC");
		
		if($query->numRows() != 0)
		{
			$orderList = array();
			
			while($fetch = $query->fetch())
			{
				$orderList[] = $fetch->id;
			}
			
			return $orderList;
		}
		else
			return false;
	}		
	
	function load($id, $fields = null)
	{	
			
		$query = $this->db->query("SELECT `id`, `name`, `email`, `target`, `type`, `period`, `cost`, `server`, `generated_by`, `generated_in`, `status`, `lastupdate_in`, `target_account`, `auth`, `email_vendor` FROM {$this->tableStr} WHERE `id` = '".$id."'");		
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			$this->id = $fetch->id;
			$this->name = $fetch->name;	$this->email = $fetch->email; $this->target = $fetch->target;
			$this->type = $fetch->type;	$this->period = $fetch->period;	$this->cost = $fetch->cost;	
			$this->server = $fetch->server;	$this->generated_by = $fetch->generated_by;	$this->generated_in = $fetch->generated_in;	
			$this->status = $fetch->status;	 $this->lastupdate_in = $fetch->lastupdate_in;	$this->target_account = $fetch->target_account;	
			$this->auth = $fetch->auth; $this->email_vendor = $fetch->email_vendor;	

			return true;	
		}
		else
		{
			return false;
		}			
	}	
	
	function getNewOrderNumber()
	{
		$query = $this->db->query("SELECT `id` FROM {$this->tableStr}");
		
		$usedOrders = array();
		
		if($query->numRows() != 0)
		{		
			foreach($query->fetchArray() as $value)
			{
				$usedOrders[] = $value;
			}
		}

		$orderNumber = \Core\Strings::randKey(6, 3, "upper+number");
		$success = false;
		
		for($i = 0; $i < 10; $i++)
		{
			if(!in_array($orderNumber, $usedOrders))
			{
				$success = true;
				break;
			}	
				
			$orderNumber = \Core\Strings::randKey(12, 1, "upper+number");
		}
		
		if($success)
		{
			$this->id = $orderNumber;
			return $this->id;
		}
		else
			return false;
	}
	
	function save()
	{
		$i = 0;
	
		$query = $this->db->query("SELECT `id` FROM {$this->tableStr} WHERE `id` = '".$this->id."'");
		
		//update
		if($query->numRows() == 1)
		{
			$this->id = $this->id;
			$this->name = $this->name;	$this->email = $this->email; $this->target = $this->target;
			$this->type = $this->type;	$this->period = $this->period;	$this->cost = $this->cost;
			$this->server = $this->server;	$this->generated_by = $this->generated_by;	$this->generated_in = $this->generated_in;
			$this->status = $this->status;	 $this->lastupdate_in = $this->lastupdate_in;	$this->target_account = $this->target_account;
			$this->auth = $this->auth;			
			
			$this->db->query("UPDATE {$this->tableStr} SET 
				`name` = '{$this->name}', `email` = '{$this->email}', `target` = '{$this->target}',
				`type` = '{$this->type}', `period` = '{$this->period}', `cost` = '{$this->cost}', `server` = '{$this->server}',
				`generated_by` = '{$this->generated_by}', `generated_in` = '{$this->generated_in}', `status` = '{$this->status}', `lastupdate_in` = '{$this->lastupdate_in}',
				`target_account` = '{$this->target_account}', `auth` = '{$this->auth}', `email_vendor` = '{$this->email_vendor}'
				WHERE `id` = '".$this->id."'");
		}
		//new account
		elseif($query->numRows() == 0)
		{
			foreach($this->data as $field => $value)
			{
				$i++;

				$insert_fields .= "`{$field}`";
				$insert_values .= "'{$value}'";				
				
				if($i != count($this->data))
				{
					$insert_fields .= ", ";
					$insert_values .= ", ";
				}		
			}

			$this->db->query("INSERT INTO {$this->tableStr} (
				`id`, `name`, `email`, `target`, `type`, `period`, `cost`, `server`, `generated_by`, `generated_in`, `status`,
				`lastupdate_in`, `target_account`, `auth`, `email_vendor`
			) 
			VALUES(
				'{$this->id}', '{$this->name}', '{$this->email}', '{$this->target}', '{$this->type}', '{$this->period}', '{$this->cost}', '{$this->server}', 
				'{$this->generated_by}', '{$this->generated_in}', '{$this->status}', '{$this->lastupdate_in}', '{$this->target_account}', '{$this->auth}', '{$this->email_vendor}'
			)");			
		}		
	}
	
	function set($field, $value)
	{
		$this->data[$field] = $value;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}	
	
	/*function importPayments()
	{
		$query = $this->db->query("SELECT * FROM siteo.payments");
		$periodoquepode = array(30, 60, 90, 180, 360);
		
		while($fetch = $query->fetch())
		{
			if(in_array($fetch->period, $periodoquepode))
			{
				if(strtolower($fetch->server) == "tenerian")
				{
					global $_contribution;
				
					$type = ($fetch->method == 1) ? "PagSeguro" : "PayPal";
					
					if($fetch->status == 0) 
						$status = 1;
					elseif($fetch->status == 1)
						$status = 2;
					elseif($fetch->status == 2)
						$status = 3;
				
					$this->getNewOrderNumber();
					
					$this->set("name", "Desconhecido");
					$this->set("email", "Desconhecido");
					$this->set("target", "Esta conta");
					$this->set("type", $type);
					$this->set("period", $fetch->period);
					$this->set("cost", $_contribution[$type][$fetch->period]);
					$this->set("server", SERVER_ID);
					$this->set("generated_by", 0);
					$this->set("generated_in", $fetch->activation);
					$this->set("target_account", $fetch->account_id);	
					$this->set("status", $status);	
					$this->set("auth",  $fetch->auth);	
					
					$this->save();
					
					$this->erease();
				}				
			}
		}
	}*/
	
	function erease()
	{
		$this->data = array();
	}
	
	function sendUrl()
	{
		global $_contribution;
	
		/*
		if($this->data['type'] == "PayPal")
		{
			$price = explode(" ", $_contribution[$this->data['type']][$this->data['period']]);
			$price_coin = $price[0];
			$price_value = $price[1];
		
			return '
				<form action="'.CONTRIBUTE_PAYPALURL.'" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="'.CONTRIBUTE_PAYPALEMAIL.'">
				<input type="hidden" name="no_shipping" value="0">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="'.$price_coin.'">
				<input type="hidden" name="item_name" value="ContribuiÃ§Ã£o para '.$this->data['period'].' dias de Conta Premium.">
				<input type="hidden" name="amount" value="'.$price_value.'">
				<input type="hidden" name="on0" value="REF#'.$this->data['id'].'">
				
				<input class="button" type="submit" value="Finalizar" />
				</form>
			';	
		}
		elseif($this->data['type'] == "PagSeguro")*/
		if($this->type == "PagSeguro")
		{			
			$premium = self::getPremiumInfoByPeriod($this->period);
			
			$form = '
				<form target="pagseguro" action="'.Consts::URLS_PAGSEGURO.'" method="post">
				<input type="hidden" name="email_cobranca" value="'.Consts::PAGSEGURO_EMAIL.'">
				<input type="hidden" name="tipo" value="CP">
				<input type="hidden" name="moeda" value="BRL">
				<input type="hidden" name="ref_transacao" value="'.$this->id.'">
				<input type="hidden" name="item_id_1" value="1">
				<input type="hidden" name="item_descr_1" value="'.$premium["product"].'. (ref: '.$this->id.')">
				<input type="hidden" name="item_quant_1" value="1">
				<input type="hidden" name="item_valor_1" value="'.self::formatCost($premium["cost"], false).'">
				<input type="hidden" name="item_frete_1" value="000">
				
				<input class="button" type="submit" value="Finalizar" />	
				</form>
			';
			
			return $form;	
		}		
	}
}
?>