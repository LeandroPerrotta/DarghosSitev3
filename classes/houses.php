<?php
class Houses
{
	private $db, $data = array(), $element;

	function __construct()
	{
		global $db;
		$this->db = $db;
		
		/*if(file_exists(DIR_DATA.HOUSES_FILE))
			$this->element = simplexml_load_file(DIR_DATA.HOUSES_FILE);
		else
			die("Banco de dados de casas não localizado.");	*/
	}

	function load($id)
	{
		//Isto não é mais necessario?!
		/*foreach($this->element->house as $house)
		{
			if($house['houseid'] == $id)
			{
				$this->data['name'] = $house['name'];
				$this->data['rent'] = $house['rent'];
				$this->data['size'] = $house['size'];
				$this->data['townid'] = (int)$house['townid'];
			}
		}*/
		
		$query = $this->db->query("SELECT * FROM houses WHERE id = '{$id}'");
		
		while($fetch = $query->fetch())
		{
			$this->data['name'] = $fetch->name;
			$this->data['rent'] = $fetch->rent;
			$this->data['size'] = $fetch->tiles;
			$this->data['townid'] = $fetch->townid;			
			$this->data['owner'] = $fetch->owner;
			$this->data['paid'] = $fetch->paid;
			$this->data['warnings'] = $fetch->warnings;
		}
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>