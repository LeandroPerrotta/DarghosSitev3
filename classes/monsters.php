<?php
class Monsters
{
	private $db, $data = array(), $element;

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
		
		if(file_exists(DIR_DATA.MONSTERS_FILE))
			$this->element = simplexml_load_file(DIR_DATA.MONSTERS_FILE);
		else
			die("Banco de dados de monstros não encontrado.");	
	}

	function load($name)
	{
		foreach($this->element->monster as $monster)
		{
			if($monster['name'] == $name)
			{
				$this->data['file'] = $monster['file'];
				return true;
			}				
		}
			return false;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>