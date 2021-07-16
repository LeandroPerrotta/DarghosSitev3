<?php
namespace Framework\UI;
class Label extends Shared
{		
	function __construct(&$dom, $value)
	{		
		parent::__construct($dom, $this->NodeName(), $value);
	}
	
	function NodeName()
	{
		$class = explode('\\', __CLASS__);
		return strtolower(end($class));
	}
	
	function SetFor($name)
	{
		$this->setAttribute("for", $name);
	}
}