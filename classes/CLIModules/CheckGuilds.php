<?php 
namespace CLIModules;

use \Framework\Guilds;
use \Framework\Guilds\Rank;
use \Framework\Guilds\War;

class CheckGuilds
{
	private $db;
	
	function __construct()
	{
		$this->db = &\Core\Main::$DB;
	}
	
	function Run()
	{
		$this->db->ExecQuery("UPDATE `guild_wars` SET `reply` = '-1', `status` = '-1' WHERE `reply` != '-1' AND ".time()." > `end`");
		
		$query_guilds = $this->db->query("SELECT `id` FROM `guilds`");
		
		if($query_guilds->numRows() == 0)
		{
			return true;
		}
		
		$terminatedGuilds = 0;
		$formedGuilds = 0;
		$formingGuilds = 0;
		
		while($result_guild = $query_guilds->fetch())
		{
			$guild = new Guilds();
			$guild->Load($result_guild->id);
			
			$vices = $guild->SearchRankByLevel(Guilds::RANK_VICE);
			$vices instanceof Rank;
			$hasVices = false;
			$toDelete = false;
			
			//guild já ativa não possui membros sulficientes para ser uma guild ativa
			if($vices->MemberCount() >= \Core\Configs::Get(\Core\Configs::eConf()->GUILDS_VICES_TO_FORMATION))
			{
				$hasVices = true;
			}			
			
			if($guild->GetFormationTime() == 0)
			{
				if(!$hasVices)
				{
					$guild->SetFormationTime(time() + 60 * 60 * 24 * 5);
					$guild->SetStatus(Guilds::STATUS_FORMATION);
					$formingGuilds++;
				}
				else
					continue;
			}
			elseif(time() > $guild->GetFormationTime() && !$hasVices)
			{
				$toDelete = true;
				$terminatedGuilds++;
			}
			else
			{
				$guild->SetStatus(Guilds::STATUS_FORMED);
				$guild->SetFormationTime(0);
				$formedGuilds++;
			}
			
			if($toDelete)
				$guild->Delete();
			else
				$guild->Save();			
		}
		
		echo 
"Guilds Formadas: {$formedGuilds}\n
Guilds aguardando novos vice-lideres: {$formingGuilds}\n
Guilds encerradas: {$terminatedGuilds}\n
";
	}
}
?>