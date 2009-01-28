<?
class Contribute extends MySQL
{
	private $db, $data = array();

	function __construct()
	{
		$this->db = new MySQL();
		$this->db->connect(DB_ULTRAXSOFT_HOST, DB_ULTRAXSOFT_USER, DB_ULTRAXSOFT_PASS, DB_ULTRAXSOFT_SCHEMA);
	}	
	
	function getOrdersListByAccount($account_id)
	{
		$query = $this->db->query("SELECT id FROM orders WHERE target_account = '".$account_id."' and status < 3 ORDER BY generated_in DESC");
		
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
		if($fields)
			$query = $this->db->query("SELECT id, $fields FROM orders WHERE id = '".$id."'");
		else
			$query = $this->db->query("SELECT id FROM orders WHERE id = '".$id."'");		
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			$this->data['id'] = $fetch->id;	
					
			if($fields)	
			{	
				$e = explode(", ", $fields);
				foreach($e as $field)
				{
					$this->data[$field] = $fetch->$field;
				}
			}

			return true;	
		}
		else
		{
			return false;
		}			
	}	
	
	function getNewOrderNumber()
	{
		$query = $this->db->query("SELECT id FROM orders");
		
		$usedOrders = array();
		
		if($query->numRows() != 0)
		{		
			foreach($query->fetchArray() as $value)
			{
				$usedOrders[] = $value;
			}
		}

		global $strings;
		$orderNumber = $strings->randKey(6, 3, "upper+number");
		$success = false;
		
		for($i = 0; $i < 10; $i++)
		{
			if(!in_array($orderNumber, $usedOrders))
			{
				$success = true;
				break;
			}	
				
			$orderNumber = $strings->randKey(12, 1, "upper+number");
		}
		
		if($success)
		{
			$this->data['id'] = $orderNumber;
			return $this->data['id'];
		}
		else
			return false;
	}
	
	function save()
	{
		$i = 0;
	
		$query = $this->db->query("SELECT id FROM orders WHERE id = '".$this->data['id']."'");
		
		//update
		if($query->numRows() == 1)
		{
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$update .= "".$field." = '".$value."'";
				}
				else
				{
					$update .= "".$field." = '".$value."', ";
				}			
			}
			
			$this->db->query("UPDATE orders SET $update WHERE id = '".$this->data['id']."'");
		}
		//new account
		elseif($query->numRows() == 0)
		{
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$insert_fields .= "".$field."";
					$insert_values .= "'".$value."'";
				}
				else
				{
					$insert_fields .= "".$field.", ";
					$insert_values .= "'".$value."', ";
				}			
			}

			$this->db->query("INSERT INTO orders ($insert_fields) values($insert_values)");			
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
	
	function importPayments()
	{
		$query = $this->db->query("SELECT * FROM siteo.payments");
		
		while($fetch = $query->fetch())
		{
			if($fetch['period'] == "30" or $fetch['period'] == "60" or $fetch['period'] == "90" or $fetch['period'] == "180" or $fetch['period'] == "360")
			{
				if(strlower($fetch->server) == "tenerian")
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
	}
	
	function erease()
	{
		$this->data = array();
	}
	
	function sendUrl()
	{
		global $_contribution;
	
		if($this->data['type'] == "PayPal")
		{
			$price = explode(" ", $_contribution[$this->data['type']][$this->data['period']]);
			$price_coin = $price[0];
			$price_value = $price[1];
		
			return '
				<form action="'.CONTRIBUTE_PAYPALURL.'" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="'.CONTRIBUTE_EMAILADMIN.'">
				<input type="hidden" name="no_shipping" value="0">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="'.$price_coin.'">
				<input type="hidden" name="item_name" value="Contribuição para '.$this->data['period'].' dias de Conta Premium.">
				<input type="hidden" name="amount" value="'.$price_value.'">
				<input type="hidden" name="on0" value="REF#'.$this->data['id'].'">
				
				<input type="submit" value="Finalizar" />
				</form>
			';	
		}
		elseif($this->data['type'] == "PagSeguro")
		{
			$price = explode(" ", $_contribution[$this->data['type']][$this->data['period']]);
			$price_value = str_replace(",", ".", $price[1]);			
			
			return '
				<form target="pagseguro" action="'.CONTRIBUTE_PAGSEGUROURL.'" method="post">
				<input type="hidden" name="email_cobranca" value="'.CONTRIBUTE_EMAILADMIN.'">
				<input type="hidden" name="tipo" value="CP">
				<input type="hidden" name="moeda" value="BRL">
				<input type="hidden" name="item_id_1" value="1">
				<input type="hidden" name="item_descr_1" value="Contribuição para '.$this->data['period'].' dias de Conta Premium.">
				<input type="hidden" name="item_quant_1" value="1">
				<input type="hidden" name="item_valor_1" value="'.$price_value.'">
				<input type="hidden" name="item_frete_1" value="000">
				<input type="hidden" name="ref_transacao" value="REF#'.$this->data['id'].'">
				
				<input type="submit" value="Finalizar" />	
				</form>
			';	
		}		
	}
}
?>