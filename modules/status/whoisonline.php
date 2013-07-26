<?php
use \Core\Configs as g_Configs;
use \Core\Consts;

if(isset($_GET["world"]) || !g_Configs::Get(g_Configs::eConf()->ENABLE_MULTIWORLD))
{
	\Core\Main::requireWorldSelection();
	
	$world_id = (int)$_GET["world"];

	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Ordon;

	$status_query = \Core\Main::$DB->query("SELECT `players`, `online`, `uptime`, `afk`, `date` FROM `serverstatus` WHERE `server_id` = {$world_id} ORDER BY `date` DESC LIMIT 1");
	$stats_fetch = $status_query->fetch();
	
	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th colspan='4'>Server Status</th>
		</tr>";	
	
	if($stats_fetch->online == 0 || $stats_fetch->date < time - 60 * 5)
	{
		$module .= "
		<tr>
			<td colspan='4'>O nosso servidor se encontra neste momento <font color='#ec0404'><b>off-line</b></font>.</td>
		</tr>";
		
		
		$module .= "
	</table>";		
	}
	else
	{
		$_isadmin = false;
			
		if($_SESSION['login'])
		{
			$_loginacc = new \Framework\Account();
			$_loginacc->load($_SESSION['login'][0]);
			
			if($_loginacc->getGroup() == t_Group::Administrator)
			{
				$_isadmin = true;
			}
		}		
		
		if($_isadmin)
		{
			$_premiums = 0;
			$levelSum = 0;
			$spoofPlayers = 0;
		}
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query = \Core\Main::$DB->query("
			SELECT 
				name, 
				vocation, 
				level, 
				town_id, 
				account_id, 
				promotion,
				afk
				,`is_spoof`
				,`pvpEnabled`
				,`skull`
			FROM 
				`players`
			WHERE
				`online` = '1'
				AND `world_id` = {$world_id}
			ORDER BY name");
		else
			$query = \Core\Main::$DB->query("SELECT name, vocation, level, group_id, town_id, account_id, skull FROM players WHERE online = '1' AND world_id = {$world_id} ORDER BY name");
		
		$_totalplayers = $stats_fetch->players + $stats_fetch->afk;
		$_afkPlayers = $stats_fetch->afk;
		
		$_pvp_enabled = 0;
		$_pvp_disabled = 0;
		
		$_sorcerers = 0;	
		$_druids = 0;	
		$_paladins = 0;	
		$_knights = 0;	
		
		$_islandofpeace = 0;
		$_quendor = 0;
		$_thorn = 0;
		$_aaragon = 0;
		$_salazart = 0;
		$_northrend = 0;
		$_kashmir = 0;	
		$_aracura = 0;	
		
		if($_totalplayers == 0)
		{
			$playersonmsg = "Infelizmente ninguem está conectado em nosso servidor neste momento.";
		}	
		else	
		{		
			$players_list = "";
			
			if($_totalplayers == 1)
				$playersonmsg = "Nós temos 1 jogador conectado em nosso servidor.";
			else	
				$playersonmsg = "Nós temos {$_totalplayers} jogadores conectados em nosso servidor.";	
				
			if(g_Configs::Get(g_Configs::eConf()->STATUS_IGNORE_AFK))
			{
				if($_afkPlayers == 1)
					$playersonmsg .= " Destes, 1 está treinando.";
				elseif($_afkPlayers == 0)
					$playersonmsg .= " Destes, nenhum está treinando.";
				elseif($_afkPlayers > 1)
					$playersonmsg .= " Destes, {$_afkPlayers} estão treinando.";
			}			
	
			while($fetch = $query->fetch())
			{							
				$_characc = new \Framework\Account();
				$_characc->load($fetch->account_id);		
	
				$spoofStyle = "";
					
				if($_isadmin)
				{						
					if($fetch->is_spoof == 1 || $fetch->group_id == t_Group::PlayerNonLogout)
					{
						$spoofPlayers++;
						$spoofStyle = "style='font-weight: normal;'";						
					}
					else
					{
						$levelSum += $fetch->level;
						
						if($_characc->getPremDays() > 0)
							$_premiums++;
							
						if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_PVP_SWITCH))
						{						
							if($fetch->pvpEnabled)
								$_pvp_enabled++;
							else
								$_pvp_disabled++;			
						}			
							
						switch($fetch->town_id)
						{
							case t_Towns::IslandOfPeace: $_islandofpeace++; break;
							case t_Towns::Quendor: $_quendor++; break;
							case t_Towns::Thorn: $_thorn++; break;
							case t_Towns::Aaragon: $_aaragon++; break;
							case t_Towns::Salazart: $_salazart++; break;
							case t_Towns::Northrend: $_salazart++; break;
							case t_Towns::Kashmir: $_kashmir++; break;
							case t_Towns::Aracura: $_aracura++; break;
						}		
	
						if(\Core\Tools::isSorcerer($fetch->vocation))
							$_sorcerers++;
						elseif(\Core\Tools::isDruid($fetch->vocation))
							$_druids++;				
						elseif(\Core\Tools::isPaladin($fetch->vocation))
							$_paladins++;				
						else
							$_knights++;							
					}
				}
				else
				{				
					if(\Core\Tools::isSorcerer($fetch->vocation))
						$_sorcerers++;
					elseif(\Core\Tools::isDruid($fetch->vocation))
						$_druids++;				
					elseif(\Core\Tools::isPaladin($fetch->vocation))
						$_paladins++;				
					else
						$_knights++;	
	
					switch($fetch->town_id)
					{
						case t_Towns::IslandOfPeace: $_islandofpeace++; break;
						case t_Towns::Quendor: $_quendor++; break;
						case t_Towns::Thorn: $_thorn++; break;
						case t_Towns::Aaragon: $_aaragon++; break;
						case t_Towns::Salazart: $_salazart++; break;
						case t_Towns::Northrend: $_salazart++; break;
						case t_Towns::Kashmir: $_kashmir++; break;
						case t_Towns::Aracura: $_aracura++; break;
					}					
				}
				
				$isAfk = $fetch->afk;
				
				$vocation_id = $fetch->vocation;
				
				if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS && $fetch->promotion == 1)
				{
					$vocation_id += 4;
				}
				elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS && $fetch->promotion == 2 && $_characc->getPremDays() > 0)
				{
					$vocation_id += 8;
				}
				
				$pvpStr = "";
					
				if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_PVP_SWITCH))
				{
					$pvpStr = "<td><span class='pvpEnabled'>Agressivo</span>";
					
					if(!$fetch->pvpEnabled)
						$pvpStr = "<td><span class='pvpDisabled'>Pacifico</span></td>";	
				}
				
				$_vocation = new t_Vocation($vocation_id);
				
				
				$skull_img = Framework\Player::getSkullImg($fetch->skull);
				
				$players_list .= "
				<tr>
					<td><a {$spoofStyle} ".(($isAfk) ? "class='afkPlayer'" : null)." href='?ref=character.view&name={$fetch->name}'>{$fetch->name}</a> {$skull_img}</td> <td>{$_vocation->GetByName()}</td> <td>{$fetch->level}</td> {$pvpStr}
				</tr>";		
			}			
		}
		
		if($_totalplayers > 0)
		{
			if($_isadmin)
			{
				$_totalplayers -= $spoofPlayers;
				$playersonmsg .= " ({$_totalplayers} / {$spoofPlayers})";
				
			}	

			$seconds = $stats_fetch->uptime % 60;
			$uptime = floor($stats_fetch->uptime / 60);
			
			$minutes = $uptime % 60;
			$uptime = floor($uptime / 60);
			
			$hours = $uptime % 24;
			$uptime = floor($uptime / 24);
			
			$days = $uptime % 365;
				
			$uptime = ($days >= 1) ? "{$days} dias {$hours} horas e {$minutes} minutos" : "{$hours} horas e {$minutes} minutos";			
			
			$module .= "
			<tr>
				<td colspan='4'>{$playersonmsg}</td>
			</tr>
			<tr>
				<td colspan='4'><b>O servidor está ligado a:</b> {$uptime}</td>
			</tr>			
			<tr>
				<td colspan='4'><b>Destes, são das vocações:</b></td>
			</tr>
			<tr>
				<td>Sorcerer's:</td><td>".\Core\Tools::getPercentOf($_sorcerers, $_totalplayers)."%</td><td>Druid's:</td><td>".\Core\Tools::getPercentOf($_druids, $_totalplayers)."%</td>
			</tr>
			<tr>
				<td>Paladin's:</td><td>".\Core\Tools::getPercentOf($_paladins, $_totalplayers)."%</td><td>Knight's:</td><td>".\Core\Tools::getPercentOf($_knights, $_totalplayers)."%</td>
			</tr>
			";
			
			$module .= "			
			<tr>
				<td colspan='4'><b>Destes, se localizam nas cidades:</b></td>
			</tr>
			<tr>
				<td>Island of Peace:</td><td>".\Core\Tools::getPercentOf($_islandofpeace, $_totalplayers)."%</td>
				<td>Quendor:</td><td>".\Core\Tools::getPercentOf($_quendor, $_totalplayers)."%</td>
			</tr>
			<tr>
				<td>Thorn:</td><td>".\Core\Tools::getPercentOf($_thorn, $_totalplayers)."%</td>
				<td>Aracura:</td><td>".\Core\Tools::getPercentOf($_aracura, $_totalplayers)."%</td>	
			</tr>
			<tr>
				<td>Aaragon:</td><td>".\Core\Tools::getPercentOf($_aaragon, $_totalplayers)."%</td>
				<td>Salazart:</td><td>".\Core\Tools::getPercentOf($_salazart, $_totalplayers)."%</td>		
			</tr>
			<tr>
				<td>Northrend:</td><td>".\Core\Tools::getPercentOf($_northrend, $_totalplayers)."%</td>
				<td>Kashmir:</td><td>".\Core\Tools::getPercentOf($_kashmir, $_totalplayers)."%</td>	
			</tr>";				
			
			if($_isadmin)
			{
				$pvpStr = "";
					
				if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_PVP_SWITCH))
				{			
					$pvpStr = "
					<tr>
						<td>Agressivos:</td><td>".\Core\Tools::getPercentOf($_pvp_enabled, $_totalplayers)."%</td><td>Pacificos:</td><td>".\Core\Tools::getPercentOf($_pvp_disabled, $_totalplayers)."%</td>
					</tr>					
					";					
				}	
				
				$module .= "
				<tr>
					<td colspan='4'><b>Estatisticas:</b></td>
				</tr>
				<tr>
					<td>Free Account's:</td><td>".\Core\Tools::getPercentOf($_totalplayers - $_premiums, $_totalplayers)."%</td><td>Premium Account's:</td><td>".\Core\Tools::getPercentOf($_premiums, $_totalplayers)."%</td>
				</tr>
				{$pvpStr}	
				<tr>
					<td colspan='2'>Level médio:</td><td colspan='2'>".(ceil($levelSum / $_totalplayers))."</td>
				</tr>			
				";
			}
		}
		else
		{
			$module .= "
			<tr>
				<td colspan='4'>{$playersonmsg}</td>
			</tr>";		
		}
		
		$module .= "
	</table>";	
		
		if($query->numRows() > 0)
		{
			$pvpStr = "";
			
			if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_PVP_SWITCH))
				$pvpStr = "<th>PvP</th>";
			
			$module .= "
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th width='35%'>Nome</th> <th width='25%'>Vocação</th> <th>Nível</th> {$pvpStr}
				</tr>
	
				{$players_list}
				
			</table>";		
		}
	}

}
else
	\Core\Main::requireWorldSelection(true);
	
	

?>