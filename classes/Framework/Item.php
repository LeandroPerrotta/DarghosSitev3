<?php
namespace Framework;
class Item
{
	protected
		$id
		,$name
	;
	
	function __construct(){
		
	}
	
	function GetId(){ return $this->id; }
	function GetName(){ return $this->name; }
	
	function SetId($id){ $this->id = $id; }
	function SetName($name){ $this->name = $name; }
}