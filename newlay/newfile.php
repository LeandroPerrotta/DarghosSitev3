#!/usr/bin/php -q
<?
include "/var/www/ultraxsoft_admin/tasks/index.php";	
	
LogMessage("=== INICIANDO TASK SPOOFPLAYERS ===");

$enabled = true;
$limit = 75;
$starts = 25;

$minlevel = 20;
$maxlevel = 60;

$lastLogout = time() - (60 * 60 * 24 * 3);

foreach($serverDB as $DB)
{	
	$ipserver = "174.37.227.172";
	$portserver = 7171;

	$server = new OTS_ServerInfo($ipserver, $portserver);
	$status = $server->info(OTS_ServerStatus::REQUEST_BASIC_SERVER_INFO);

	LogMessage("Conectando a servidor: {$ipserver}/{$portserver}");

	if($status)
	{
		$DB->query("UPDATE `players` SET `online` = 0, `is_spoof` = 0, `lastlogin` = UNIX_TIMESTAMP() WHERE `is_spoof` = 1");
		
		$playerson = $status->getOnlinePlayers();
		if($playerson <= $starts)
			continue;

		$tospoof = min(($playerson + ($limit - $starts)) - $limit, $limit);

		LogMessage("Jogadores online: {$playerson}");
		LogMessage("Jogadores para spoofar: {$tospoof}");
		
		if($tospoof == 0) continue;

		$query = $DB->query("
		SELECT 
			`p`.`id`
			,`p`.`account_id`
		FROM 
			`players` `p`
		LEFT JOIN
			`accounts` `a`
		ON
			`p`.`account_id` = `a`.`id`
		WHERE
			`p`.`online` = 0
			AND `a`.`premdays` = 0
			AND `p`.`level` >= {$minlevel}
			AND `p`.`level` <= {$maxlevel}
			AND `p`.`lastlogout` <= {$lastLogout}
			AND `p`.`lastlogout` > 0");

		$targetList = array();

		while($fetch = $query->fetch())
		{
			$targetList[] = array(
				"id" => $fetch->id
				,"account_id" => $fetch->account_id
			);
		}
		
		$spoofedPlayers = 0;

		while($spoofedPlayers < $tospoof)
		{
			$target = $targetList[rand(0, count($targetList))];
			
			$onlineCheck = $DB->query("SELECT `id` FROM `players` WHERE `account_id` = {$target["account_id"]} AND `online` = 1");
			if($onlineCheck->numRows() == 0)
			{
				$DB->query("UPDATE `players` SET `online` = '1', `is_spoof = 1 WHERE `id` = '{$target["id"]}'");
				$spoofedPlayers++;
			}			
		}

		LogMessage("Jogadores spoofados: {$spoofedPlayers}");
	}
	else
	{
		$DB->query("UPDATE `players` SET `online` = 0, `is_spoof` = 0 WHERE `is_spoof` = 1");
		LogMessage("O servidor estava offline.");
	}
}
?>