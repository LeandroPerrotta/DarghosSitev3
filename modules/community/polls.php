<?php
$module .= "
<p>No Darghos costumamos sempre abrir enquetes sobre mudanças de certas caracteristicas do jogo, tornando o desenvolvimento do servidor mais democratico entre a equipe e os jogadores. Para votar em uma enquete é necessario apenas estar logado em sua conta e pessouir um char com level minimo (isto pode ser modificado de enquete para enquete).</p>";

$pollList = Forum_Topics::ListPollTopics();

$activeTable = new HTML_Table();
$activeTable->AddDataRow("Enquetes em atividade");

$style = "font-weight: bold;";

$activeTable->AddField("Titulo", null, $style);	
$activeTable->AddField("Level minimo", 5, $style);
$activeTable->AddField("Requer premium?", 5, $style);
$activeTable->AddField("Termina em", null, $style);
$activeTable->AddRow();

$inactiveTable = new HTML_Table();
$inactiveTable->AddDataRow("Enquetes concluidas");

$inactiveTable->AddField("Titulo", null, $style);	
$inactiveTable->AddField("Terminou em", null, $style);
$inactiveTable->AddRow();

$haveActive = false;
$haveInactive = false;

foreach($pollList as $poll)
{
	if(time() < $poll->GetPollEnd())
	{
		$activeTable->AddField("<a href='?ref=forum.topic&v={$poll->GetId()}'>" . $poll->GetTitle() . "</a>");	
		$activeTable->AddField($poll->GetPollMinLevel());
		
		if($poll->PollIsOnlyForPremiums())
			$activeTable->AddField("Sim");
		else
			$activeTable->AddField("Não");
		
		$activeTable->AddField(Core::formatDate($poll->GetPollEnd()));
		$activeTable->AddRow();	

		$haveActive = true;
	}
	else
	{
		$inactiveTable->AddField("<a href='?ref=forum.topic&v={$poll->GetId()}'>" . $poll->GetTitle() . "</a>");	
		$inactiveTable->AddField(Core::formatDate($poll->GetPollEnd()));
		$inactiveTable->AddRow();	

		$haveInactive = true;
	}
}

if(!$haveActive)
{
	$activeTable->AddField("Não possuimos nenhuma enquete aberta.", null, null, 2);
	$activeTable->AddRow();		
}

if(!$haveInactive)
{
		$inactiveTable->AddField("Não possuimos nenhuma enquete concluida.", null, null, 2);
		$inactiveTable->AddRow();	
}

$activedPolls = "
<div title='active' class='viewable' style='margin: 0px; padding: 0px;'>
	{$activeTable->Draw()}
</div>";
	
$endedPolls = "
<div title='ended' style='margin: 0px; padding: 0px;'>
	{$inactiveTable->Draw()}
</div>";

$module .= "
<fieldset>
	<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
		<select>
			<option value='active'>Enquetes abertas.</option>
			<option value='ended'>Enquetes encerradas.</option>
		</select>
	</div>
	
	{$activedPolls}
	{$endedPolls}
	
</fieldset>";
?>
