<?php
$module .= "
<p>No Darghos costumamos sempre abrir enquetes sobre mudanças de certas caracteristicas do jogo, tornando o desenvolvimento do servidor mais democratico entre a equipe e os jogadores. Para votar em uma enquete é necessario apenas estar logado em sua conta e pessouir um char com level minimo (isto pode ser modificado de enquete para enquete).</p>";

$pollList = \Framework\Forums\Topics::ListPollTopics();

$activeTable = new \Framework\HTML\Table();
$activeTable->AddDataRow("Enquetes em atividade");

$style = "font-weight: bold;";

$activeTable->AddField("Titulo / Detalhes", null, $style);	
$activeTable->AddField("Respostas", null, $style);	
$activeTable->AddField("Ultimo por", null, $style);	
$activeTable->AddRow();

$inactiveTable = new \Framework\HTML\Table();
$inactiveTable->AddDataRow("Enquetes concluidas");

$inactiveTable->AddField("Titulo / Detalhes", null, $style);	
$inactiveTable->AddField("Respostas", null, $style);
$inactiveTable->AddField("Ultimo por", null, $style);
$inactiveTable->AddRow();

$haveActive = false;
$haveInactive = false;

if($pollList)
{
	foreach($pollList as $poll)
	{
		if(time() < $poll->GetPollEnd())
		{
			$premium = "Não.";
			
			if($poll->PollIsOnlyForPremiums())
				$premium = "Sim.";
			
			
			$string = "
				<a href='?ref=forum.topic&v={$poll->GetId()}'>" . $poll->GetTitle() . "</a><br/>
				<i>Iniciado em " . \Core\Main::formatDate($poll->GetDate()) . ".
				Termina em " . \Core\Main::formatDate($poll->GetPollEnd()) . ".</i><br>
				Premium: {$premium} Level: {$poll->GetPollMinLevel()}
			";
			
			$activeTable->AddField($string);				
			$activeTable->AddField($poll->GetPostCount());	
			
			$lastPost = $poll->GetLastPost();
			
			if($poll->GetPostCount() > 0)
			{	
				$user_post = new \Framework\Forums\User();
				$user_post->Load($lastPost["user_id"]);
				
				$user_character = new \Framework\Player();
				$user_character->load($user_post->GetPlayerId());			
				
				$string = "
					<a href='?ref=character.view&name={$user_character->getName()}'>{$user_character->getName()}</a><br>
					<i>" . \Core\Main::formatDate($lastPost["date"]) . "</i>
				";
			}
			else
			{
				$string = "<i>Nenhum post.</i>";				
			}
	
			$activeTable->AddField($string);	
			$activeTable->AddRow();				
			
			$haveActive = true;
		}
		else
		{			
			$string = "
				<a href='?ref=forum.topic&v={$poll->GetId()}'>" . $poll->GetTitle() . "</a><br/>
				<i>Iniciado em " . \Core\Main::formatDate($poll->GetDate()) . ".
				Terminou em " . \Core\Main::formatDate($poll->GetPollEnd()) . ".</i>
			";
			
			$inactiveTable->AddField($string);				
			$inactiveTable->AddField($poll->GetPostCount());	
			
			$lastPost = $poll->GetLastPost();
			
			if($poll->GetPostCount() > 0)
			{	
				$user_post = new \Framework\Forums\User();
				$user_post->Load($lastPost["user_id"]);
				
				$user_character = new \Framework\Player();
				$user_character->load($user_post->GetPlayerId());			
				
				$string = "
					<a href='?ref=character.view&name={$user_character->getName()}'>{$user_character->getName()}</a><br>
					<i>" . \Core\Main::formatDate($lastPost["date"]) . "</i>
				";
			}
			else
			{
				$string = "<i>Nenhum post.</i>";				
			}
	
			$inactiveTable->AddField($string);	
			$inactiveTable->AddRow();					
	
			$haveInactive = true;
		}
	}
}

if(!$haveActive)
{
	$activeTable->AddField("Não possuimos nenhuma enquete aberta.", null, null, 3);
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
