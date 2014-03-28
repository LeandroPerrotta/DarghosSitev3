<?php
namespace Framework;
use \Core\Configs as g_Configs;;
class Items
{
	static public $_gearBonus = array(
		array("items" => array(12712, 12713, 12714), "name" => "Hot Death Set (Sorcerer's)"
				, "2pieces" => array(
					"Aumenta regeneração de pontos de mana padrão em 50%."
					,"Aumenta a quantidade máxima de pontos de vida em 120."
				)
				, "3pieces" => array(
					"Aumenta a absorção de danos de fogo em 4%."
					,"Aumenta a quantidade máxima de pontos de mana em 325 pontos."
					,"Aumenta a chance de causar dano crítico em 1%."
				)
			),		
			array("items" => array(12715, 12716, 12717), "name" => "Magic Cold Set (Druid's)"
					, "2pieces" => array(
							"Aumenta regeneração de pontos de mana padrão em 50%."
							,"Aumenta a quantidade máxima de pontos de vida em 120."
					)
					, "3pieces" => array(
							"Aumenta a absorção de danos de gelo em 4%."
							,"Aumenta a quantidade máxima de pontos de mana em 325 pontos."
							,"Aumenta a chance de causar dano crítico em 1%."
					)
			),			
			array("items" => array(12718, 12719, 12720), "name" => "Dream Archer's Set (Paladin's)"
					, "2pieces" => array(
							"Aumenta regeneração de pontos de vida padrão em 120%."
							,"Aumenta a chance de causar dano crítico em 1%."
					)
					, "3pieces" => array(
							"Aumenta a absorção de danos de morte em 2%."
							,"Aumenta a chance de causar dano crítico em 2%."
					)
			),			
			array("items" => array(12721, 12722, 12723), "name" => "Heavy Stomper Set (Knight's)"
					, "2pieces" => array(
							"Aumenta a quantidade máxima de pontos de vida em 215."
							,"Buff: A cada dano sofrido existe 1% de chance de durante os proximos 6 segundos reduzir todos os danos sofridos em 50%."
					)
					, "3pieces" => array(
							"Aumenta a quantidade máxima de pontos de vida em 330."
							,"Buff: A cada dano sofrido existe 2% de chance de durante os proximos 8 segundos reduzir todos os danos sofridos em 75%."
					)
			),
	        array("items" => array(12724, 12725, 12726), "name" => "Gladiator's Set (Knight Dano)"
	                , "3pieces" => array(
	                        "Aumenta a chance de seus fierce berserk (exori gran) causarem dano critico em 50%."
	                )
	        ),
	        array("items" => array(12732, 12733, 12734), "name" => "Sacred Set (Knight Defesa)"
	                , "3pieces" => array(
	                        "Seus protector (utamo tempo) também aumentaram em 10% a sua resiliência durante a duração do efeito."
	                )
	        ),
	        array("items" => array(12741, 12742, 12743), "name" => "Demonhunter Set (Paladin)"
	                , "3pieces" => array(
	                        "Suas ethereal spears (exori con) possuem dano e custo de mana aumentados em 450% contra alvos que estiverem com mais de 80% de vida."
	                )
	        ),
	        array("items" => array(12747, 12748, 12749), "name" => "Arcanist Set (Sorcerer)"
	                , "3pieces" => array(
	                        "Diminui o tempo de lançamento da magia hells core (exevo gran mas flam) de 3s para 2s."
	                )
	        ),
	        array("items" => array(12751, 12752, 12753), "name" => "Ancient Nature Set (Sorcerer)"
	                , "3pieces" => array(
	                        "Ao usar a paralyze rune em um alvo, o mesmo fica impossibilitado de remover a paralisia por 2s."
	                        ,"A magia summon creature (utevo res) irá invocar 2 monstros de uma vez e a um custo de mana reduzido em 80%."
	                )
	        ),	                                 	
	);
	
	
	static private $_instance;
	
	static function LoadById($item_id)
	{
		$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA). "items/items.xml";
		$xml = new \SimpleXMLElement($patch, null, true);
		
		$result = $xml->xpath("//*[@id=\"".(int)$item_id."\"]");
		
		if(!$result)
			return false;
		
		$item = new Item($result);
		return $item;
	}
	
	static function LoadByName($item_name)
	{
		$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA). "items/items.xml";
		$xml = new \SimpleXMLElement($patch, null, true);
		
		$result = $xml->xpath("//item[starts-with(@name, \"".strtolower($item_name)."\")]");
		
		if(!$result)
			return false;
		
		if(count($result) == 1)
		{
			$item = new Item($result);
			return $item;			
		}
		else
		{
			return $result;
		}
	}
	
	function __construct()
	{
		$query = \Core\Main::$DB->query("SELECT name FROM ".\Core\Tools::getSiteTable("items")."");
		
		if($query->numRows() != 0)
		{
			return;
		}		
		
		$itemsXML = new \DOMDocument();
		
		$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA)."items/items.xml";
		
		if(file_exists($patch))
		{
			$itemsXML->load($patch);
			
			$nodeList = $itemsXML->getElementsByTagName("item");
			for($x = 0; $x < $nodeList->length; $x++)
			{				
				$id = $nodeList->item($x)->getAttribute("id");
				$name = addslashes($nodeList->item($x)->getAttribute("name"));
				if($id && $name)
				{
					\Core\Main::$DB->query("INSERT INTO ".\Core\Tools::getSiteTable("items")." values ('{$id}', '{$name}')");
				}
			}
		}
		else
			die("Banco de dados necessario não localizado #5400.");	
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

	function getNameById($itemid)
	{
		$item = self::LoadById($itemid);
		if(!$item)
			return false;
		
		return $item->GetName();
	}
}
