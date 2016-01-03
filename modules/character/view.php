<?
use \Core\Configs;
use \Framework\Player;

if($_POST["player_name"] || $_GET['name'])
{		
	$name = ($_POST) ? $_POST["player_name"] : $_GET['name'];

	$player = new Player();
	
	if(!$player->loadByName($name))
	{	
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_WRONG);
	}
	else
	{			
		$account = new \Framework\Account();
		$account->load($player->get("account_id"));
		
		$logged_acc = \Framework\Account::loadLogged();
		
		$deaths = new \Framework\Deaths();
		
		//$bans = $account->getBans();
		$bans = new \Framework\Bans();
		
		$houseid = $player->getHouse();
		$lastlogin = ($player->getLastLogin()) ? \Core\Main::formatDate($player->getLastLogin()) : "Nunca entrou.";
		$creation = \Core\Main::formatDate($player->getCreation());
		
		$premium = ($account->getPremDays() != 0) ? "<font style='color: #00ff00; font-weight: bold;'>" . tr("Conta Premium") : tr("Conta Gratuita");	
		//$vip = ($account->getVIPDaysLeft() != 0) ? "<font style='color: #e1dc48; font-weight: bold;'>" . tr(" [com VIP]") . "</font>" : null;	
		$premium = empty($vip) ? $premium : $premium . $vip;
		$realname = ($account->get("real_name") != "") ? $account->get("real_name") : tr("não configurado");
		$location = ($account->get("location") != "") ? $account->get("location") : tr("não configurado");
		$url = ($account->get("url") != "") ? $account->get("url") : "não configurado";
		
		$deathlist = $deaths->getDeathListOfPlayer($player->getId());
		$list = $account->getCharacterList();
		$oldnames = $player->loadOldNames();
	
		$module .= "
		<div id='horizontalSelector'>
			<span name='left_corner'></span>
			<ul>
				<li name='profile' checked='checked'><span>".tr("Profile")."</span></li>
				<li name='statistics'><span>".tr("Estatisticas")."</span></li>
				<li name='achievements'><span>".tr("Façanhas")."</span></li>
				<li name='accounts'><span>".tr("Conta")."</span></li>
			</ul>
			<span name='right_corner'></span>
		</div>
		
		<div title='profile' class='viewable' style='margin: 0px; padding: 0px;'>";

$infos = array();

if($player->deletionStatus())
	$infos[] = "<font style='color: red; font-weight: bold;'>".tr("Este personagem esta agendado para ser deletado no dia @v1@.", \Core\Main::formatDate($player->deletionStatus()))."</font>";

$skull_img = Framework\Player::getSkullImg($player);
$infos[] = array(tr("Nome"), $player->getName() . " {$skull_img}");

if($oldnames)
{
	$i = 0;
	foreach($oldnames as $name => $time)
	{
		$i++;

		$oldnames_string .= $name;

		if($i != count($oldnames))
			$oldnames_string .= ", ";
	}

	$infos[] = array(tr("Nomes Antigos"), $oldnames_string);
}

$infos[] = array(tr("Mundo"), t_Worlds::GetString($player->getWorldId()));
$infos[] = array(tr("Nível"), $player->getLevel());

if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH, $player->getWorldId()))
{
	$pvp_str = $player->isPvpEnabled() ? "<span class='pvpEnabled'>".tr("PvP Ativo")."</span>" : "<span class='pvpDisabled'>".tr("PvP Desativo")."</span>";
	$infos[] = array(tr("JvJ"), $pvp_str);
}

$infos[] = array(tr("Nível mágico"), $player->getMagLevel());

if(Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $player->getWorldId()))
	$infos[] = array(tr("Ranque em Campos de Batalhas"), $player->getBattlegroundRating());

$onlineTicks = $player->getOnlineTime();

$temp_date = new DateTime("@".(time() + $player->getOnlineTime(0)));
$now_date = new DateTime();
$diff = $temp_date->diff($now_date);

$infos[] = array(tr("Sexo"), t_Genre::GetString($player->getSex()));

$_vocation = new t_Vocation($player->getVocation());
$infos[] = array(tr("Vocação"), $_vocation->GetByName($player->getVocation()));

