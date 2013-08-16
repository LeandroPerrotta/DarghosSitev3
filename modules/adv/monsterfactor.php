<?php
$monster = \Framework\Monsters::GetInstance();
$monster instanceof \Framework\Monsters;

$it = $monster->getList()->getIterator();
$it instanceof ArrayIterator;

global $module;

$dataPatch = \Core\Configs::Get(\Core\Configs::eConf()->PATCH_SERVER) . \Core\Configs::Get(\Core\Configs::eConf()->FOLDER_DATA);
$respawns_patch = $dataPatch . "world/-spawn.xml";
$spawns_list = array();
$total_spawns = 0;

if(file_exists($respawns_patch)){
   $spawns = new \DOMDocument();
   
   $spawns->load($respawns_patch);
   
   $list = $spawns->getElementsByTagName("monster");
   for($x = 0; $x < $list->length; $x++)
   {
       $name = $list->item($x)->getAttribute("name");     
       $spawns_list[strtolower($name)]++;
       $total_spawns++;
   }
}

$table = "";

$list = array();

while($it->valid())
{
	$info = $it->current();
	$name = $it->key();
	
	if(isset($spawns_list[strtolower($name)])){
    	$monster->loadByName($name);
    	
    	if($monster->getExperience() > 600){
    
        	$spawns_count = $spawns_list[strtolower($name)];
        	$powerFactor = number_format($monster->getExperience() / $monster->getHealthMax(), 2);
        	$attackFactor = number_format($monster->getExperience() / abs($monster->getMaxDamage()), 2);
        	
        	$data = array(
        	    "name" => $name
                ,"factor" => $powerFactor . "/" . $attackFactor
                ,"exp" => $monster->getExperience()
                ,"life" => $monster->getHealthMax()   
                ,"count" => $spawns_count   
            );
        	
        	array_push($list, $data);
    	}
	}
	
	$it->next();
}

$tmp = array();
foreach($list as &$v)
    $tmp[] = &$v["count"];

array_multisort($tmp, $list);

foreach($list as &$data){
    $table .= "
    <tr>
        <td><a href='?ref=darghopedia.monster&name={$data["name"]}'>{$data["name"]}</a></td>
        <td>".$data["factor"]."</td>
        <td>".$data["exp"]."</td>
        <td>".$data["life"]."</td>
        <td>".$data["count"]." (".\Core\Tools::getPercentOf($data["count"], $total_spawns, 2).")</td>
    </tr>";
}

if($table == "")
{
	$table .= "
	<tr>
		<td colspan='3'>Selecione uma categoria acima.</td> 
	</tr>";		
}

$module .= '	
<table cellspacing="0" cellpadding="0" id="table">
    <p>Total Spawns: '.$total_spawns.'</p>

	<tr>
		<th>Nome</th>
		<th>Fator</th>
		<th>Exp</th>
		<th>Vida</th>
		<th>Qtd</th>
	</tr>	
'.$table.'
</table>';