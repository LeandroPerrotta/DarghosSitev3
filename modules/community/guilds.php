<?php
use \Core\Configs;

\Core\Main::requireWorldSelection();

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];
	
	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Ordon;	
	
	$guildsActived = \Framework\Guilds::ActivedGuildsList($world_id);
	
	if(Configs::Get(Configs::eConf()->ENABLE_GUILD_IN_FORMATION))
		$guildsForming = \Framework\Guilds::FormingGuildsList($world_id);
	
	/*
	 * Guilds List
	 */
	
	$guild_list = "
	<div title='guild_list' class='viewable' style='margin: 0px; padding: 0px;'>";
		
		$_guildsTable = new \Framework\HTML\Table();	
		
		$_guildsTable->AddDataRow("Guildas em Atividade");
	
		if($guildsActived)
		{
			$_guildsTable->AddField("", 20);
			
			$style = "font-weight: bold;";
			
			$_guildsTable->AddField("Descrição", null, $style);
			
			if(Configs::Get(Configs::eConf()->ENABLE_GUILD_POINTS))
				$_guildsTable->AddField("Pontos", null, $style);
				
			$_guildsTable->AddRow();
			
			foreach($guildsActived as $guild)
			{			
				$_guildsTable->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$guild->GetImage()}'' height='100' width='100' />");
				
				$string = "
					<a href='?ref=guilds.details&name={$guild->GetName()}'>{$guild->GetName()}</a><br>
					{$guild->GetMotd()}
				";
				
				$style = "vertical-align: middle; height: 50px;";	
					
				$_guildsTable->AddField($string, null, $style, null);
				
				if(Configs::Get(Configs::eConf()->ENABLE_GUILD_POINTS))
				{
					$string = "{$guild->GetBetterPoints()}/{$guild->GetPoints()}";
					$_guildsTable->AddField($string);
				}
				
				$_guildsTable->AddRow();
			}
		}
		else
		{			
			$_guildsTable->AddField("Não existe nenhuma guilda ativa neste momento.");
			$_guildsTable->AddRow();			
		}
		
		$guild_list .= $_guildsTable->Draw();
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_IN_FORMATION))
		{
			$_formingTable = new \Framework\HTML\Table();
			$_formingTable->AddDataRow("Guildas em Formação");
			
			if($guildsForming)
			{
				$_formingTable->AddField("", 20);
				
				$style = "font-weight: bold;";
				
				$_formingTable->AddField("Descrição", null, $style);
				$_formingTable->AddRow();
				
				foreach($guildsForming as $guild)
				{	
					$_formingTable->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$guild->GetImage()}'' height='100' width='100' />");
					
					$string = "
						<a href='?ref=guilds.details&name={$guild->GetName()}'>{$guild->GetName()}</a><br>
						{$guild->GetMotd()}
					";
					
					$style = "vertical-align: middle; height: 50px;";	
						
					$_formingTable->AddField($string, null, $style, null);
					
					$_formingTable->AddRow();
				}
			}	
			else
			{			
				$_formingTable->AddField("Não existe nenhuma guilda em formação neste momento.");
				$_formingTable->AddRow();	
			}
		
			$guild_list .= $_formingTable->Draw();
		}
	
	$guild_list .= "
	</div>";
	
	/*
	 * Guilds Wars List
	 */	
		
	$guild_wars = "";
	$end_wars = "";
	$negotiation_wars = "";
	
	if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
	{	
		$_warsTable = new \Framework\HTML\Table();
		$_warsTable->AddDataRow("Guerras em andamento");	
		
		$warsStarted = \Framework\Guilds\War::ListStartedWars($world_id);
		
		if($warsStarted)
		{
			$style = "font-weight: bold;";
			
			$_warsTable->AddField("Declarante", null, $style);	
			$_warsTable->AddField("Oponente", null, $style);
			$_warsTable->AddField("Declarada em", null, $style);
			$_warsTable->AddField("Termina em", null, $style);
			$_warsTable->AddField("", 5);
			$_warsTable->AddRow();
			
			foreach($warsStarted as $guild_war)
			{
				$guild = new \Framework\Guilds();
				$guild->Load($guild_war->GetGuildId());
				
				$opponent = new \Framework\Guilds();
				$opponent->Load($guild_war->GetOpponentId());
				
				$_warsTable->AddField($guild->GetName());	
				$_warsTable->AddField($opponent->GetName());
				$_warsTable->AddField(\Core\Main::formatDate($guild_war->GetDeclarationDate()));
				$_warsTable->AddField(\Core\Main::formatDate($guild_war->GetEndDate()));
				
				$string = "<a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a>";
				
				$_warsTable->AddField($string);
				$_warsTable->AddRow();		
			}
		}
		else
		{
			$_warsTable->AddField("Não existe nenhuma guerra declarada no momento.");
			$_warsTable->AddRow();		
		}
			
		$guild_wars = "
		<div title='guild_wars' style='margin: 0px; padding: 0px;'>
			{$_warsTable->Draw()}
		</div>
		";	
			
		/*
		 * Ended Wars List
		 */	
			
		$_warsTable = new \Framework\HTML\Table();
		$_warsTable->AddDataRow("Guerras terminadas");	
		
		$warsEnded = \Framework\Guilds\War::ListEndedWars($world_id);
		
		if($warsEnded)
		{
			$style = "font-weight: bold;";
			
			$_warsTable->AddField("Declarante", null, $style);	
			$_warsTable->AddField("Oponente", null, $style);
			$_warsTable->AddField("Declarada em", null, $style);
			$_warsTable->AddField("Vencedor", null, $style);
			$_warsTable->AddField("", 5);
			$_warsTable->AddRow();
			
			foreach($warsEnded as $guild_war)
			{
				$guild = new \Framework\Guilds();
				$guild->Load($guild_war->GetGuildId());
				
				$opponent = new \Framework\Guilds();
				$opponent->Load($guild_war->GetOpponentId());
				
				$_warsTable->AddField($guild->GetName());	
				$_warsTable->AddField($opponent->GetName());
				$_warsTable->AddField(\Core\Main::formatDate($guild_war->GetDeclarationDate()));
		
				$winner = $guild->GetName();
				
				if($guild_war->GetOpponentFrags() > $guild_war->GetGuildFrags())
					$winner = $opponent->GetName();
				
				$_warsTable->AddField($winner);
				
				$string = "<a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a>";
				
				$_warsTable->AddField($string);
				$_warsTable->AddRow();		
			}
		}
		else
		{
			$_warsTable->AddField("Nenhuma guerra foi terminada até o momento.");
			$_warsTable->AddRow();		
		}
		
		$end_wars = "
		<div title='end_wars' style='margin: 0px; padding: 0px;'>
			{$_warsTable->Draw()}
		</div>
		";
			
		/*
		 * Negotiation Wars List
		 */	
			
		$_warsTable = new \Framework\HTML\Table();
		$_warsTable->AddDataRow("Guerras em negociação");	
		
		$warsNegotiation = \Framework\Guilds\War::ListNegotiationWars($world_id);
		
		if($warsNegotiation)
		{
			$style = "font-weight: bold;";
			
			$_warsTable->AddField("Declarante", null, $style);	
			$_warsTable->AddField("Oponente", null, $style);
			$_warsTable->AddField("Declarada em", null, $style);
			$_warsTable->AddField("", 5);
			$_warsTable->AddRow();
			
			foreach($warsNegotiation as $guild_war)
			{
				$guild = new \Framework\Guilds();
				$guild->Load($guild_war->GetGuildId());
				
				$opponent = new \Framework\Guilds();
				$opponent->Load($guild_war->GetOpponentId());
				
				$_warsTable->AddField($guild->GetName());	
				$_warsTable->AddField($opponent->GetName());
				$_warsTable->AddField(\Core\Main::formatDate($guild_war->GetDeclarationDate()));
		
				$string = "<a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a>";
				
				$_warsTable->AddField($string);
				$_warsTable->AddRow();		
			}
		}
		else
		{
			$_warsTable->AddField("Nenhuma guerra em negociação neste momento.");
			$_warsTable->AddRow();		
		}
		
		$negotiation_wars = "
		<div title='negotiation_wars' style='margin: 0px; padding: 0px;'>
			{$_warsTable->Draw()}
		</div>
		";		
	}
			
		$module .= "
		<br>";
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT))
		$module .= "<p>
			<a class='buttonstd' href='?ref=guilds.create'>Criar nova Guild</a>
		</p>";
		
		$module .="
		<fieldset>
			<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
				<select>
					<option value='guild_list'>Lista de Guildas</option>";
					
					if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
					{
						$module .= "	
						<option value='guild_wars'>Guerras em andamento</option>
						<option value='end_wars'>Guerras terminadas</option>
						<option value='negotiation_wars'>Guerras em negociação</option>";
					}
					
					$module .= "
				</select>
			</div>
			
			{$guild_list}
			{$guild_wars}
			{$end_wars}
			{$negotiation_wars}
			
		</fieldset>";
}
?>