<?

$query = Core::$DB->query("SELECT * FROM old_accounts ORDER BY id");

while($fetch = $query->fetch())
{
	if($fetch->premend == 0 || $fetch->premend <= time())
		$premdays = 0;
	else
		$premdays = ($fetch->premend - time()) / 60 / 60 / 24;
	
	Core::$DB->query("
	INSERT INTO 
		accounts 
		(
			`name`, 
			`password`,
			`premdays`,
			`lastday`,
			`email`,
			`warnings`
		)
		VALUES
		(
			'".addslashes($fetch->name)."',
			'{$fetch->password}',
			'{$premdays}',
			UNIX_TIMESTAMP(),
			'".addslashes($fetch->email)."',
			'{$fetch->warnings}'
		)");
	
	$id = Core::$DB->lastInsertId();
	
	Core::$DB->query("
	INSERT INTO 
		wb_accounts_personal 
		(
			`account_id`, 
			`real_name`,
			`location`,
			`url`,
			`creation`
		)
		VALUES
		(
			'{$id}',
			'".addslashes($fetch->real_name)."',
			'".addslashes($fetch->location)."',
			'".addslashes($fetch->url)."',
			'{$fetch->creation}'
		)");	
}

?>