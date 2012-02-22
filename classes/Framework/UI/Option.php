<?php
namespace Framework\UI;
class Option extends Shared
{		
	function __construct(&$dom, $label, $value = NULL)
	{		
		parent::__construct($dom, $this->NodeName(), $label);
		
		if($value)
			$this->SetValue($value);
		else
			$this->SetValue($label);
	}
	
	function NodeName()
	{
		$class = explode('\\', __CLASS__);
		return strtolower(end($class));
	}
	
	function IsSelected(){
		$this->setAttribute("selected", "selected");
	}
}