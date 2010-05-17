<?php
class HTML_Input
{
	private $_name, $_value, $_size = 40, $_length, $_type = "text";
	private $_isPassword = false, $_isDisabled = false, $_isWritable = true;
	private $_isTextArea = false, $_textAreaRows = 8, $_textAreaColums = 30;
	
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
	
	function IsDisabled()
	{
		$this->_isDisabled = true;
	}
	
	function IsNotWritable()
	{
		$this->_isWritable = false;
	}
	
	function IsTextArea($rows = null, $colums = null)
	{
		$this->_isTextArea = true;
		
		if($rows)
			$this->_textAreaRows;
		
		if($colums)
			$this->_textAreaColums;
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
				case "radio": 		$string .= " type='radio'"; 			break;
				case "checkbox": 	$string .= " type='checkbox'"; 		break;
			}
			
			if(!$this->_isWritable)
				$string .= " readonly='readonly'";
				
			if($this->_isDisabled)
				$string .= " enabled='enabled'";
				
			if($this->_length)
				$string .= " maxlength='{$this->_length}'";
				
			$string .= "/>";
		}
		else
		{
			$string = "<textarea name='{$this->_name}' rows='{$this->_textAreaRows}' cols='{$this->_textAreaColums}'";
			
			if(!$this->_isWritable)
				$string .= " readonly='readonly'";
				
			if($this->_isDisabled)
				$string .= " enabled='enabled'";		

			$string .= ">{$this->_value}</textArea>";	
		}
		
		//echo "<textarea cols='25' rows='10'>{$string}</textarea>";
		
		return $string;
	}
}