$town_str = t_Towns::GetString($player->getTownId());
$infos[] = array(tr("Residencia"), $town_str);

if($houseid)
{
	$houses = new \Framework\Houses($player->getWorldId());
	$houses->load($houseid, $player->getWorldId());
	
	$_house_town_str = t_Towns::GetString($houses->get("town"));
	
	$housemsg = "";
	if($houses->get("warnings") == 0)
	{
		$housemsg = "{$houses->get("name")} ({$_house_town_str}) ".tr("com pagamento no dia")."  ".\Core\Main::formatDate($houses->get("paid")).".";
	}
	else
	{
		$housemsg = "{$houses->get("name")} ({$_house_town_str}) ".tr("está com @v1@ pagamento(s) atrazado(s).", $houses->get("warnings"));
	}	
	
	$infos[] = array(tr("Casa"), $housemsg);
}
				
if($player->LoadGuild())
	$infos[] = array(tr("Membro da Guild"), "{$player->GetGuildRank()} da <a href='?ref=guilds.details&name={$player->GetGuildName()}'>{$player->GetGuildName()}");			
		
if($player->getComment())
{
	$str = str_replace('\\r', "", $player->getComment());
	$str = str_replace('\\n', "<br>", $str);
	$infos[] = array(tr("Comentario"), nl2br($str));
}
	
$infos[] = array(tr("Atividade recente"), \Core\Tools::getPercentOf($onlineTicks, 60*60*24) . tr("% das últimas 24h."));
$infos[] = array(tr("Tempo total"), tr("@v1@ dias conectado.", $diff->format("%a")));
$infos[] = array(tr("Criado em"), $creation);
$infos[] = array(tr("Último Login:"), $lastlogin);

$table = new \Framework\HTML\Table();
$table->AddField(tr("Personagem"), null, null, 2, true);
$table->AddRow();

foreach($infos as $k => $v)
{
	if(is_array($v))
	{
		$table->AddField($v[0], 25);
		$table->AddField($v[1]);
		$table->AddRow();
		
		continue;
	}
	
	$table->AddField($v, null, null, 2);
	$table->AddRow();
}

$module .= $table->Draw();

if($logged_acc && $logged_acc->getGroup() == t_Group::Administrator)
{	
	$contribute = new \Framework\Contribute();
	$oders = $contribute->getOrdersListByAccount($account->getId());
	
	$alreadyIsPremium = false;
	$numberOfPremiums = 0;
	
	if(is_array($oders))
	{	
		foreach($oders as $orderId)
		{
			$contribute->load($orderId);
			
			if($contribute->status == 1 OR $contribute->status == 2)
			{
				$numberOfPremiums++;
				$alreadyIsPremium = true;
			}
		}
	}	

	$alreadyIsPremiumHTML = ($alreadyIsPremium) ? "Sim" : "Não";
	
	if($alreadyIsPremium)
	{
		$alreadyIsPremiumHTML .= " ({$numberOfPremiums}x)";
	}
	
	$player->loadSkills();
	
	$infos = array();
	
	$infos[] = array("Horas de Stamina", (($player->getStamina() > 0) ? round($player->getStamina() / 1000 / 60 / 60) : "Nenhuma"));
	$infos[] = array("Numero da Conta", $account->getId());
	$infos[] = array("Nome da Conta", $account->getName());
	$infos[] = array("Email da Conta", $account->getEmail());
	$infos[] = array("Dias de Premium", $account->getPremDays());
	$infos[] = array("Alguma vez premium?", $alreadyIsPremiumHTML);
	$infos[] = array("Ultimo IP", $player->getIpAddress());
	$infos[] = array("Posição", "x:{$player->getPosX()} y:{$player->getPosY()} z:{$player->getPosZ()}");
	$infos[] = array("Player ID", $player->getId());
	$infos[] = array("Skill sword", $player->getSkill(t_Skills::Sword));
	$infos[] = array("Skill axe", $player->getSkill(t_Skills::Axe));
	$infos[] = array("Skill club", $player->getSkill(t_Skills::Club));
	$infos[] = array("Skill shield", $player->getSkill(t_Skills::Shielding));
	$infos[] = array("Skill distance", $player->getSkill(t_Skills::Distance));

	$table = new \Framework\HTML\Table();
	$table->AddField("Informações Avançadas", null, null, 2, true);
	$table->AddRow();
	
	foreach($infos as $k => $v)
	{
		if(is_array($v))
		{
			$table->AddField($v[0], 25);
			$table->AddField($v[1]);
			$table->AddRow();
	
			continue;
		}
	
		$table->AddField($v, null, null, 2);
		$table->AddRow();
	}
	
	$module .= $table->Draw();		
}
		
