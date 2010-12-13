<?php
$monster = Monsters::GetInstance();
$monster instanceof Monsters;

$it = $monster->getList()->getIterator();
$it instanceof ArrayIterator;

global $module;

$table = "";

while($it->valid())
{
	$info = $it->current();
	$name = $it->key();
	
	if(isset($_GET["category"]) && isset($info["category"]) && $info["category"] == $_GET["category"])
	{
		$monster->loadByName($name);
		
		$img = "files/creatures/{$monster->getLookType()}.gif";
		
		if(!$monster->lookIsType())
		{
			$img = "files/items/{$monster->getLookItem()}.gif";
		}
		
		$table .= "
		<tr>
			<td style='text-align: right; vertical-align: bottom; width: 64px; height: 64px;'><a href='?ref=darghopedia.monster&name={$name}'><img src=\"{$img}\"/></a></td> 
			<td><a href='?ref=darghopedia.monster&name={$name}'>{$name}</a></td> 
			<td>".($monster->getExperience() * EXP_NORMAL)."</td>
		</tr>";	
	}
	
	$it->next();
}

if($table == "")
{
	$table .= "
	<tr>
		<td colspan='3'>Selecione uma categoria acima.</td> 
	</tr>";		
}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="get">
	<fieldset>
		<p>	
			<input type="hidden" name="ref" value="darghopedia.monster"/> 	
			<label for="name">Procurar monstro</label><br />
			<input name="name" value=""/>
			<input id="btNext" class="button" type="submit" value="Procurar" />
		</p>		
		
	</fieldset>
</form>

<form action="'.$_SERVER['REQUEST_URI'].'" method="get">
	<fieldset>
		<p>	
			<input type="hidden" name="ref" value="'.$_GET["ref"].'"/> 	
			<label for="category">Selecione uma categoria</label><br />
			'.$monster->getListAsSelect()->Draw().'
		</p>		
		
	</fieldset>
</form>
	
<table cellspacing="0" cellpadding="0" id="table">
	<tr>
		<th>#</th>
		<th>Nome</th>
		<th>Experience</th>
	</tr>	
'.$table.'
</table>';