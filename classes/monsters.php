<?php
class Monsters
{
	private $db, $data = array(), $element;
	static private $_instance;

	function __construct()
	{
		global $db;
		$this->db = $db;
		
		if(file_exists(DIR_DATA.MONSTERS_FILE))
			$this->element = simplexml_load_file(DIR_DATA.MONSTERS_FILE);
		else
			die("Banco de dados de monstros não localizado.");	
	}

	static function GetInstance()
	{
		if(self::$_instance)
		{
			return self::$_instance;
		}
		
		$class = __CLASS__;
		self::$_instance = new $class;
		return self::$_instance;
	}	
	
	function load($name)
	{
		foreach($this->element->monster as $monster)
		{
			if(strtolower($monster['name']) == strtolower($name))
			{
				$this->data['file'] = $monster['file'];
				return true;
			}				
		}
			return false;
	}
	
	function getList()
	{
		return $this->element->monster;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>