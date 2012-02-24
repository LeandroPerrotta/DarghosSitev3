<?php
namespace Framework;
class Item
{
	private $m_element;
	
	public 
		$skillClub
		,$skillAxe
		,$skillSword
		,$skillDist
		,$skillShield
		,$magicLevelPoints

		,$manaGain
		,$manaTicks
		,$healthGain
		,$healthTicks		
		
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
	
		,$absorbPercentAll
		,$absorbPercentElements
		,$absorbPercentMagic
		,$absorbPercentEnergy
		,$absorbPercentFire
		,$absorbPercentPoison
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
		$keys = array_keys($props);
		return array_search($att, $keys);
	}	
	
	function __construct($element){
		$this->m_element = $element;
		
		foreach($this->m_element[0]->children() as $k => $v)
		{
			$v instanceof \SimpleXMLElement;
			$attr = $v->attributes();
			
			$key = $attr->key;
			
			if($this->__isset($key))
			{
				$this->$key = $attr->value;
			}
		}
	}
	
	function GetId(){ return $this->m_element[0]->attributes()->id; }
	function GetName(){ return $this->m_element[0]->attributes()->name; }	
}