<?
class Account extends MySQL
{
	private $db, $data = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}

	function load($id, $fields)
	{
		$query = $this->db->query("SELECT id, $fields FROM accounts WHERE id = '".$id."'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			$this->data['id'] = $fetch->id;	
					
			$e = explode(", ", $fields);
			foreach($e as $field)
			{
				$this->data[$field] = $fetch->$field;
			}
		}
		else
		{
			return false;
		}			
	}
	
	function loadByEmail($email, $fields)
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
				
				$this->db->query("UPDATE accounts SET $update WHERE id = id = '".$this->data['id']."'");
			}
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