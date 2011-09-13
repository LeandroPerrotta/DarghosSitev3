<?php
class Battleground
{
	const BG_KILLS_RATE = 5;
	const BG_ASSISTS_RATE = 1;
	const BG_DEATHS_RATE = 6;
	
	const PVP_TYPE_BATTLEGROUND = 0;
	const PVP_TYPE_ARENA = 1;
	
	static function listAll($from, $to)
	{		
		$query = Core::$DB->query("
		SELECT 
			result.player_id, 
			players.name, 
			result.kills, 
			result.assists, 
			result.deaths 
		FROM 
			(
				(
					SELECT 
						player_id, 
						COUNT(*) as kills, 
						0 as assists, 
						0 as deaths 
					FROM 
						custom_pvp_kills 
					WHERE 
						is_frag = 1 
					AND 
						`date` > {$from} 
					AND
						`date` <= {$to}
					AND
						`type` = ".self::PVP_TYPE_BATTLEGROUND."
					GROUP BY 
						player_id
				) 
				UNION 
				(
					SELECT 
						player_id, 
						0 as kills, 
						0 as assists, 
						COUNT(*) as deaths 
					FROM 
						custom_pvp_deaths 
					WHERE  
						`date` > {$from} 
					AND
						`date` <= {$to}		
					AND
						`type` = ".self::PVP_TYPE_BATTLEGROUND."										
					GROUP BY 
						player_id
				) 
				UNION 
				(
					SELECT 
						player_id, 
						0 as kills, 
						COUNT(*) as assists, 
						0 as deaths 
					FROM 
						custom_pvp_kills 
					WHERE 
						`date` > {$from} 
					AND
						`date` <= {$to}	
					AND
						`type` = ".self::PVP_TYPE_BATTLEGROUND."												
					GROUP BY 
						player_id
				)
			) 
			AS 
				result 
			LEFT JOIN 
				players 
			ON 
				players.id = result.player_id 
			ORDER BY 
				result.kills DESC, 
				result.deaths ASC");
		
		return $query;
	}
	
	static function buildRating($query)
	{
		$array = array();
		
		while($fetch = $query->fetch())
		{
			if($array[$fetch->player_id])
			{
				$array[$fetch->player_id]["name"] = $fetch->name;
				$array[$fetch->player_id]["kills"] += $fetch->kills;
				$array[$fetch->player_id]["deaths"] += $fetch->deaths;
				$array[$fetch->player_id]["assists"] += $fetch->assists;
			}
			else
			{
				$array[$fetch->player_id] = array(
					"name" => $fetch->name,
					"kills" => $fetch->kills,
					"deaths" => $fetch->deaths,
					"assists" => $fetch->assists,
				);
			}
			
			$rating = (($array[$fetch->player_id]["kills"] * self::BG_KILLS_RATE) + ($array[$fetch->player_id]["assists"] * self::BG_ASSISTS_RATE)) - ($array[$fetch->player_id]["deaths"] * self::BG_DEATHS_RATE);
			
			$array[$fetch->player_id]["rating"] = ($rating > 0) ? $rating : 0; 
		}
		
		$ratings = array();
		
		foreach($array as $key => $value)
		{
			$ratings[$key] = $value["rating"];
		}
		
		array_multisort($ratings, SORT_DESC, $array);
		return $array;
	}
}