$module .= "</div>";

$infos = array();

if($bans->isBannished($account->getId()))
{
	$ban = $bans->getBannishment($account->getId());
	
	if($ban['type'] == 3 OR $ban['type'] == 5)
	{
		$ban_str .= "<font color='red'>";
			
		if($ban['type'] == 3)
		{
			$ban_str .= tr("Banido por").": <b>".\Core\Tools::getBanReason($ban['reason'])."</b><br>
			".tr("Duração: Até")." ".\Core\Main::formatDate($ban['expires']).".";
		}
		elseif($ban['type'] == 5)
		{
			$ban_str .= tr("Deletado por").": <b>".\Core\Tools::getBanReason($ban['reason'])."</b><br>
			".tr("Duração: permanentemente.");
		}
			
		$ban_str .= "</font>";
	}
	
	$infos[] = array(tr("Punição"), $ban_str);
}

$infos[] = array(tr("Tipo de Conta"), $premium);
$infos[] = array(tr("Nome Real"), $realname);
$infos[] = array(tr("Localização"), $location);
$infos[] = array(tr("Website"), $url);
$infos[] = array(tr("Criação"), \Core\Main::formatDate($account->getCreation()));

$module .= "
<div title='accounts' style='display: none; margin: 0px; padding: 0px;'>";

$table = new \Framework\HTML\Table();
$table->AddField(tr("Informações da Conta"), null, null, 2, true);
$table->AddRow();

foreach($infos as $k => $v)
{
	if(is_array($v))
	{
		$table->AddField($v[0], 25);
		$table->AddField($v[1]);
		$table->AddRow();

		continue;
	}

	$table->AddField($v, null, null, 2);
	$table->AddRow();
}

$module .= "{$table->Draw()}
</div>

<div title='statistics' style='display: none; margin: 0px; padding: 0px;'>";

if(Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $player->getWorldId()))
{	
	$table = new \Framework\HTML\Table();
	$table->AddField("Partidas em Battlegrounds", null, null, null, true);
	$table->AddRow();
	
	$table->AddField("Venceu {$player->getBattlegroundsWon()} partidas.");
	$table->AddRow();
	
	$table->AddField("Empatou {$player->getBattlegroundsDraw()} partidas.");
	$table->AddRow();
	
	$table->AddField("Perdeu {$player->getBattlegroundsLose()} partidas.");
	$table->AddRow();
	
	$table->AddField("Capturou {$player->getBattlegroundFlagsCaptured()} bandeiras.");
	$table->AddRow();
	
	$table->AddField("Foi morto carregando {$player->getBattlegroundFlagsDroped()} bandeiras.");
	$table->AddRow();
	
	$table->AddField("Recuperou {$player->getBattlegroundFlagsReturned()} bandeiras.");
	$table->AddRow();
	
	$table->AddField("Matou {$player->getBattlegroundFlagsKilled()} carregadores de bandeiras.");
	$table->AddRow();
	
	$module .= $table->Draw();
}

$table = new \Framework\HTML\Table();
$table->AddField(tr("Participação em Masmorras"), null, null, null, true);
$table->AddRow();

$table->AddField(tr("Ariadne - Trolls Wing: @v1@ tentativas e @v2@ completadas.", $player->getDungeonAriadneTrollsAttemps(), $player->getDungeonAriadneTrollsCompleted()));
$table->AddRow();

$module .= $table->Draw();

$table = new \Framework\HTML\Table();
$table->AddField(tr("Mortes Causadas"), null, null, null, true);
$table->AddRow();

$table->AddField(tr("Matou @v1@ jogadores.", $player->getTotalKills()));
$table->AddRow();

