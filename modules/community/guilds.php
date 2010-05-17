<?php
$guildsActived = Guilds::ActivedGuildsList();
$guildsForming = Guilds::FormingGuildsList();

/*
 * Guilds List
 */

$guild_list = "
<div title='guild_list' class='viewable' style='margin: 0px; padding: 0px;'>";
	
	$_guildsTable = new HTML_Table();	
	
	$_guildsTable->AddDataRow("Guildas em Atividade");

	if($guildsActived)
	{
		$_guildsTable->AddField("", 20);
		
		$style = "font-weight: bold;";
		
		$_guildsTable->AddField("Descrição", null, $style);
		$_guildsTable->AddRow();
		
		foreach($guildsActived as $guild)
		{			
			$_guildsTable->AddField("<img src='".GUILD_IMAGE_DIR."{$guild->GetImage()}'' height='100' width='100' />");
			
			$string = "
				<a href='?ref=guilds.details&name={$guild->GetName()}'>{$guild->GetName()}</a><br>
				{$guild->GetMotd()}
			";
			
			$style = "vertical-align: middle; height: 50px;";	
				
			$_guildsTable->AddField($string, null, $style, null);
			
			$_guildsTable->AddRow();
		}
	}
	else
	{			
		$_guildsTable->AddField("Não existe nenhuma guilda ativa neste momento.");
		$_guildsTable->AddRow();			
	}
	
	$guild_list .= $_guildsTable->Draw();
	
	$_formingTable = new HTML_Table();
	$_formingTable->AddDataRow("Guildas em Formação");
	
	if($guildsForming)
	{
		$_formingTable->AddField("", 20);
		
		$style = "font-weight: bold;";
		
		$_formingTable->AddField("Descrição", null, $style);
		$_formingTable->AddRow();
		
		foreach($guildsForming as $guild)
		{	
			$_formingTable->AddField("<img src='".GUILD_IMAGE_DIR."{$guild->GetImage()}'' height='100' width='100' />");
			
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
	
	
	$guild_list .= "
	{$_formingTable->Draw()}
</div>
";

/*
 * Guilds Wars List
 */	
	
$_warsTable = new HTML_Table();
$_warsTable->AddDataRow("Guerras em andamento");	

$warsStarted = Guild_War::ListStartedWars();

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
		$guild = new Guilds();
		$guild->Load($guild_war->GetGuildId());
		
		$opponent = new Guilds();
		$opponent->Load($guild_war->GetOpponentId());
		
		$_warsTable->AddField($guild->GetName());	
		$_warsTable->AddField($opponent->GetName());
		$_warsTable->AddField(Core::formatDate($guild_war->GetDeclarationDate()));
		$_warsTable->AddField(Core::formatDate($guild_war->GetEndDate()));
		
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
	
$module .= "
<br><p>
	<a class='buttonstd' href='?ref=guilds.create'>Criar nova Guild</a>
</p>	

<fieldset>
	<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
		<select>
			<option value='guild_list'>Lista de Guildas</option>
			<option value='guild_wars'>Guildas em Guerra</option>
		</select>
	</div>
	
	{$guild_list}
	{$guild_wars}
	
</fieldset>";
?>