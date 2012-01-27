<?php
namespace Framework\Battlegrounds;

class Match
{
	const
		TEAM_ONE = 1
		,TEAM_TWO = 2
		;
	
	public
		$id
		,$begin
		,$end
		,$finishBy
		;
		
	/*
	 * Teams Structure
	 *	
	 * Team
	 * 	- points
	 * 	- playerList
	 * 		- pĺayer_id
	 * 		- ip_address
	 * 		- frags
	 * 		- deaths
	 * 		- deserter
 	 * 
	 */	
	public
		$teams;
		
	function __construct($id)
	{
		$this->id = $id;
	}
	
	function createTeams($team1_points, $team2_points)
	{
		$this->teams[self::TEAM_ONE]["points"] = $team1_points;
		$this->teams[self::TEAM_TWO]["points"] = $team2_points;	
	}
	
	function addPlayer($team, $player_id, $ip_address, $frags, $assists, $deaths, $deserter, $damage, $heal, $gainExp, $gainHonor, $changeRating, $highStamina)
	{
		$this->teams["players"][] = array(
			"player_id" => $player_id
			,"team_id" => $team
			,"ip_address" => $ip_address
			,"frags" => $frags
			,"assists" => $assists
			,"deaths" => $deaths
			,"deserter" => $deserter
			,"damage" => $damage
			,"heal" => $heal
			,"gainExp" => $gainExp
			,"gainHonor" => $gainHonor
			,"changeRating" => $changeRating
			,"highStamina" => $highStamina
		);
	}
	
}