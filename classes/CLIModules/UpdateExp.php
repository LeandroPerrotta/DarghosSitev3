<?php 
namespace CLIModules;

use \Framework\Guilds;
use \Framework\Guilds\Rank;
use \Framework\Guilds\War;

class UpdateExp
{
	private $db;
	
	function __construct()
	{
		$this->db = &\Core\Main::$DB;
	}
	
	function NormalizeExp($exp)
	{
		$_expTable = array(
			array("to" => 917800, "multipler" => 20) 			//level 0-39
			,array("to" => 7915800, "multipler" => 15) 			//level 40-79
			,array("to" => 15694800, "multipler" => 10) 		//level 80-99
			,array("to" => 27393800, "multipler" => 8) 			//level 100-119
			,array("to" => 43812800, "multipler" => 6) 			//level 120-139
			,array("to" => 65751800, "multipler" => 4) 			//level 140-159
			,array("to" => 94010800, "multipler" => 3) 			//level 160-179
			,array("to" => 129389800, "multipler" => 2) 		//level 180-199
			,array("to" => 224707800, "multipler" => 1.5) 		//level 200-239
			,array("multipler" => 1) 							//level 240+
		);
		
		/*
		 * Para normalizar a exp (isto é, trazer a exp do player de volta ao 1x, como se ele tivesse caçado a todo instante em 1x)
		 * nos iremos converter a exp que ele tem em cada stage com a seguinte formula: exp / multipler, removendo a exp do restante.
		 * Se ainda restar exp então passaremos ao proximo stage.
		 */
		
		$_expCheck = 0;
		$realExp = 0;
		
		while($exp > 0)
		{
			$stage = $_expTable[$_expCheck];
			
			if(!isset($stage["to"]))
			{
				$realExp += $exp;
				$exp -= $exp;
			}
			
			if($stage["to"] > $exp)
			{
				$realExp += floor($exp / $stage["multipler"]);
				$exp -= $exp;
			}
			else
			{
				$realExp += floor($stage["to"] / $stage["multipler"]);
				$exp -= $stage["to"];		
			}
			
			$_expCheck++;
		}
		
		return $realExp;
	}
	
	function UpdateExp($exp)
	{		
		$_expTable = array(
				array("to" => 15694800, "multipler" => 100) 		//level 0-99
				,array("to" => 27393800, "multipler" => 50) 		//level 100-119
				,array("to" => 43812800, "multipler" => 25) 		//level 120-139
				,array("to" => 65751800, "multipler" => 15) 		//level 140-159
				,array("to" => 94010800, "multipler" => 10)			//level 160-179
				,array("to" => 129389800, "multipler" => 8)			//level 180-199
				,array("to" => 172688800, "multipler" => 6) 		//level 200-219
				,array("to" => 224707800, "multipler" => 5) 		//level 220-239
				,array("to" => 354255400, "multipler" => 4) 		//level 240-279
				,array("to" => 535983800, "multipler" => 3) 		//level 280-319
				,array("to" => 764741800, "multipler" => 2.5) 		//level 320-359
				,array("to" => 1050779800, "multipler" => 2) 		//level 360-399
				,array("to" => 1400497800, "multipler" => 1.5) 		//level 400-439
				,array("multipler" => 1) 							//level 440+
		);

		/*
		 * Aqui, no enfim iremos converter uma exp normalizada (1x) para o equivalente nos stages do Darghos (Ordon).
		 */
		
		$newExp = 0;
		$finished = false;
		$_expCheck = 0;
		
		while($exp > 0)
		{
			$stage = $_expTable[$_expCheck];
			
			if(!isset($stage["to"]))
			{
				$newExp += $exp;
				$exp -= $exp;
			}
			
			$normalExpStage = floor($stage["to"] / $stage["multipler"]);
			
			if($exp > $normalExpStage)
			{
				$newExp += $stage["to"] * $stage["multipler"];
				$exp -= $normalExpStage;
			}
			else
			{
				$newExp += $exp * $stage["multipler"];
				$exp -= $exp;
			}
			
			$_expCheck++;
		}
		
		return $newExp;
	}
	
	function Run()
	{
		$query = $this->db->query("SELECT `id`, `name`, `experience` FROM `darghos_bkp`.`players` WHERE `level` > 20 AND `world_id` = '1'");
		
		while($player = $query->fetch())
		{
			$realExp = $this->NormalizeExp($player->experience);
			$newExp = $this->UpdateExp($realExp);
			
			echo "{[$player->name}] Aaragon Exp: {$player->experience}, Normalized Exp: {$realExp}, New Exp: {$newExp}\n";
		}
	}
}
?>