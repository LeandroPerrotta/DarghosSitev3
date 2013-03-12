<?php
/*
 * Script construido para analisar 2 monsters.xml e identificar diferenças
 */

$monsters_patches = array(

	"/home/leandro/darghos/global/monster/monsters.xml"
	,"/home/leandro/otserv/tfs/global/data/monster/monsters.xml"
);

$root_one = new SimpleXMLIterator($monsters_patches[0], null, true);
$root_two = new SimpleXMLIterator($monsters_patches[1], null, true);

$one_names = array();

for($root_one->rewind(); $root_one->valid(); $root_one->next())
{
	$node = $root_one->current();
	$node instanceof SimpleXMLIterator;
	$attrs = $node->attributes();
	
	$one_names[] = (string)$attrs["name"];
	
}

for($root_two->rewind(); $root_two->valid(); $root_two->next())
{
	$node = $root_two->current();
	
	if(in_array($node["name"], $one_names))
	{
		continue;
	}
	
	echo "Monster name <b>{$node["name"]}</b> in <b>{$node["file"]}</b> not found.<br>";
}