$table->AddField(tr("Participou da morte de @v1@ jogadores.", $player->getTotalAssists()));
$table->AddRow();

if(Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $player->getWorldId()))
{
	$table->AddField("Matou {$player->getTotalBgKills()} jogadores em battlegrounds.");
	$table->AddRow();
	
	$table->AddField("Participou da morte de {$player->getTotalBgAssists()} jogadores em battlegrounds.");
	$table->AddRow();
}

$module .= $table->Draw();
	
$table = new \Framework\HTML\Table();
$table->AddField(tr("Mortes Sofridas"), null, null, null, true);
$table->AddRow();

$table->AddField(tr("Foi morto @v1@ vezes (total).", $player->getTotalDeaths()));
$table->AddRow();

$table->AddField(tr("Foi morto @v1@ vezes com participação de jogadores.", $player->getTotalDeathsPlayers()));
$table->AddRow();

$table->AddField(tr("Foi morto @v1@ vezes com participação de criaturas.", $player->getTotalDeathsEnv()));
$table->AddRow();

if(Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $player->getWorldId()))
{
	$table->AddField(tr("Foi morto @v1@ vezes em battlegrounds.", $player->getTotalBgDeaths()));
	$table->AddRow();
}

$module .= $table->Draw();
			
if(is_array($deathlist))
{
	$module .= "

							
	<table cellspacing='0' cellpadding='0'>
		<tr>
			<th colspan='2'>".tr("Mortes Recentes")."</th>
		</tr>									
	";
	
	foreach($deathlist as $death_id)
	{
		$death_values = $deaths->load($death_id);
		
		$date = \Core\Main::formatDate($death_values['date']);
		
		$death = tr("Morto no Nivel @v1@ por ", $death_values['level']);
		
		if(count($death_values['killers']) != 0)
		{
			$k = 0;
			foreach($death_values['killers'] as $killer)
			{
				$k++;

				if($k > 1)
				{
					if(count($death_values['killers']) > 2)
					{
						if($k < count($death_values['killers']))
						{
							$death .= ", ";
						}
						elseif($k == count($death_values['killers']))
						{
							if($killer["isEnv"] == 1)
							{			
								$death .= " " . tr("e por um(a)"). " ";
							}	
							else	
								$death .= " " . tr("e por"). " ";
						}
					}
					else
					{
						if($killer["isEnv"] == 1)
						{			
							$death .= " " . tr("e por um(a)"). " ";
						}	
						else	
							$death .= " " . tr("e por"). " ";
					}	
				}		
				
				if($killer["isEnv"] == 1)
				{
					if($k == 1)		    
						$death .= tr("um(a) ");
					
					$explodeKiller = explode(" ", $killer['killer'], 2);
					
					$death .= "".$explodeKiller[1];
				}
				else
				{
					$_killer = new Player();	
					$_killer->load($killer['killer']);	

					$death .= "<a href='?ref=character.view&name={$_killer->getName()}'>{$_killer->getName()}</a>";
				}
			}
		}

		$death .= ".";
		
		$module .= "
			<tr>
				<td width='25%'>{$date}</td> <td>{$death}</td>
			</tr>					
		";	
	}
	
	$module .= "
	</table>";		
}		
		
if($logged_acc && ($account->getId() == $logged_acc->getId() || $logged_acc->getGroup() >= t_Group::GameMaster))
{
	$kills = $deaths->getKillsOfPlayer($player->getId());

	if(is_array($kills))
	{
		$module .= "
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='3'>".tr("Assassinatos Recentes")."</th>
			</tr>					
		";				
		
		foreach($kills as $kill)
		{
			$killed = new Player();
			$killed->load($kill["killed"]);
			
			$date = \Core\Main::formatDate($kill['date']);
			$isInjust = ($kill["injust"] == 1) ? "<font color='#ec0404'><b>".tr("injustificada")."</b></font>" : "<font color='#00ff00'><b>".tr("justificada")."</b></font>";
			
			$module .= "
				<tr>
					<td width='25%'>{$date}</td> <td width='50%'>".tr("Matou <a href='?ref=character.view&name=@v1@'>@v2@</a> no level @v3@", $killed->getName(), $killed->getName(), $kill["level"]).".</td> <td>{$isInjust}</td>
				</tr>					
			";					
		}
		
		$module .= "
		</table>";						
	}
	else
	{
		$module .= "
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='3'>".tr("Assassinatos Recentes")."</th>
			</tr>

			<tr>
				<td>".tr("Você não matou nenhum personagem recentemente").".</td>
			</tr>					
			
		</table>	
		";					
	}
}

