<?php
namespace Core;
class Structs
{
	private static $m_position = 0;
	
	static function ItValid($reset = true)
	{
		if(static::$m_typeStrings[self::$m_position])
			return true;
		
		if($reset)
			self::$m_position = 0;
		
		return false;
	}
	
	static function ItNext()
	{
		self::$m_position++;
	}
	
	static function It()
	{
		$keys = array_keys(static::$m_typeStrings);
		return $keys[self::$m_position];
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
	
		$keys = array_keys(static::$m_typeStrings);
		return $keys[0];
	}
	
	static function GetByString($string, $returnDefault = true)
	{
		foreach(static::$m_typeStrings as $k => $v)
		{
			if(strtolower($v) == strtolower($string))
			{
				return $k;
			}
		}
		
		if(!$returnDefault)
			return false;
		
		$keys = array_keys(static::$m_typeStrings);
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