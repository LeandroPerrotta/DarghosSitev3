<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;

class Misc
{
	function ping()
	{
		if(Configs::Get(Configs::eConf()->STATUS_SHOW_PING))
		{
			//TODO: Precisa ser re-implementado se for voltar a usar.
			/*
				$query = \Core\Main::$DB->query("INSERT INTO `wb_pingtest` VALUES ('{$_POST['pingavg']}', '{$_SERVER['REMOTE_ADDR']}', '".time()."')");
				
			if(!$query)
				echo "mysql_error";
			else
				echo $_POST['value'];
			*/
		}
	}
	
	function Language(){
	    if(!empty($_POST["language"]))
	        setcookie("language", $_POST["language"]);
	    
        \Core\Main::redirect("?ref=news.last");
	}
	
	function Searchitembyname()
	{
		\Core\Main::$isAjax = true;
		
		if(empty($_POST["value"]) || strlen($_POST["value"]) < 3)
			return "";
		
		$limit = 100;
		
		$result = \Framework\Items::LoadByName($_POST["value"]);
		
		$string = "";
			
		$string .= '
		<script>
		$(".requestItemInfo").on({mouseenter: requestItemInfo, mouseleave: ereaseItemInfo});
		$(".requestItemInfo").on({
		mousemove: function(e){
		$("#iteminfo").css("left", (e.pageX + 3) + "px");
		$("#iteminfo").css("top", (e.pageY + 3) + "px");
		}
		});
		</script>
		';		
		
		if(!$result)
		{
			return "";
		}
		elseif($result instanceof \Framework\Item)
		{
			$string .= "<span onclick='fillSearchBox(\"{$result->GetId()}\")' id='item_{$attr->id}' class='requestItemInfo'><img src='files/items/{$attr->id}.gif'/> {$result->GetName()}</span>";
		}
		else
		{					
			$i = 0;
			foreach($result as $element)
			{
				
				if($element)
				{
					$attr = $element->attributes();
					$string .= "<span onclick='fillSearchBox(\"{$attr->id}\")' id='item_{$attr->id}' class='requestItemInfo'><img src='files/items/{$attr->id}.gif'/> {$attr->name}</span>";
				}
				$i++;
				
				if($i > $limit)
					break;
			}
		}
		
		return $string;
			
	}
	
	function mapMarks(){
	    
	    $floor = (int)$_POST["floor"];
	    
	    \Core\Main::$isAjax = true;
	    
	    $list = array();
	    
	    if($floor == 7){   
    	    
    	    $mark = new \stdClass();
    	    $mark->x = 1982;
    	    $mark->y = 1841;
    	    $mark->z = 7;
    	    $mark->type = "mark-correct";
    	    
    	    array_push($list, $mark);
	    }
	    	    
	    if(count($list) >= 1)
	        return json_encode($list);
	    else
	        return "";
	}
	
