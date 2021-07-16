<?php
namespace Core;
class Query 
{
	private $queryResource;
	private $connectionResource;
	
	public function __construct($queryRes, $connRes) 
	{
		$this->queryResource = $queryRes;
		$this->connectionResource = $connRes;
		
		if($this->queryResource AND $this->connectionResource) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	public function numRows() 
	{
		return mysql_num_rows($this->queryResource);
	}
	
	public function fetch($obj = NULL, $params = NULL) 
	{
		$ret = NULL;
		
		if($obj)
		{
			$ret = mysql_fetch_object($this->queryResource, $obj, $params);
		}
		else
		{
			$ret = mysql_fetch_object($this->queryResource);
		}
		
		return $ret;
	}
	
	public function fetchAsArrayObject(&$array, $objectName, $params = NULL)
	{
		while($obj = $this->fetch($objectName, $params))
		{
			array_push($array, $obj);
		}
	}
	
	public function fetchArray() 
	{
		return mysql_fetch_array($this->queryResource);
	}
	
	public function fetchAssocArray()
	{
		return mysql_fetch_assoc($this->queryResource);
	}
	
	public function pointerRow($rownumber)
	{
		return mysql_data_seek($this->queryResource, $rownumber);
	}
	
	public function getData()
	{
		return mysql_fetch_assoc($this->queryResource);
	}
	
	public function getResultIterator()
	{
		return new ResultIterator($this);
	}
}
?>