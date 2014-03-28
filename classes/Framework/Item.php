<?php
namespace Framework;
class Item
{
	private $m_element;
	private $m_itemtransform;
	
	public 
		$skillClub
		,$skillAxe
		,$skillSword
		,$skillDist
		,$skillShield
		,$magicLevelPoints
		,$criticalChance
		,$resil

		,$transformEquipTo
		,$stopDuration
		
		,$manaGain
		,$manaTicks
		,$healthGain
		,$healthTicks

		,$maxHealthPoints
		
		,$weaponType
		,$slotType
		,$ammoType		
		
		,$duration
		,$weight
		,$description
		,$speed	
		,$range

		,$armor
		,$attack
		,$defense
		,$extradefense
		,$attackSpeed
		,$hitChance
	
		,$absorbPercentEnergy
		,$absorbPercentFire
		,$absorbPercentEarth
		,$absorbPercentIce
		,$absorbPercentHoly
		,$absorbPercentDeath
		,$absorbPercentLifeDrain
		,$absorbPercentManaDrain
		,$absorbPercentDrown
		,$absorbPercentPhysical
	;
	
	function __isset($att)
	{
		$props = get_object_vars($this);
		foreach($props as $key => $value)
		{
			if(strtolower($key) == strtolower($att))
				return $key;
		}

		return false;
	}	
	
	function __construct($element){
		$this->m_element = $element;
		
		foreach($this->m_element[0]->children() as $k => $v)
		{
			$v instanceof \SimpleXMLElement;
			$attr = $v->attributes();
			
			$key = $attr->key;
			
			if(strtolower($key) == "magicpoints" || strtolower($key) == "magiclevelpoints")
			{
				$this->magicLevelPoints = $attr->value;
			}
			elseif(strtolower($key) == "absorbpercentelements")
			{
				$this->absorbPercentEarth = $attr->value;
				$this->absorbPercentEnergy = $attr->value;
				$this->absorbPercentFire = $attr->value;
				$this->absorbPercentIce = $attr->value;
			}
			elseif(strtolower($key) == "absorbpercentmagic")
			{
				$this->absorbPercentEarth = $attr->value;
				$this->absorbPercentEnergy = $attr->value;
				$this->absorbPercentFire = $attr->value;
				$this->absorbPercentIce = $attr->value;
				$this->absorbPercentHoly = $attr->value;
				$this->absorbPercentDeath = $attr->value;
			}
			elseif(strtolower($key) == "absorbpercentall")
			{
				$this->absorbPercentEarth = $attr->value;
				$this->absorbPercentEnergy = $attr->value;
				$this->absorbPercentFire = $attr->value;
				$this->absorbPercentIce = $attr->value;
				$this->absorbPercentHoly = $attr->value;
				$this->absorbPercentDeath = $attr->value;
				$this->absorbPercentLifeDrain = $attr->value;
				$this->absorbPercentManaDrain = $attr->value;
				$this->absorbPercentDrown = $attr->value;
				$this->absorbPercentPhysical = $attr->value;
				$this->absorbPercent = $attr->value;
			}
			elseif(strtolower($key) == "absorbpercentearth" || strtolower($key) == "absorbpercentpoison")
			{
				$this->absorbPercentEarth = $attr->value;
			}		
			else
			{
				$key = $this->__isset($key);
				if($key)
					$this->$key = $attr->value;
			}
		}
	}
	
	function GetId(){ return $this->m_element[0]->attributes()->id; }
	function GetName(){ return $this->m_element[0]->attributes()->name; }
	
	function GetTransformableItem()
	{
		if($this->stopDuration && $this->transformEquipTo)
		{
			$item = Items::LoadById($this->transformEquipTo);
			$this->m_itemtransform = $item;
			
			return $this->m_itemtransform;
		}
		
		return false;
	}
	
	function GetGearBonus()
	{
		foreach(Items::$_gearBonus as $bonus)
		{
			if(in_array($this->GetId(), $bonus["items"]))
			{
				return $bonus;
			}
		}
		
		return NULL;
	}
}