	function Iteminfo()
	{
		\Core\Main::$isAjax = true;
		
		list(,$itemtype) = explode("_", $_POST["itemtype"]);
		(int)$itemtype;
		
		$item = \Framework\Items::LoadById($itemtype);
		if(!$item)
			return "<p>Item not found</p>";
		
		$temp = $item->GetTransformableItem();
		
		if($temp)
			$item = $temp;
		
		$string = "
			<h3>{$item->GetName()}
		";
		
		if($item->description)
			$string .= "<p>{$item->description}</p>";
		
		$string .= "</h3>";

		if($item->attack || $item->defense)
		{
			$isAmmo = false;
			
			$type_str = "";
			$type_name = "Tipo de arma";
			$effect_type = "";
			$defense_str = $item->defense;
			$attack_str = $item->attack;
			
			if($item->weaponType == "distance")
			{			
				$type_str = "a distancia";
				
				if($item->ammoType == "bolt")
				{
					$isAmmo = true;
					$type_str .= " (besta), 2 m??os";
				}
				elseif($item->ammoType == "arrow")
				{
					$isAmmo = true;
					$type_str .= " (arco), 2 m??os";
				}
				else
				{
					$type_str .= " 1 m??o";
				}
			}
			elseif($item->weaponType == "shield")
			{
				$type_name = "Tipo de equipamento";
				$type_str = "Escudo";
			}
			elseif($item->weaponType == "ammunition" || $item->weaponType == "ammo")
			{
				$type_name = "Tipo de muni????o";
				
				if($item->ammoType == "bolt")
					$type_str = "para bestas";
				if($item->ammoType == "arrow")
					$type_str = "para arcos";		
			}
			else
			{
				if($item->weaponType == "axe")
					$type_str .= "machado";
				elseif($item->weaponType == "sword")
					$type_str .= "espada";
				elseif($item->weaponType == "club")
					$type_str .= "martelo";
				elseif($item->weaponType == "wand")
					$type_str .= "vara magica";
				elseif($item->weaponType == "rod")
					$type_str .= "cajado magico";
				
				if($item->slotType == "two-handed")
					$type_str .= " de duas m??os";
				else
					$type_str .= " de uma m??o";
			}
			

			$string .= "<p>{$type_name}: <strong>{$type_str}</strong></p>";
			
			if($item->range)
				$string .= "<p>Distancia do alvo: <strong>{$item->range} sqm</strong></p>";	
			
			if($item->attack)
			{
				$effect_type = "Ataque";
				if($isAmmo)
					$effect_type = "Ataque extra";
				
				$string .= "<p>{$effect_type}: <strong>{$attack_str}</strong></p>";
				
				if($item->attackSpeed)
					$string .= "<p>Velocidade do ataque: <strong>" . round($item->attackSpeed / 1000, 2) . "s</strong></p>";
				else
					$string .= "<p>Velocidade do ataque: <strong>1.5s</strong></p>";
				
				if($item->hitChance)
					$string .= "<p>Chance bonus: <strong>+{$item->hitChance}%</strong></p>";
			}
			
			if($item->defense)
			{
				$effect_type = "Defesa";
				
				if($item->extradefense)
					$defense_str .= " + ({$item->extradefense})";
					
				$string .= "<p>{$effect_type}: <strong>{$defense_str}</strong></p>";
			}
		}
		
		if($item->armor)
		{
			$string .= "<p>Tipo de equipamento: <strong>armadura</strong></p>";
			$string .= "<p>Prote????o: <strong>{$item->armor}</strong></p>";
		}

		if($item->speed)
			$string .= "<p>Velocidade: <strong>+{$item->speed} leveis</strong></p>";
		
		if($item->criticalChance)
			$string .= "<p>Critico: <strong>+{$item->criticalChance}%</strong></p>";		
		
		if($item->resil)
		    $string .= "<p>Resili??ncia PvP: <strong>+{$item->resil}%</strong></p>";		
		
		if($item->weight)
			$string .= "<p>Peso: <strong>".round($item->weight / 100, 2)." oz</strong></p>";
		
		if($item->skillAxe)
			$string .= "<p>Skill axe: <strong>+{$item->skillAxe}</strong></p>";
		
		if($item->skillClub)
			$string .= "<p>Skill club: <strong>+{$item->skillClub}</strong></p>";
		
		if($item->skillSword)
			$string .= "<p>Skill sword: <strong>+{$item->skillSword}</strong></p>";		
		
		if($item->skillDist)
			$string .= "<p>Skill distance: <strong>+{$item->skillDist}</strong></p>";
		
		if($item->skillShield)
			$string .= "<p>Skill shield: <strong>+{$item->skillShield}</strong></p>";
		
		if($item->magicLevelPoints)
			$string .= "<p>Magic level: <strong>+{$item->magicLevelPoints}</strong></p>";
		
		
		if($item->manaGain)
			$string .= "<p>Regenera????o (mana): <strong>+{$item->manaGain}/".(floor($item->manaTicks / 1000))."s</strong></p>";
		
		if($item->healthGain)
			$string .= "<p>Regenera????o (life): <strong>+{$item->healthGain}/".(floor($item->healthTicks / 1000))."s</strong></p>";
		
		if($item->maxHealthPoints)
			$string .= "<p>Pontos de vida: <strong>+{$item->maxHealthPoints}</strong></p>";
		
		if($item->duration)
		{
			$string .= "<p>Dura????o: <strong>".($item->duration / 60)." minutos</strong></p>";
		}		
		
		if($item->absorbPercentDeath || $item->absorbPercentDrown || $item->absorbPercentEnergy || $item->absorbPercentFire || $item->absorbPercentHoly || $item->absorbPercentIce
			|| $item->absorbPercentPhysical	|| $item->absorbPercentEarth)
		{
			$string .= "<p class='spaced'>Resistencias & Fraquezas:</p>";
			
			if($item->absorbPercentDeath)
				$string .= "<p>Death: <strong>{$item->absorbPercentDeath}%</strong></p>";
			
			if($item->absorbPercentDrown)
				$string .= "<p>Em baixo d'agua: <strong>{$item->absorbPercentDrown}%</strong></p>";		
				
			if($item->absorbPercentEnergy)
				$string .= "<p>Energy: <strong>{$item->absorbPercentEnergy}%</strong></p>";	
			
			if($item->absorbPercentFire)
				$string .= "<p>Fire: <strong>{$item->absorbPercentFire}%</strong></p>";
			
			if($item->absorbPercentEarth)
				$string .= "<p>Earth: <strong>{$item->absorbPercentEarth}%</strong></p>";		
			
			if($item->absorbPercentHoly)
				$string .= "<p>Holy: <strong>{$item->absorbPercentHoly}%</strong></p>";
			
			if($item->absorbPercentIce)
				$string .= "<p>Ice: <strong>{$item->absorbPercentIce}%</strong></p>";
			
			if($item->absorbPercentPhysical)
				$string .= "<p>Fisico: <strong>{$item->absorbPercentPhysical}%</strong></p>";			
		}
		
		$bonus = $item->GetGearBonus();
		if($bonus)
		{
			$string .= "<p class='spaced'>Set B??nus:</p>";
			$string .= "<p>Parte do <strong>{$bonus["name"]}</strong></p>";
			
			if($bonus["2pieces"])
			{
				$string .= "<p>Duas partes:</p>";
				foreach($bonus["2pieces"] as $str)
					$string .= "<p class='small'> ??? {$str}</p>";
			}
			
			if($bonus["3pieces"])
			{
				$string .= "<p>Tr??s partes:</p>";
				foreach($bonus["3pieces"] as $str)
					$string .= "<p class='small'> ??? {$str}</p>";
			}			
		}
		
		return $string;
	}
}
?>