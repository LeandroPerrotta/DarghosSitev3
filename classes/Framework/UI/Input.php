<?php
namespace Framework\UI;
class Input extends Shared
{				
	function __construct(&$dom, $value = NULL, $label = NULL)
	{		
		parent::__construct($dom, $this->NodeName(), $value);
	}
	
	function NodeName()
	{
		$class = explode('\\', __CLASS__);
		return strtolower(end($class));
	}
	
	function SetLabel($label)
	{
		throw new \Exception('Deprecated use.');
	}
	
	function SetId($id)
	{
		if($this->label)
		{
			$this->label->SetFor($id);
		}
		
		parent::SetId($id);
	}
	
	function IsPassword(){ $this->setAttribute("type", "password"); }
	function IsCheackeable(){ $this->setAttribute("type", "checkbox"); }
	function IsRadio(){ $this->setAttribute("type", "radio"); }
	function IsHidden(){ $this->setAttribute("type", "hidden"); }
	function IsButton(){ 
		$this->SetClass("button");
		$this->setAttribute("type", "submit"); 
	}
	function IsDefault(){ $this->setAttribute("checked", "checked"); }
	function IsNotWritable(){ $this->setAttribute("readonly", "readonly"); }
	
	function IsTextArea($rows = null, $colums = null)
	{
		throw new \Exception('Deprectead use: Try new \\Framework\\HTML\\TextArea() instead.');
	}
	
	/* Special */
	function IsOnlyNumeric($min = NULL, $max = NULL)
	{
		$this->SetSize(self::SIZE_SMALL);
		$this->EventOnKeyUp("return filterOnlyNumbers(this)");
		
		if($min)
			$this->setAttribute("minvalue", $min);
			
		if($max)
			$this->setAttribute("maxvalue", $max);
	}
	
	function IsDatepick()
	{
		$this->SetClass("datepicker");
	}
}
