<?php
namespace Framework\UI;
class Form extends Shared
{	
	private			
		$fieldSet
	;
	
	function __construct(&$dom, $action = NULL, $type = "POST")
	{		
		parent::__construct($dom, $this->NodeName());
		
		if(!$action)
			$action = $_SERVER["REQUEST_URI"];
		
		$this->setAttribute("action", $action);
		$this->setAttribute("method", $type);
		
		$fieldSet = new \DOMElement("fieldset");
		$this->fieldSet = $this->appendChild($fieldSet);
	}
	
	function GetFieldSet()
	{
		return $this->fieldSet;
	}
	
	function NodeName()
	{
		$class = explode('\\', __CLASS__);
		return strtolower(end($class));
	}
}