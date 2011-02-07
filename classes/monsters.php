<?php
define('LOT_RATE', 2);

class Monsters
{
	private $db, $monsterList, $monster, $list;
	static private $_instance;

	function __construct()
	{
		global $db;
		$this->db = $db;
		
		$this->monsterList = new DOMDocument();
		
		if(file_exists(DIR_DATA.MONSTERS_FILE) || file_exists(DIR_DATA."world/-spawn.xml"))
		{
			$this->monsterList->load(DIR_DATA.MONSTERS_FILE);
			
			$this->list = new ArrayObject();
			
			$list = $this->monsterList->getElementsByTagName("monster");
			for($x = 0; $x < $list->length; $x++)
			{
				$patch = $list->item($x)->getAttribute("file");
				$name = $list->item($x)->getAttribute("name");
				if($patch && $name)
				{
					list($cat, $file) = explode("/", $patch);
					
					$array = array();
					$array["file"] = $file;
					$array["category"] = $cat;
					$array["patch"] = $patch;
					
					$this->list->offsetSet(strtolower($name), $array);
				}
			}			
		}
		else
			die("Banco de dados de monstros não localizado.");	
	}
	
	static function GetInstance()
	{
		if(self::$_instance)
		{
			return self::$_instance;
		}
		
		$class = __CLASS__;
		self::$_instance = new $class;
		return self::$_instance;
	}	
	
	function getListAsSelect()
	{
		$select = new HTML_SelectBox();
		$select->SetName("category");
		$select->onChangeSubmit();
		
		$it = $this->list->getIterator();
		
		$added = array();
		
		$i = 0;
		while($it->valid())
		{
			$info = $it->current();
			
			if(in_array($info["category"], $added))
			{
				$it->next();
				continue;
			}
			
			$added[] = $info["category"];			
			$select->AddOption($info["category"]);
			
			if(isset($_GET["category"]) && isset($info["category"]) && $info["category"] == $_GET["category"])
			{
				$select->SelectedIndex($i);
			}
			
			$i++;
		}
		
		return $select;
	}
	
	function getList()
	{
		return $this->list;
	}	
	
	function loadByName($name)
	{
		if(!$this->list->offsetExists(strtolower($name)))
		{
			return false;
		}
		
		$info = $this->list->offsetGet(strtolower($name));
		
		$this->monster = new DOMDocument();
		$this->monster->load(DIR_DATA."monster/".$info["patch"]);
		$this->loadMonsterInfo();
		return true;
	}
	
	private $name, $experience, $manacost;
	private $healthnow, $healthmax;
	private $looktype, $looktypeex;
	private $isSummonable = false, $isAttackable = false, $isHostile = true, $isIllusionable = false, $isConviceable = false, $ruOnHealth = 0;
	private $attacks, $healingmax = 0, $healingfreq = 0;
	private $physicalPercent = 0, $energyPercent = 0, $earthPercent = 0, $icePercent = 0, $holyPercent = 0, $deathPercent = 0;
	public $immunityPhysical = false, 
			$immunityEnergy = false, 
			$immunityFire = false, 
			$immunityEarth = false, 
			$immunityDrown = false,
			$immunityIce = false,
			$immunityHoly = false,
			$immunityDeath = false,
			$immunityLifeDrain = false,
			$immunityParalize = false,
			$immunityOutfit = false,
			$immunityDrunk = false,
			$immunityInvisible = false;
			
	private $maxSummons, $summons;
	private $voices;
	private $lotMap;
		
