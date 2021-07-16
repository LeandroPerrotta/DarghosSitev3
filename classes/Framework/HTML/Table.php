<?php
namespace Framework\HTML;
class Table
{
	private $_dataRow = array(), $_colums, $_fields = array();
	private $_width = "90%", $_dropdown = false, $_subtable = false;
	
	public $style = "";
	
	function __construct()
	{
		
	}
	
	function &AddField($value, $width = null, $style = null, $colspan = null, $isheader = null)
	{
		$field = array();
		
		$field["value"] = $value;
		
		if($width)
			$field["width"] = $width;
			
		if($style)
			$field["style"] = $style;

		if($colspan)
			$field["colspan"] = $colspan;	
			
		if($isheader)	
			$field["class"] = "header";
			
		$this->_fields[] =& $field;
		return $field;
	}
	
	function AddRow()
	{
		$this->_dataRow[] = $this->_fields;
		
		if(count($this->_fields) > $this->_colums)
			$this->_colums = count($this->_fields);
		
		$this->_fields = array();
	}
	
	function AddDataRow()
	{
		$args = func_get_args(); //array contendo os argumentos recebidos
				
		$this->_dataRow[] = $args;	
		
		if(count($args) > $this->_colums)
			$this->_colums = count($args);
	}	
	
	function SetWidth($width)
	{
		$this->_width = $width;
	}
	
	function IsDropDownHeader()
	{
		$this->_dropdown = true;
	}
	
	function IsSubTable()
	{
		$this->_subtable = true;
	}
	
	function Draw()
	{
		$args = "";
		
		$classes = array();
		
		if($this->_dropdown)
			$classes[] = "dropdowntable-2";
			
		if($this->_subtable)
			$classes[] = "dropdowntable";
			
		if(count($classes) > 0)
		{
			$args .= "class='";
			
			foreach($classes as $k => $classe)
			{
				$args .= $classe;
				
				if($k != count($classes) - 1)
					$args .= " ";
			}
			
			$args .= "'";
		}
			
		if($this->style != "")
			$args .= "style='{$this->style}' ";
		
		$string = "
		<table width='{$this->_width}' {$args} cellspacing='0' cellpadding='0' id='table'>";
		
		$i = 0;
		
		foreach($this->_dataRow as $row)
		{
			$i++;
			
			if($i == 1)
			{
				if(!is_array($row[0]))
				{
					$value = $row[0];
				}
				else
				{
					$value = $row[0]["value"];
				}
				
				$string .= "
				<tr>
					<th colspan='{$this->_colums}'>{$value}</th>
				</tr>
				";
			}
			else
			{
				$string .= "
				<tr>
				";				
				
				foreach($row as $field)
				{
					if(!is_array($field))
					{		
						$string .= "
							<td>{$field}</td>
						";
					}	
					else
					{
						$args = null;
						
						if($field["width"])
							$args .= "width='{$field["width"]}%'";
							
						if($field["style"])
							$args .= " style='{$field["style"]}'";
							
						if($field["colspan"])
							$args .= " colspan='{$field["colspan"]}'";
							
						if($field["class"])
							$args .= " class='{$field["class"]}'";
						
						$string .= "
							<td {$args}>{$field["value"]}</td>
						";					
					}			
				}
				
				$string .= "
				</tr>
				";					
			}
		}

		$string .= "
		</table>";		
		
		return $string;
	}
}
?>