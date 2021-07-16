<?php
namespace Core;
class Views extends \DOMDocument
{
	protected
		$data
		;	
		
	function __construct($data)
	{
		parent::__construct();
		$this->data = $data;
		$this->loadJavaScriptHelper();
	}
	
	private function loadJavaScriptHelper()
	{
		$data = explode("\\", get_called_class());
		unset($data[0]);		
		$str = implode("_", $data);
		
		$str = strtolower($str);
		
		\Core\Main::includeJavaScriptSource("views/{$str}.js");
	}
}