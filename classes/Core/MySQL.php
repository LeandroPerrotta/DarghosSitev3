<?
namespace Core;
class MySQL 
{
	private $connection;
	
	public function connect($host, $user, $password, $database) 
	{
		$this->connection = mysql_pconnect($host, $user, $password);
		mysql_select_db($database, $this->connection);				
	}
	
	public function close() 
	{
		@mysql_close($this->connection);			
	}	
	
	public function query($queryStr) 
	{
		$query = mysql_query($queryStr, $this->connection);
		
		if(!$query)
		{
			throw new \Exception("".mysql_error($this->connection)."\n query: {$queryStr}");
			return false;		
		}	
		else
			return new Query($query, $this->connection);
	}
	
	function escapeString($string)
	{
		return mysql_real_escape_string($string, $this->connection);
	}
	
	public function ExecQuery($queryStr) 
	{
		
		$ret = mysql_unbuffered_query($queryStr, $this->connection);
		
		if(!$ret)
		{
			throw new \Exception("".mysql_error($this->connection)."\n query: {$queryStr}");
			return false;			
		}
		
		return true;
	}	
	
	public function lastInsertId()
	{
		return mysql_insert_id($this->connection);
	}
}
?>
