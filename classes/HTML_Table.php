<?php
class HTML_Table
{
	private $_dataRow = array(), $_colums, $_fields = array();
	private $_width = "90%";
	
	function HTML_Table()
	{
		
	}
	
	function AddField($value, $width = null, $style = null, $colspan = null)
	{
		$field = array();
		
		$field["value"] = $value;
		
		if($width)
			$field["width"] = $width;
			
		if($style)
			$field["style"] = $style;

		if($colspan)
			$field["colspan"] = $colspan;	
		
		$this->_fields[] = $field;
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
	
	function Draw()
	{
		$string = "
		<table width='{$this->_width}' cellspacing='0' cellpadding='0' id='table'>";
		
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
							$args .= " style='{$field["style"]}%'";
							
						if($field["colspan"])
							$args .= " colspan='{$field["colspan"]}'";
						
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