	function loadMonsterInfo()
	{
		$this->name = $this->monster->getElementsByTagName("monster")->item(0)->getAttribute("name");
		$this->experience = $this->monster->getElementsByTagName("monster")->item(0)->getAttribute("experience");
		$this->manacost = $this->monster->getElementsByTagName("monster")->item(0)->getAttribute("manacost");
		
		$this->healthnow = $this->monster->getElementsByTagName("health")->item(0)->getAttribute("now");
		$this->healthmax = $this->monster->getElementsByTagName("health")->item(0)->getAttribute("max");
		
		$this->looktype = $this->monster->getElementsByTagName("look")->item(0)->getAttribute("type");
		$this->looktypeex = $this->monster->getElementsByTagName("look")->item(0)->getAttribute("typeex");
		
		//loading flags
		$nodeList = $this->monster->getElementsByTagName("flag");
		
		for($i = 0; $i < $nodeList->length; $i++)
		{
			if($nodeList->item($i)->getAttribute("summonable") == 1)
				$this->isSummonable = true;	
				
			if($nodeList->item($i)->getAttribute("attackable") == 1)	
				$this->isAttackable = true;
				
			if($nodeList->item($i)->getAttribute("hostile") == 1)	
				$this->isHostile = true;
				
			if($nodeList->item($i)->getAttribute("illusionable") == 1)	
				$this->isIllusionable = true;
				
			if($nodeList->item($i)->getAttribute("convinceable") == 1)	
				$this->isConviceable = true;
				
			if($nodeList->item($i)->getAttribute("runonhealth") > 0)	
				$this->isConviceable = $nodeList->item($i)->getAttribute("runonhealth");
		}
		
		//reading attacks
		$nodeList = $this->monster->getElementsByTagName("attack");
		$this->attacks = new ArrayObject();
		
		for($i = 0; $i < $nodeList->length; $i++)
		{
			$attack = new ArrayObject();
			
			$value = $nodeList->item($i)->getAttribute("min");
			if($value)
				$attack->offsetSet("min", $value);
			
			$value = $nodeList->item($i)->getAttribute("max");
			if($value)
				$attack->offsetSet("max", $value);		
			
			$value = $nodeList->item($i)->getAttribute("interval");
			if($value)
				$attack->offsetSet("interval", $value);				
				
			$attackName = $nodeList->item($i)->getAttribute("name");
			if($attackName == "melee")
			{
				$meele = new ArrayObject();
				
				$value = $nodeList->item($i)->getAttribute("skill");
				if($value)
					$meele->offsetSet("skill", $value);
					
				$value = $nodeList->item($i)->getAttribute("attack");
				if($value)
					$meele->offsetSet("attack", $value);
					
				$attack->offsetSet("melee", $meele);
			}		
			
			$this->attacks->append($attack);
		}	
		
		$chance = 0;
		
		//reading defenses
		$nodeList = $this->monster->getElementsByTagName("defense");
		for($i = 0; $i < $nodeList->length; $i++)
		{
			$defName = $nodeList->item($i)->getAttribute("name");	
			
			if($defName == "healing")
			{
				$value = $nodeList->item($i)->getAttribute("max");
				
				if($value)
				{
					$interval = $nodeList->item($i)->getAttribute("interval");
					
					if($interval)
					{
						if($interval < 2000)
						{
							$m = 2000 / $interval;
							$value = ceil($value / $m);
						}
						elseif($interval > 2000)
						{
							$m = $interval / 2000;
							$value = ceil($value * $m);
						}						
					}
					
					$this->healingmax += $value;
				}
					
				$value = $nodeList->item($i)->getAttribute("chance");
				
				if($value)
					$chance += $value;
			}
		}

		$this->healingfreq = $chance;
		
		//reading elements
		$nodeList = $this->monster->getElementsByTagName("element");
		for($i = 0; $i < $nodeList->length; $i++)
		{
			$value = $nodeList->item($i)->getAttribute("physicalPercent");
			if($value)
				$this->physicalPercent = $value;
				
			$value = $nodeList->item($i)->getAttribute("energyPercent");
			if($value)
				$this->energyPercent = $value;
				
			$value = $nodeList->item($i)->getAttribute("earthPercent");
			if($value)
				$this->earthPercent = $value;
				
			$value = $nodeList->item($i)->getAttribute("icePercent");
			if($value)
				$this->icePercent = $value;
				
			$value = $nodeList->item($i)->getAttribute("holyPercent");
			if($value)
				$this->holyPercent = $value;
				
			$value = $nodeList->item($i)->getAttribute("deathPercent");
			if($value)
				$this->deathPercent = $value;
		}
		
		//reading immunities
		$nodeList = $this->monster->getElementsByTagName("immunity");
		for($i = 0; $i < $nodeList->length; $i++)		
		{
			$value = $nodeList->item($i)->getAttribute("name");
			
			if($value)
			{
				if($value == "physical")
					$this->immunityPhysical = true;			
				if($value == "energy")
					$this->immunityEnergy = true;			
				if($value == "fire")
					$this->immunityFire = true;			
				if($value == "poison" || $value == "earth")
					$this->immunityEarth = true;			
				if($value == "drown")
					$this->immunityDrown = true;			
				if($value == "ice")
					$this->immunityIce = true;			
				if($value == "holy")
					$this->immunityHoly = true;					
				if($value == "death")
					$this->immunityDeath = true;			
				if($value == "lifedrain")
					$this->immunityLifeDrain = true;			
				if($value == "paralyze")
					$this->immunityParalize = true;			
				if($value == "outfit")
					$this->immunityOutfit = true;			
				if($value == "drunk")
					$this->immunityDrunk = true;			
				if($value == "invisible")
					$this->immunityInvisible = true;			
			}
			else
			{
				$value = $nodeList->item($i)->getAttribute("physical");
				if($value) { $this->immunityPhysical = true; }
				
				$value = $nodeList->item($i)->getAttribute("energy");
				if($value) { $this->immunityEnergy = true; }
				
				$value = $nodeList->item($i)->getAttribute("fire");
				if($value) { $this->immunityFire = true; }

				if($nodeList->item($i)->getAttribute("poison") || $nodeList->item($i)->getAttribute("earth")) { $this->immunityEarth = true; }
			
				$value = $nodeList->item($i)->getAttribute("drown");
				if($value) { $this->immunityDrown = true; }			
			
				$value = $nodeList->item($i)->getAttribute("ice");
				if($value) { $this->immunityIce = true; }			
			
				$value = $nodeList->item($i)->getAttribute("holy");
				if($value) { $this->immunityHoly = true; }			
			
				$value = $nodeList->item($i)->getAttribute("death");
				if($value) { $this->immunityDeath = true; }			
			
				$value = $nodeList->item($i)->getAttribute("lifedrain");
				if($value) { $this->immunityLifeDrain = true; }			
			
				$value = $nodeList->item($i)->getAttribute("paralyze");
				if($value) { $this->immunityParalize = true; }			
			
				$value = $nodeList->item($i)->getAttribute("outfit");
				if($value) { $this->immunityOutfit = true; }			
			
				$value = $nodeList->item($i)->getAttribute("drunk");
				if($value) { $this->immunityDrunk = true; }					
			
				$value = $nodeList->item($i)->getAttribute("invisible");
				if($value) { $this->immunityInvisible = true; }			
			}
		}
		
		$this->summons = new ArrayObject();
		
		$value = $this->monster->getElementsByTagName("summons");
		if($value->length > 0) { $this->maxSummons = $value->item(0)->getAttribute("maxSummons"); }
		
		//reading summons
		$nodeList = $this->monster->getElementsByTagName("summon");
		for($i = 0; $i < $nodeList->length; $i++)		
		{
			$value = $nodeList->item($i)->getAttribute("name");
			if($value) { $this->summons->append($value); }		
		}		
		
		$this->voices = new ArrayObject();
		
		//reading voices
		$nodeList = $this->monster->getElementsByTagName("voice");
		for($i = 0; $i < $nodeList->length; $i++)		
		{
			$value = $nodeList->item($i)->getAttribute("sentence");
			if($value) { $this->voices->append($value); }		
		}	

		$this->lotMap = new ArrayObject();
		
		//reading lots
		$nodeList = $this->monster->getElementsByTagName("item");		
		for($i = 0; $i < $nodeList->length; $i++)		
		{
			$lot = new ArrayObject();
			
			$value = $nodeList->item($i)->getAttribute("id");
			if($value) { $lot->offsetSet("id", $value); }				
			
			$value = $nodeList->item($i)->getAttribute("countmax");
			if($value) { $lot->offsetSet("countmax", $value); }				
			
			$value = ($nodeList->item($i)->getAttribute("chance")) ? $nodeList->item($i)->getAttribute("chance") : $nodeList->item($i)->getAttribute("chance1");
			if($value) 
			{ $lot->offsetSet("chance", $value); }	

			$this->lotMap->append($lot);
		}				
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function getExperience()
	{
		return $this->experience;
	}
	
	function getManaCost()
	{
		if($this->isSummonable)
			return $this->manacost;
	
		return false;		
	}
	
	function getHealthNow()
	{
		return $this->healthnow;
	}
	
	function getHealthMax()
	{
		return $this->healthmax;
	}
	
	function getLookType()
	{
		return $this->looktype;
	}
	
	function getLookItem()
	{
		return $this->looktypeex;
	}
	
	function lookIsType()
	{
		return ($this->getLookType() != 0) ? true : false;
	}
	
	function isSummonable()
	{
		return $this->isSummonable;
	}
	
	function isAttackable()
	{
		return $this->isAttackable;
	}
	
	function isHostile()
	{
		return $this->isHostile;
	}
	
	function isConvinceable()
	{
		return $this->isConviceable;
	}
	
	function isIllusionable()
	{
		return $this->isIllusionable;
	}
	
	function getMaxMeeleDamage($skill, $attack)
	{
		$calc = -ceil(($skill * ($attack * 0.05)) + ($attack * 0.5));
		//echo "Calc: {$calc}<br>";
		return $calc;
	}
	
	function getMaxHealing()
	{
		return $this->healingmax;
	}
	
	function getHealingFreq()
	{
		return $this->healingfreq;
	}
	
	function getMaxDamage()
	{
		$iterator = $this->attacks->getIterator();
		
		//print_r($this->attacks);
		
		$iterator instanceof ArrayIterator;
		$damagemax = 0;
		while($iterator->valid())
		{
			$attack = $iterator->current();
			$attack instanceof ArrayObject;
			
			$damage = ($attack->offsetExists("max")) ?  $attack->offsetGet("max") : 0;
		
			if($attack->offsetExists("melee"))
			{
				$meele = $attack->offsetGet("melee");	
				$meele instanceof ArrayObject;
				
				$damage += $this->getMaxMeeleDamage($meele->offsetGet("skill"), $meele->offsetGet("attack")); 
			}
			
			$interval = $attack->offsetGet("interval");
			
			if($interval < 2000)
			{
				$m = 2000 / $interval;
				$damage = ceil($damage * $m);
			}
			elseif($interval > 2000)
			{
				$m = $interval / 2000;
				$damage = ceil($damage / $m);
			}
			
			$damagemax += $damage;
			
			$iterator->next();
		}
		
		return $damagemax;
	}
	
	function getPhysicalPercent()
	{
		return $this->physicalPercent;
	}
	
	function getIcePercent()
	{
		return $this->icePercent;
	}
	
	function getEnergyPercent()
	{
		return $this->energyPercent;
	}
	
	function getEarthPercent()
	{
		return $this->earthPercent;
	}
	
	function getHolyPercent()
	{
		return $this->holyPercent;
	}
	
	function getDeathPercent()
	{
		return $this->deathPercent;
	}
	
	function getMaxSummons()
	{
		return $this->maxSummons;
	}
	
	function getSummons()
	{
		return $this->summons;
	}
	
	function getVoices()
	{
		return $this->voices;
	}
	
	function getLotMap()
	{
		return $this->lotMap;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>
