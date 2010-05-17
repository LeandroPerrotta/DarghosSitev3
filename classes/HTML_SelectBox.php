<?php
class HTML_SelectBox
{
	private $_options = array();
	private $_name, $_size = 120, $_selectedIndex = 0;
	
	function HTML_SelectBox()
	{
		
	}
	
	function SelectedIndex($index)
	{
		$this->_options[$index]["selected"] = 1;
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
	
	function AddOption($label, $value = "")
	{		
		$option = array();
		
		$option["label"] = $label;
		$option["value"] = $label;
		
		if($value)
		{
			$option["value"] = $value;
		}
		
		$option["selected"] = 0;
		
		return array_push($this->_options, $option);
	}
	
	function GetPost()
	{
		return $_POST[$this->_name];
	}	
	
	function Draw()
	{
		$string = "<select name='{$this->_name}' style='width: {$this->_size}px;'>";

		foreach($this->_options as $key => $option)
		{
			if($option["selected"] == 1)
				$string .= "<option selected='selected' value='{$option["value"]}'>{$option["label"]}</option>";
			else
				$string .= "<option value='{$option["value"]}'>{$option["label"]}</option>";	
		}
		
		$string .= "</select>";
		
		return $string;
	}
}