<?php
namespace Core;
class DBTable
{
	protected $m_tableName;
	
	function __construct($tableName)
	{
		$this->m_tableName = $tableName;
	}
	
	private static function _GetFields()
	{
		$vars = get_class_vars(get_called_class());
		unset($vars["m_tableName"]);
		return $vars;		
	}
	
	private function GetFields()
	{
		$vars = get_object_vars($this);
		unset($vars["m_tableName"]);
		return $vars;		
	}
	
	function SerializePost()
	{
		/* TODO: Must be implemented */
	}
	
	static function Load($tableName, $id)
	{
		$tableFields = self::_GetFields();
		
		$db = Main::$DB;
		
		$fields = implode("`, `", array_keys($tableFields));
		$query = "SELECT `{$fields}` FROM `{$tableName}` WHERE `id` = {$id}";
		
		$result = $db->query($query);
		if($result->numRows() != 1)
			return false;
		
		$obj = $result->fetch(get_called_class());
		return $obj;
	}
	
	function Update()
	{
		$db = Main::$DB;
		
		$query = "UPDATE `{$this->m_tableName}` SET ";
		
		$first = true;
		$tableFields = $this->GetFields();
		foreach($tableFields as $key => $value)
		{
			if($first)
				$first = false;
			else
				$query .= ", ";
				
			if(is_numeric($value))
				$query .= "`{$key}` = {$value}";
			else
				$query .= "`{$key}` = '{$db->escapeString($value)}'";
		}		
		
		$query .= " WHERE `id` = {$tableFields["id"]}";
		
		$db->ExecQuery($query);
	}
	
	function Delete()
	{
		$db = Main::$DB;
		
		$tableFields = $this->GetFields();
		
		$query = "DELETE FROM `{$this->m_tableName}` WHERE `id` = {$tableFields["id"]} ";
		$db->ExecQuery($query);		
	}
	
	function Insert()
	{
		$db = Main::$DB;
		
		$query = "INSERT INTO `{$this->m_tableName}`";
		
		$tableFields = $this->GetFields();
		unset($tableFields["id"]);
		
		$fields = implode("`, `", array_keys($tableFields));
		$query .= "(`{$fields}`) VALUES (";
		
		$first = true;
		
		foreach($tableFields as $key => $value)	
		{
			if($first)
				$first = false;
			else
				$query .= ", ";			
			
			if(is_numeric($value))
				$query .= $value;
			else 
				$query .= "'{$db->escapeString($value)}'";
		}
		
		$query .= ")";
		
		$db->ExecQuery($query);
		return $db->lastInsertId();
	}
}