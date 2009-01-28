<?
class Account
{
	private $db, $data = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}

	function load($id, $fields = null)
	{
		if($fields)
			$query = $this->db->query("SELECT id, $fields FROM accounts WHERE id = '".$id."'");
		else
			$query = $this->db->query("SELECT id FROM accounts WHERE id = '".$id."'");		
		
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
	
	function loadByEmail($email, $fields = null)
	{
		$query = $this->db->query("SELECT id FROM accounts WHERE email = '".$email."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id, $fields);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getNumber()
	{
		$random = rand(100000, 9999999);
		$number = $random;
		
		$exist = array();
	
		$query = $this->db->query("SELECT id FROM accounts");
		
		if($query->numRows() != 0)
		{
			foreach($query->fetchArray() as $value => $account)
			{
				$exist[] = $account;
			}
		}
		
        while(true)
        {
            if( !in_array($number, $exist) )
            {
                break;
            }

            $number++;

            if($number > $max)
            {
                $number = $min;
            }

            if($number == $random)
            {
                return false;
            }
        }
		
		$this->data['id'] = $number;	
		return $number;				
	}
	
	function getCharacterList()
	{
		$query = $this->db->query("SELECT name FROM players WHERE account_id = '".$this->data['id']."'");
		
		if($query->numRows() != 0)
		{
			$list= array();
		
			while($fetch = $query->fetch())
			{
				$list[] = $fetch->name;
			}
			
			return $list;
		}
		else
			return false;
	}
	
	function save()
	{
		$i = 0;
	
		$query = $this->db->query("SELECT id FROM accounts WHERE id = '".$this->data['id']."'");
		
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
			
			$this->db->query("UPDATE accounts SET $update WHERE id = '".$this->data['id']."'");
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

			$this->db->query("INSERT INTO accounts ($insert_fields) values($insert_values)");			
		}
	}
	
	function updatePremDays()
	{
		$daysToRemove = time() - $this->data['lastday'];
		
		if($daysToRemove >= $this->data['premdays'])
		{
			$this->data['premdays'] = 0;
		}
		else
		{
			$this->data['premdays'] -= $daysToRemove;
		}
		
		$this->data['lastday'] = time();
	}
	
	function set($field, $value)
	{
		$this->data[$field] = $value;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>