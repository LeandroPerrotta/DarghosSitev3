<?php
namespace Framework\UI;
class TextArea extends Shared
{		
	function __construct(&$dom, $value = NULL)
	{		
		parent::__construct($dom, $this->NodeName(), $value);
	}
	
	function NodeName()
	{
		$class = explode('\\', __CLASS__);
		return strtolower(end($class));
	}
	
	function SetRows($rows){
		$this->setAttribute("rows", $rows);
	}
	function SetColumns($cols){
		$this->setAttribute("cols", $cols);
	}	
}