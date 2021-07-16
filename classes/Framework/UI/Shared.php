<?php
namespace Framework\UI;
abstract class Shared extends \DOMElement
{
	const SIZE_SMALL = 20;
	const SIZE_NORMAL = 40;
	const SIZE_BIG = 80;
	
	/*protected
		$root
		//,$element
	;*/
	
	abstract protected function NodeName();
	
	function __construct(&$dom, $node, $value = NULL)
	{
		parent::__construct($node, $value);
		$dom->appendChild($this);
		/*$this->root = new \SimpleXMLElement("<root></root>");
		$this->element = &$this->root->addChild($this->NodeName(), $value);*/
	}
	
	/* Generic */
	function SetText($text){ $this->nodeValue = $text; }
	function SetValue($value){ $this->setAttribute("value", $value); }
	function SetName($name)	{ $this->setAttribute("name", $name); }
	function SetLength($length)	{ $this->setAttribute("length", $length); }
	function SetSize($size)	{ $this->setAttribute("size", $size); }
	function SetId($id)	{ $this->setAttribute("id", $id); }
	function SetStyle($style){ $this->setAttribute("style", $style); }
	function SetClass($class){ $this->setAttribute("class", $class); }
	
	function GetValue(){ return $this->nodeValue; }
	function GetName(){ return $this->getAttribute("name"); }
	function GetLength(){ return $this->getAttribute("length"); }
	function GetSize(){ return $this->getAttribute("size"); }
	function GetId(){ return $this->getAttribute("id"); }
	
	function IsDisabled(){ $this->setAttribute("disabled", "disabled"); }
	
	/* Events */
	function EventOnKeyPress($event) { $this->setAttribute("onkeypress", $event); }
	function EventOnKeyUp($event) { $this->setAttribute("onkeyup", $event); }
	function EventOnChange($event) { $this->setAttribute("onchange", $event); }
	
	/* Global */
	/*function GetAttribute($name)
	{
		$attrs = $this->element->attributes();
		if($attrs[$name])
			return $attrs[$name];
		
		return NULL;
	}*/
	
	/*function &AddElement(\SimpleXMLElement &$element, \SimpleXMLElement $child)
	{
		$node = &$element->addChild($child->getName(), $child);
		if(count($child->attributes()) > 0)
		{
			foreach($child->attributes() as $name => $value)
			{
				$node->addAttribute($name, $value);
			}
		}		
		
		if(count($child->children()) > 0)
		{
			foreach($child->children() as $child)
			{
				$this->AddElement($node, $child);
			}
		}
		
		return $node;
	}*/
	
	function Draw()
	{
		/*$children = $this->root->children();
		return $children[0]->asXML();*/
		//return $this->saveHTML();
	}
}