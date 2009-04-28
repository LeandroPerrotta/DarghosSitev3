<?
class MySQL 
{
	private $connection;
	
	public function connect($host, $user, $password, $database) 
	{
		$this->connection = mysql_connect($host, $user, $password);
		mysql_select_db($database, $this->connection);				
	}
	
	/*public function close() 
	{
		@mysql_close();			
	}*/	
	
	public function query($queryStr) 
	{
		$query = mysql_query($queryStr, $this->connection);
		
		if(!$query)
			echo mysql_error($this->connection)."<br>";		
		else
			return new Query($query, $this->connection);
	}
	
	public function lastInsertId() 
	{
		return mysql_insert_id($this->connection);
	}
}

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
	
	public function fetch() 
	{
		return mysql_fetch_object($this->queryResource);
	}
	
	public function fetchArray() 
	{
		return mysql_fetch_array($this->queryResource);
	}
}
?>