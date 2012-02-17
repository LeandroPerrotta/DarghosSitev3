<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>

<?php
/*
 * Script construido para analisar 2 monsters.xml e identificar diferenças
 */

$__items = array();
$__items["ring of healing"] = 2214;
$__items["stealth ring"] = 2165;
$__items["orb"] = 2176;
$__items["demon trophy"] = 7393;
$__items["clerical mace"] = 2423;
$__items["arrow"] = 2544;
$__items["rope"] = 2120;
$__items["lyre"] = 2071;
$__items["lyre"] = 2071;
$__items["war hammer"] = 2391;
$__items["energy ring"] = 2167;
$__items["red dragon scale"] = 5882;
$__items["life crystal"] = 2177;
$__items["dragon slayer"] = 7402;
$__items["party trumpet"] = 6572;
$__items["ice cube"] = 7441;
$__items["death ring"] = 6300;
$__items["strange symbol"] = 2174;
$__items["magic light wand"] = 2162;
$__items["crystal ring"] = 2124;
$__items["torch"] = 2050;
$__items["skull"] = 2229;
$__items["bone"] = 2230;
$__items["voodoo doll"] = 3955;

$items_patch = "/home/leandro/darghos/global/items/items.xml";

function getItemIdByName(SimpleXMLElement &$xml, $itemName)
{
	global $__items;
	
	$result = $xml->xpath("//*[@name=\"".strtolower($itemName)."\"]");
	if(!$result)
	{
		if(isset($__items[strtolower($itemName)]))
		{
			return $__items[strtolower($itemName)];
		}		
		else 
		{
			echo "Warning: Item with name {$itemName} not found!<br>";
			return false;
		}
	}

	if(count($result) > 1)
	{
		if($__items[strtolower($itemName)])
		{
			return $__items[strtolower($itemName)];
		}
		else
		{
			echo "Warning: Item with name {$itemName} has multiple entries in items.xml and is not configured here!<br>";
			return false;
		}
	}
	
	$attr = $result[0]->attributes();
	return $attr["id"];
}

function addLootItem(SimpleXMLElement &$loot, $item_id, $item_chance, $item_countmax = 1)
{
	$item = $loot->addChild("item");
	
	$item->addAttribute("id", $item_id);
	$item->addAttribute("chance", $item_chance);

	if($item_countmax > 1)
		$item->addAttribute("countmax", $item_countmax);
}

if(isset($_POST["monster"]))
{
	$itemsXml = new SimpleXMLElement($items_patch, null, true);
	
	$replace = str_replace(" ", "_", $_POST["monster"]);
	
	$xml = file_get_contents("http://tibia.wikia.com/wiki/Loot_Statistics:{$replace}");
	
	$dom = new DOMDocument();
	@$dom->loadHTML($xml);
	
	
	$elements = $dom->getElementsByTagName("table");
	$xpath = new DOMXPath($dom);
	/*$elements = $xpath->query("//*[@id='sortable_table_id_0']", $dom);*/
	
	$loot = new SimpleXMLElement("<loot></loot>");
	
	if(!$elements)
	{
		echo "Not found!";
	}
	else
	{	
		$element = $elements->item(1);
	
		$entries = $xpath->query("tr", $element);		
		
		if(!$entries)
		{
			echo "Not found xpath!<br>";
			continue;
		}
		
		foreach($entries as $entry)
		{
			$tds = $entry->childNodes;			
			
			if($tds && $tds->length > 5)
			{				
				$amount = $tds->item(2);
				$name = $tds->item(4);
				$times = $tds->item(6);
				$totalAmount = $tds->item(8);
				$percentage = $tds->item(10);
				$kills2Get = $tds->item(12);
					
				if($amount->nodeName != "td")
					continue;
				
				$id = getItemIdByName($itemsXml, $name->nodeValue);
				
				/*
				$str = "";
				$str .= "Item | {$name->nodeValue} ({$id})<br>";
				$str .= "--- Amount | {$amount->nodeValue}<br>";
				$str .= "--- Times | {$times->nodeValue}<br>";
				$str .= "--- Each | {$kills2Get->nodeValue}<br>";
				$str .= "--- % | {$percentage->nodeValue}<br>";
				$str .= "<br>";
				
				print($str);
				*/
				
				if($id)
				{				
					$chance = floor(100000 / (int)$kills2Get->nodeValue);
					
					if($amount->nodeValue != "1")
					{
						if(is_numeric($amount->nodeValue))
							$countmax = (int)$amount->nodeValue;
						else
							list(, $countmax) = explode("-", $amount->nodeValue);
						
						if((int)$countmax <= 100)					
							addLootItem($loot, $id, $chance, $countmax);
						else
						{
							$temp = $countmax;
							while($temp > 0)
							{
								$temp_count = min($temp, 100);
								
								addLootItem($loot, $id, $chance, $temp_count);
								$temp -= $temp_count;
							}
						}
					}
					else
					{
						addLootItem($loot, $id, $chance);
					}
				}
			}			
		}
	}
	
	print("<textarea cols='120' rows='20'>{$loot->asXML()}</textarea>");
}
?>

<form method="POST">
	Monstro: <input type="text" name="monster"></input>
	<input type="submit" name="submit"/>
</form>

</body>
</html>
