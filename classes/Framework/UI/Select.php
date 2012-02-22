<?php
namespace Framework\UI;
class Select extends Shared
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
	
	function AddOption($label, $value = null, $selected = false, $disabled = false)
	{		
		$option = new Option($this, $label, $value);
		$this->appendChild($option);
		
		if($selected)
			$option->IsSelected();
		
		if($disabled)
			$option->IsDisabled();
	}
	
	function AutoSubmit()
	{
		$this->EventOnChange("this.form.submit()");
	}
	
	function onChangeSubmit(){ $this->AutoSubmit(); }
}
