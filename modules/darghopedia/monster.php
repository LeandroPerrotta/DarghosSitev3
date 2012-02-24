<?php
class View
{		
	//variables
	private $_message, $monster;	
	
	function View()
	{		
		if(!$_GET['name'])
			return false;		
		
		global $module;
		$module .= "
		<form action='{$_SERVER['REQUEST_URI']} method='get'>
			<fieldset>
				<p>	
					<input type='hidden' name='ref' value='darghopedia.monster'/> 	
					<label for='name'>Procurar monstro</label><br />
					<input name='name' value=''/>
					<input id='btNext' class='button' type='submit' value='Procurar' />
				</p>						
			</fieldset>
		</form>
		";			
			
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}		
		
		if($_POST)
		{
			if(!$this->Post())
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				return true;
			}
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->monster = \Framework\Monsters::GetInstance();
		$this->monster instanceof \Framework\Monsters;
		
		if(!$this->monster->loadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->MONSTER_NOT_FOUND, $_GET['name']);
			return false;			
		}		
		
		return true;
	}
	
	function Post()
	{	
		return true;
	}
	
	function Draw()
	{
		$monster = $this->monster;
		$monster instanceof \Framework\Monsters;
		global $module;
		
		if($monster->lookIsType())
			$img = "files/creatures/{$monster->getLookType()}.gif";
		else
			$img = "files/items/{$monster->getLookItem()}.gif";
		
		/* GERAL AREA */		
		$features = "";	
		if($monster->isAttackable()) $features .= "É atacavel.";
		else $features .= "Não atacavel.";
			
		if($monster->isHostile()) $features .= "<br/>É agressivo.";
		else $features .= "<br/>É passivo.";				
			
		if($monster->isSummonable()) $features .= "<br/>É summonavel: {$monster->getManaCost()} de mana necessarios.";			
		if($monster->isIllusionable()) $features .= "<br/>Jogadores podem imitar sua aparencia.";			
		if($monster->isConvinceable()) $features .= "<br/>Pode ser adestrado para ajudar em caçadas.";			
			
		/* IMMUNITY AREA */	
		$immunity = "";
		if($monster->immunityPhysical){ $immunity .= "Ataques Fisicos<br>"; }
		if($monster->immunityEnergy){ $immunity .= "Ataques de Energia<br>"; }
		if($monster->immunityFire){	$immunity .= "Ataques de Fogo<br>"; }
		if($monster->immunityEarth){ $immunity .= "Ataques de Terra & Veneno<br>"; }
		if($monster->immunityDrown){ $elements .= "Afogamento<br>"; }
		if($monster->immunityIce){ $immunity .= "Ataques de Gelo<br>"; }
		if($monster->immunityHoly){ $immunity .= "Ataques Divinos<br>"; }
		if($monster->immunityDeath){ $immunity .= "Ataques de Morte<br>"; }
		if($monster->immunityLifeDrain){ $immunity .= "Drenagem de vida<br>"; }
		if($monster->immunityOutfit){ $immunity .= "Outfit trocado<br>"; }
		if($monster->immunityParalize){	$immunity .= "Paralização<br>"; }
		if($monster->immunityInvisible){ $immunity .= "Vê inimigos mesmo invisiveis<br>"; }
		if($monster->immunityDrunk){ $immunity .= "Não fica bebado<br>"; }
			
		/* ELEMENTS AREA */
		$elements = "";	
		if($monster->getPhysicalPercent() != 0) { $elements .= "Ataques Fisícos: {$monster->getPhysicalPercent()}%<br>"; }	
		if($monster->getFirePercent() != 0) { $elements .= "Fogo: {$monster->getFirePercent()}%<br>"; }	
		if($monster->getEnergyPercent() != 0) {	$elements .= "Energia: {$monster->getEnergyPercent()}%<br>"; }	
		if($monster->getEarthPercent() != 0) { $elements .= "Terra: {$monster->getEarthPercent()}%<br>"; }	
		if($monster->getIcePercent() != 0) { $elements .= "Gelo: {$monster->getIcePercent()}%<br>"; }	
		if($monster->getHolyPercent() != 0) { $elements .= "Divino: {$monster->getHolyPercent()}%<br>"; }		
		if($monster->getDeathPercent() != 0) { $elements .= "Morte: {$monster->getDeathPercent()}%"; }	
			
		/* HEALING AREA */
		if($monster->getMaxHealing() > 0)
		{ 
			$healingStr = "<br>Regeneração máxima em média por turno (2 seg): <b>{$monster->getMaxHealing()}</b><br>";
			$healingStr .= "Frequencia de regeneração: <b>";
			
			if($monster->getHealingFreq() < 10){ $healingStr .= "Muito Baixa"; }
			elseif($monster->getHealingFreq() < 20){ $healingStr .= "Baixa"; }
			elseif($monster->getHealingFreq() < 40){ $healingStr .= "Frequente"; }
			elseif($monster->getHealingFreq() < 70){ $healingStr .= "Muito Frequente"; }
			else { $healingStr .= "Semi Imortal"; }
		}
		$healingStr .= "</b>";
		
		/* SUMMONS AREA */
		
		$summons = "";
		
		if($monster->getMaxSummons() > 0)
		{
			$summons = "<br>Summons (max: {$monster->getMaxSummons()}): ";
			
			$it = $monster->getSummons()->getIterator();
			$it instanceof ArrayIterator;
			
			while($it->valid())
			{
				$summons .= "{$it->current()}";
				if($it->count() - 1 != $it->key()) $summons .= ", ";
				else $summons .= ".";
				
				$it->next();
			}
		}
		
		$voicesStr = "";
		$voices = $monster->getVoices();
		
		if($voices)
		{
			$voices instanceof ArrayObject;
			$it = $voices->getIterator();
			$it instanceof ArrayIterator;
			while($it->valid())
			{
				$voicesStr .= "<span style='font-weight: bold; color: #ff7d00;'>{$it->current()}</span><br>";
				$it->next();
			}
		}
			
		$module .= "				
		<form action='{$_SERVER['REQUEST_URI']}' method='get'>
			<fieldset>
				<p>	
					<input type='hidden' name='ref' value='darghopedia.monsterlist'/> 	
					<label for='category'>Selecione uma categoria</label><br />
					{$monster->getListAsSelect()->Draw()}
				</p>			
			</fieldset>
		</form>		
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th colspan='2'>Detalhes do monstro</td>
			</tr>
			<tr>
				<td style='text-align: right; vertical-align: bottom; width: 64px; height: 64px;'><img src='{$img}'/></td> 
				<td><h3>{$monster->getName()}</h3><br/>{$monster->getHealthMax()} pontos de vida.<br>".($monster->getExperience())." (multiplique pelo seu stage) pontos de experiencia por morte.</td>
			</tr>
			<tr>
				<td>Geral</td> <td>{$features}</td>
			</tr>";
			
			if($elements != "")
			{
				$module .= "
				<tr>
					<td>Fraquezas & Resistencias</td> <td>{$elements}<br><br><b>Obs:</b> Valores negativos são respectivos a fraqueza enquanto positivos são resistencia.</td>
				</tr>";
			}		
			
			if($immunity != "")
			{
				$module .= "
				<tr>
					<td>Imunidades</td> <td>{$immunity}</td>
				</tr>";				
			}
			
			if($voicesStr != "")
			{
				$module .= "
				<tr>
					<td>Vozes</td> <td>{$voicesStr}</td>
				</tr>";				
			}
			
			$module .= "
			<tr>
				<td>Batalha</td> <td>Dano maximo (combo) por turno (2 seg): <b>".abs($monster->getMaxDamage())."</b>{$healingStr}{$summons}</td>
			</tr>
		</table>
		";	

		$lotMap = $monster->getLotMap();
		$it = $lotMap->getIterator();
		$it instanceof ArrayIterator;
		
		$itemsStr = "";
		
		$coldcoins = array(2148, 2152, 2160);
		
		$totalmoney = 0;
		
		function getMoney($id, $c, &$money)
		{			
			//echo "Money: {$money}";
			if($id == 2152)
			{
				$money += $c * 100;
			}
			elseif($id == 2160)
			{
				$money += $c * 10000;
			}
			elseif($id == 2148)
			{
				$money += $c;
			}
		}
		
		while($it->valid())
		{
			$lot = $it->current();
			$lot instanceof ArrayObject;
			
			if(in_array($lot->offSetGet("id"), $coldcoins))
			{
				getMoney($lot->offSetGet("id"), $lot->offsetGet("countmax"), $totalmoney);
				$it->next();
				continue;
			}
			
			$items = \Framework\Items::GetInstance();
			$items instanceof \Framework\Items;
			
			$name = "<span id='item_{$lot->offsetGet("id")}' class='requestItemInfo'> " . $items->getNameById($lot->offsetGet("id")) . "</span>";
			$chance = $lot->offsetGet("chance") * \Framework\Monsters::LOT_RATE;
			$chance = min($chance, 100000);
			$chanceStr = "";
			
			if($chance <= 50){ $chanceStr = "<span style='color: #ff921d; font-weight: bold;'>Item lendário</span>"; }
			elseif($chance <= 200){ $chanceStr = "<span style='color: #c001ff; font-weight: bold;'>Item épico</span>"; }
			elseif($chance <= 500){ $chanceStr = "<span style='color: #4925ff; font-weight: bold;'>Extremamente raro</span>"; }
			elseif($chance <= 1000){ $chanceStr = "<span style='color: #18b515; font-weight: bold;'>Raro</span>"; }
			elseif($chance <= 2500){ $chanceStr = "<span style='font-weight: bold;'>Pouco raro</span>"; }
			elseif($chance <= 5000){ $chanceStr = "<span style='color: #dcdcdc;'>Um pouco frequente</span>";	}
			elseif($chance <= 10000){ $chanceStr = "<span style='color: #dcdcdc;'>Muito frequente</span>"; }
			elseif($chance <= 20000){ $chanceStr = "<span style='color: #dcdcdc;'>Frequente</span>"; }
			elseif($chance <= 50000){ $chanceStr = "<span style='color: #dcdcdc;'>Abundante</span>"; }
			elseif($chance <= 99999){ $chanceStr = "<span style='color: #dcdcdc;'>Quase sempre</span>"; }
			elseif($chance == 100000){ $chanceStr = "<span style='color: #dcdcdc;'>Sempre</span>"; }
			
			$quanty = ($lot->offsetExists("countmax") && $lot->offsetGet("countmax") > 0) ? "{$lot->offsetGet("countmax")}x" : "";
			$img = "<img id='item_{$lot->offsetGet("id")}' class='requestItemInfo' src='files/items/{$lot->offsetGet("id")}.gif'/>";
			
			$itemsStr .= "
			<tr>
				<td style='text-align: right; vertical-align: bottom; width: 32px; height: 32px;'>{$img}</td>
				<td>{$quanty} {$name}</td>
				<td>{$chanceStr}</td>
			</tr>";
			
			$it->next();
		}
		
		$goldStr = "";
		
		if($totalmoney > 0)
		{
			$goldStr = "
			<tr>
				<td style='text-align: right; vertical-align: bottom; width: 32px; height: 32px;'><img id='item_2148' class='requestItemInfo' src='files/items/2148.gif'/></td>
				<td><span id='item_2148' class='requestItemInfo'>0 a {$totalmoney} gold coins<span></td>
				<td>Sempre</td>
			</tr>";		
		}
		
		$module .= "				
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>#</td>
				<th>Item</td>
				<th>Frequencia</td>
			</tr>
			{$goldStr}
			{$itemsStr}
		</table>";	
	}
}

$view = new View();
?>