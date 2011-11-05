<?php
class ResultIterator
{	
	private $query, $result, $pointer = 0 , $end;
	
	function ResultIterator(Query $query)
	{
		$this->query = $query;
		$this->end = $query->numRows();
	}
	
	function end()
	{
		return $this->end;
	}
	
	function begin()
	{
		return 0;
	}
	
	function next()
	{
		if($this->query->numRows() == 0)
			return $this->end();
		
		$this->result = $this->query->fetch();
		$ret = $this->pointer;
		$this->pointer++;
		
		return $ret;
	}
	
	function result()
	{
		return $this->result;
	}
}