<?php
namespace Core;
abstract class Structs
{
	protected $m_data;
	protected $m_dataTypes = array();
	
	function __construct($data = NULL)
	{
		if($data)
			$this->m_data = $data;
		
		$this->m_dataTypes = static::LoadTypes();
	}
	
	abstract static function LoadTypes();
	
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
	
	function GetData()
	{
		return $this->m_data;
	}
	
	function GetType()
	{
		return $this->m_dataTypes[$this->m_data];
	}
}