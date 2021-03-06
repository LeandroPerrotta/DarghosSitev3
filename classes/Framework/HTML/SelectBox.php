<?php
namespace Framework\HTML;
class SelectBox
{
	private $_options = array();
	private $_name, $_size = Consts::SELECTBOX_SIZE_NORMAL, $_selectedIndex = 0, $onChangeSubmit = false;
	
	function __construct()
	{

	}
	
	function SelectedIndex($index)
	{
		$this->_selectedIndex = $index;
	}
	
	function SetName($name)
	{
		$this->_name = $name;
	}
	
	function SetOptions($arrayOptions)
	{
		$this->_options = $arrayOptions;
	}
	
	function SetSize($size)
	{
		$this->_size = $size;
	}
	
	function AddOption($label, $value = null, $selected = false, $disabled = false)
	{		
		$option = array();
		
		$option["label"] = $label;
		$option["value"] = $label;
		
		if(isset($value))
		{
			$option["value"] = $value;
		}

		if($disabled)
		{
			$option["disabled"] = true;
		}
		
		array_push($this->_options, $option);
		
		$key = count($this->_options) - 1;
		
		if($selected)
		{
			$this->SelectedIndex($key);
		}
		
		return $key;
	}
	
	function onChangeSubmit()
	{
		$this->onChangeSubmit = true;
	}
	
	function GetPost()
	{
		return $_POST[$this->_name];
	}	
	
	function Draw()
	{
		$onchange = "";
		
		if($this->onChangeSubmit)
			$onchange = "onchange='this.form.submit()'";
		
		$string = "<select name='{$this->_name}' style='width: {$this->_size}px;' {$onchange}>";

		foreach($this->_options as $key => $option)
		{
			$string .= "<option ";
				
			if($key == $this->_selectedIndex || ($_POST && $this->GetPost() == $option["value"]))
				$string .= "selected='selected' ";

			if($option["disabled"])
				$string .= "disabled='disabled' ";
			
			$string .= "value='{$option["value"]}'>{$option["label"]}</option>";	
		}
		
		$string .= "</select>";
		
		return $string;
	}
}