$module .= "
</div>
		
<div title='achievements' style='display: none; margin: 0px; padding: 0px;'>";

$battleground_achievements = array(
	array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_RANK_BRAVE
			,title => "Rank - Bravo"
			, text => "Conquistou 1.000 pontos de classificação (rating).")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_RANK_VETERAN
			, title => "Rank - Veterano"
			, text => "Conquistou 1.500 pontos de classificação (rating).")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_RANK_LEGEND
			, title => "Rank - Lenda"
			, text => "Conquistou 2.000 pontos de classificação (rating).")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_ISANE_KILLER
			, title => "Matador insano!"
			, text => "Derrotou 25 oponentes sem ser derrotado nenhuma vez.")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_FLAG_CAPTURED
			, title => "Entregou uma bandeira!"
			, text => " Capturou e entregou uma bandeira a sua base colaborando com o seu time!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_PERFECT
			, title => "3x0, a partida perfeita!"
			, text => "Venceu uma partida aonde o time entregou 3 vezes a bandeira adversária sem deixar que entregassem nenhuma!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_FLAG_CATCHER
			, title => "Entregador de bandeiras!"
			, text => "Levou o time a vitória capturando a bandeira adversária por três vezes e as entregando a base numa mesma partida!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_MANY_FLAGS_RETUREND
			, title => "Recuperador de bandeiras!"
			, text => "Recuperou mais de 50 bandeiras prestando grande ajuda aos times que participou!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_FLAG_KILLER
			, title => "Matador preciso!"
			, text => "Aterrorizou os adversários matando 50 carregadores de bandeiras prestando grande ajuda aos times que participou!")
	
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_MANY_FLAG_CAPTURED
			, title => "Trabalhador incansavel!"
			, text => "Mostrou muita disposição capturando e entregando mais de 50 bandeiras prestando grande ajuda aos times que participou!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_SAVE_THE_DAY
			, title => "O melhor do dia!"
			, text => "Matou carregador da bandeira de seu time e a recuperou enquanto a partida estava em 2x2 contribuindo para a vitória de seu time na sequencia em grande estilo!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_EPIC_MATCH
			, title => "Não desiste nunca!"
			, text => "Estiveram perdendo por 0x2 mas em uma magnifica reação buscaram a virada e terminaram a partida vencendo por 3x2!")
		
	,array( "achiev" => Player::PH_ACHIEV_BATTLEGROUND_PERFECT_COLLECTOR
			, title => "Colecionador de partidas perfeitas!"
			, text => "Participou de mais de 10 partidas perfeitas, aonde venceram pelo resultado de 3x0!")
);

$dungeon_achievements = array(
	array( "name" => "Ariadne: Trolls Wing", "list" => array(
		array( "achiev" => Player::PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_GOT_ALL_TOTEMS, text => tr("Obteve os 12 totems para acessar o lar do Ghazran.") )
		,array( "achiev" => Player::PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_GOT_GHAZRAN_TONGUE, text => tr("Derrotou e obteve a lingua do Ghazran, o boss.") )
		,array( "achiev" => Player::PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_COMPLETE_WITHOUT_ANYONE_DIE, text => tr("Derrotou o Ghazran sem que ninguem do time morresse nesta tentativa inteira.") )
		,array( "achiev" => Player::PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_COMPLETE_IN_ONLY_ONE_ATTEMP, text => tr("Obteve os 12 totems e derrotou o Ghazran em apénas uma tentativa.") )
	))	
);

