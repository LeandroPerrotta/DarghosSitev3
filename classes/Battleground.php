<?php
class Battleground
{
	const BG_KILLS_RATE = 5;
	const BG_ASSISTS_RATE = 1;
	const BG_DEATHS_RATE = 6;
	
	const PVP_TYPE_BATTLEGROUND = 0;
	const PVP_TYPE_ARENA = 1;
	
	static function listByBestRating()
	{		
		$query = Core::$DB->query("
		SELECT 
			`name`,
			`battleground_rating`
		FROM 
			`players`
		ORDER BY 
			`battleground_rating`
			DESC
		LIMIT 5");
		
		return $query;
	}
}