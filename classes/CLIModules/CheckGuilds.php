<?php 
namespace CLIModules;

use \Framework\Guilds;
use \Framework\Guilds\Rank;
use \Framework\Guilds\War;
use \Core\Configs;

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
		
		echo "Required vices: " . Configs::Get(Configs::eConf()->GUILDS_VICES_TO_FORMATION) . "\n\n";
		
		while($result_guild = $query_guilds->fetch())
		{
			$guild = new Guilds();
			$guild->Load($result_guild->id);
			
			$guildOK = true;
			$toDelete = false;
			
			echo "Checking guild {$guild->GetName()}\n";
			
			$leader = new \Framework\Player(); 
			$leader->load($guild->GetOwnerId());
			
			if(Configs::Get(Configs::eConf()->GUILD_LEADERS_MUST_BE_PREMIUM) && !$leader->isPremium())
			{
				echo "The guild leader has not premium and need it...\n";
				$guildOK = false;
			}
			
			if($guildOK)
				$vices = $guild->SearchRankByLevel(Guilds::RANK_VICE);
				$vices instanceof Rank;

				echo "Vices: {$vices->MemberCount()}\n";
				echo "Formation Time: {$guild->GetFormationTime()}\n\n";
				//guild já ativa não possui membros sulficientes para ser uma guild ativa
				if($vices->MemberCount() < Configs::Get(Configs::eConf()->GUILDS_VICES_TO_FORMATION))
					$guildOK = false;		
			}
			
			if($guildOK && Configs::Get(Configs::eConf()->GUILD_VICE_LEADERS_MUST_BE_PREMIUM))
			{
				$guildOK = false;
				$foundVices = 0;
				
				foreach($vices->Members as $player)
				{
					$player instanceof \Framework\Player;
					
					if($player->isPremium()){
						$foundVices++;
						
						if($foundVices == Configs::Get(Configs::eConf()->GUILDS_VICES_TO_FORMATION)){
							$guildOK = true;
							break;
						}
					}
				}
			}
			
			if($guild->GetFormationTime() == 0)
			{
				if(!$guildOK)
				{
					$guild->SetFormationTime(time() + 60 * 60 * 24 * 5);
					$guild->SetStatus(Guilds::STATUS_FORMATION);
					$formingGuilds++;
				}
				else
					continue;
			}
			elseif(time() > $guild->GetFormationTime() && !$guildOK)
			{
				$toDelete = true;
				$terminatedGuilds++;
			}
			elseif($guildOK)
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