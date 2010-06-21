<?php
class HTML_Input
{
	private $_name, $_id, $_value, $_size = 40, $_length, $_type = "text";
	private $_isPassword = false, $_isDisabled = false, $_isWritable = true, $_isDefault = false;
	private $_isTextArea = false, $_textAreaRows = 8, $_textAreaColums = 30;
	private $_event_onkeypress;
	
	function HTML_Input()
	{
		
	}
	
	function SetValue($value)
	{
		$this->_value = $value;
	}
	
	function GetPost()
	{
		return $_POST[$this->_name];
	}
	
	function SetName($name)
	{
		$this->_name = $name;
	}
	
	function SetLenght($length)
	{
		$this->_length = $length;
	}
	
	function SetSize($size)
	{
		$this->_size = $size;
	}
	
	function SetId($id)
	{
		$this->_id = $id;
	}
	
	function IsPassword()
	{
		$this->_type = "password";
	}
	
	function IsCheackeable()
	{
		$this->_type = "checkbox";
	}	
	
	function IsRadio()
	{
		$this->_type = "radio";
	}	
	
	function IsDefault()
	{
		$this->_isDefault = true;
	}
	
	function IsDisabled()
	{
		$this->_isDisabled = true;
	}
	
	function IsNotWritable()
	{
		$this->_isWritable = false;
	}
	
	function IsButton()
	{
		$this->_type = "submit";
	}
	
	function IsTextArea($rows = null, $colums = null)
	{
		$this->_isTextArea = true;
		
		if($rows)
			$this->_textAreaRows = $rows;
		
		if($colums)
			$this->_textAreaColums = $colums;
	}
	
	/* Events */
	
	function OnKeyPress($string)
	{
		$this->_event_onkeypress = $string;
	}
	
	function Draw()
	{		
		if(!$this->_isTextArea)
		{
			$string = "<input name='{$this->_name}' value='{$this->_value}' size='{$this->_size}'";	
	
			switch($this->_type)
			{
				case "text": 		$string .= " type='text'"; 			break;
				case "password": 	$string .= " type='password'"; 		break;
				case "radio": 		$string .= " type='radio'"; 		break;
				case "checkbox": 	$string .= " type='checkbox'"; 		break;
				case "submit": 		$string .= " type='submit' class='button'"; 		break;
			}
			
			if($this->_id)
				$string .= " id='{$this->_id}'";
			
			if(!$this->_isWritable)
				$string .= " readonly='readonly'";
			
			if($this->_isDefault)
				$string .= " checked='checked'";
				
			if($this->_isDisabled)
				$string .= " disabled='disabled'";
				
			if($this->_length)
				$string .= " maxlength='{$this->_length}'";
				
			if($this->_event_onkeypress)
				$string .= " onkeyup='{$this->_event_onkeypress}'";
				
			$string .= "/>";
		}
		else
		{
			$string = "<textarea name='{$this->_name}' rows='{$this->_textAreaRows}' cols='{$this->_textAreaColums}'";
			
			if($this->_id)
				$string .= " id='{$this->_id}'";			
			
			if(!$this->_isWritable)
				$string .= " readonly='readonly'";
				
			if($this->_isDisabled)
				$string .= " enabled='enabled'";		
				
			if($this->_event_onkeypress)
				$string .= " onkeyup='{$this->_event_onkeypress}'";				

			$string .= ">{$this->_value}</textArea>";	
		}
		
		//echo "<textarea cols='25' rows='10'>{$string}</textarea>";
		
		return $string;
	}
}