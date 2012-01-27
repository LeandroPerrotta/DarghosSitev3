<?php
namespace Core;
abstract class Enumerators
{
	private $m_count = 0;
		
	function __construct()
	{
		foreach($this as $var => $value)
		{
			if($var != "m_count")
			{
				$this->{$var} = $this->m_count;
				$this->m_count++;
			}
		}
	}
	
	function last(){ return $this->m_count; }
}