$misc_achievements = array(
	array( "achiev" => Player::PH_ACHIEV_MISC_GOT_LEVEL_100, text => tr("Atingiu level 100.") )
	,array( "achiev" => Player::PH_ACHIEV_MISC_GOT_LEVEL_200, text => tr("Atingiu level 200.") )
	,array( "achiev" => Player::PH_ACHIEV_MISC_GOT_LEVEL_300, text => tr("Atingiu level 300.") )
	,array( "achiev" => Player::PH_ACHIEV_MISC_GOT_LEVEL_400, text => tr("Atingiu level 400.") )
	,array( "achiev" => Player::PH_ACHIEV_MISC_GOT_LEVEL_500, text => tr("Atingiu level 500.") )
);

function showAchievement(&$table, &$player, $achiev)
{
	$achievInfo = $player->getAchievementInfo($achiev["achiev"]);
	
	if(!$achiev["title"])
		$string = "<span class='".($achievInfo["has"] ? "hasAchiev" : "notHasAchiev")."'>{$achiev["text"]}</span>";
	else
		$string = "<span class='".($achievInfo["has"] ? "hasAchiev" : "notHasAchiev")."'><h3 class='achievTitle'>{$achiev["title"]}</h3>{$achiev["text"]}</span>";

	if($achievInfo["has"])
	{
		if($achievInfo["date"] > 0)
			$string .= "<br><span class='gotAchiev'>".tr("Adquirido em @v1@.", \Core\Main::formatDate($achievInfo["date"]))."</span>";
	}

	$table->AddField($string);
	$table->AddRow();
}

$table = new \Framework\HTML\Table();
$table->AddField(tr("Variados"), null, null, null, true);
$table->AddRow();

foreach($misc_achievements as $achiev)
{
	showAchievement($table, $player, $achiev);
}

$module .= $table->Draw();

if(Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $player->getWorldId()))
{
	$table = new \Framework\HTML\Table();
	$table->AddField("Battlegrounds", null, null, null, true);
	$table->AddRow();	
	
	foreach($battleground_achievements as $achiev)
	{
		showAchievement($table, $player, $achiev);
	}
	
	$module .= $table->Draw();
}

foreach($dungeon_achievements as $dungeon)
{
	$table = new \Framework\HTML\Table();
	$table->AddField($dungeon["name"], null, null, null, true);
	$table->AddRow();

	foreach($dungeon["list"] as $achiev)
	{
		showAchievement($table, $player, $achiev);
	}	
	
	$module .= $table->Draw();
}

$module .= "</div>";		
		
		if($player->get("hide") == 0 || ($logged_acc && $logged_acc->getGroup() >= t_Group::GameMaster))
		{
			$module .= "
			<div title='accounts' style='display: none; margin: 0px; padding: 0px;'>
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='4'>".tr("Outros Personagens")."</th>
				</tr>					
			";			
			
			foreach($list as $player_name)
			{
				$character_list = new Player();
				$character_list->loadByName($player_name);
				
				if($character_list->get("hide") == 0 || ($logged_acc && $logged_acc->getGroup() >= t_Group::GameMaster))
				{
					$character_status = ($character_list->getOnline() == 1) ? "<font style='color: green; font-weight: bold;'>".tr("On-line")."</font>" : "<font style='color: red; font-weight: bold;'>".tr("Off-line")."</font>";
					
					$module .= "
						<tr>
							<td width='25%'>{$character_list->getName()}</td> <td width='10%'>{$character_list->getLevel()}</td> <td width='15%'>".t_Worlds::GetString($character_list->getWorldId())."</td>  <td>{$character_status}</td>
						</tr>					
					";						
				}				
			}

			$module .= "
			</table>
			</div>";		
		}
		
		
		$module .= "
		<p id='line1'></p>
		";
	}
}


if($error)	
{
	\Core\Main::sendMessageBox("Erro!", $error);
}

$module .= '
<form action="?ref=character.view" method="post">
	<fieldset>
		
		<p>
			<label for="search_value">'.tr("Nome").'</label><br />
			<input id="search_value" name="player_name" size="40" type="text" value="" autocomplete="off" onkeyup="requestSearchBoxPlayer(this.value)" />

			<div id="search_suggestions">
				<div id="search_suggestions_list">
				</div>
			</div>
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.tr("Enviar").'" />
		</p>
	</fieldset>
</form>';
?>

