<?php 
namespace Core;
class Pages
{
	private $data;
	private $content;
	private $changes;
	private $patch;
	
	function __construct($patch)
	{		
		$this->patch = $patch;
		
		if(file_exists($patch))
		{
			$this->data = new \SimpleXMLElement($patch, null, true);
		}
		else
		{
			$this->data = new \SimpleXMLElement("<data></data>");

			$this->data->addChild("content");
			$this->data->addChild("changes");
		}
	}
	
	function AddChange($author)
	{
		$child = $this->data->changes->addChild("change");
		$child instanceof \SimpleXMLElement;
		
		$child->addAttribute("author", $author);
		$child->addAttribute("date", time());
	}
	
	function SetContent($string)
	{
		$string = stripcslashes($string);
		$this->data->content[0] = str_replace(array("\\n", "\\r"), "", $string);
	}
	
	function GetContent()
	{				
		return $this->data->content;
	}
	
	function save()
	{
		return $this->data->asXML($this->patch);
	}
}
?>