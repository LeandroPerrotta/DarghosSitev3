<?php
namespace Core;
class Structs
{
	protected $m_data;
	protected $m_dataTypes = array();
	
	function __construct($data = NULL)
	{
		if($data)
			$this->m_data = $data;
		
		$this->m_dataTypes = static::LoadTypes();
	}
	
	static function Get($id)
	{
		foreach(static::$m_typeStrings as $k => $v)
		{	
			if($k == $id)
			{
				return $k;
			}
		}
	
		return false;
	}
	
	static function GetByString($string)
	{
		foreach(static::$m_typeStrings as $k => $v)
		{
			if(strtolower($k) == strtolower($string))
			{
				return $k;
			}
		}
		
		$keys = array_keys(static::$m_types);
		return $keys[0];
	}
	
	static function GetString($type)
	{
		return static::$m_typeStrings[$type];
	}	
	
	function SetDataByType($type)
	{
		foreach($this->m_dataTypes as $k => $v)
		{
			if(strtolower($v) == strtolower($type))
			{
				$this->m_data = $k;
				return $k;
			}
		}
	}
}