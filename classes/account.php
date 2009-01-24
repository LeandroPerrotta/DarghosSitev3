<?
class Account extends MySQL
{
	private $db, $data = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}

	function getNumber()
	{
		$random = rand(100000, 9999999);
		$number = $random;
		
		$exist = array();
	
		$query = $this->db->query("SELECT id FROM accounts");
		
		foreach($query->fetch()->id as $account)
		{
			$exist[] = $account;
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
}
?>