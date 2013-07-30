<?php

$db = \Core\Main::$DB;

$timestamps = array();

$timestamps["today"] = mktime(0, 0, 0);
$timestamps["yesterday"] = $timestamps["today"] - (60 * 60 * 24);

function getTimestampByDaysAgo($days = 1){
    global $timestamps;
    
    $start = $timestamps["today"] - (60 * 60 * 24 * $days);
    $end = $timestamps["today"];
    
    if($days > 1)
        $end = $timestamps["today"] - (60 * 60 * 24 * ($days - 1));
    
    return array($start, $end);
}

$online_uniquePlayers = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE `online` = 1 AND `group_id` < 3 ORDER BY `lastip`")->numRows();


$uniquePlayers_today = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE `login` >= {$timestamps["today"]}")->numRows();
$uniquePlayers_one_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(1))->numRows();
$uniquePlayers_two_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(2))->numRows();
$uniquePlayers_three_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(3))->numRows();
$uniquePlayers_four_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(4))->numRows();
$uniquePlayers_five_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(5))->numRows();
$uniquePlayers_six_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(6))->numRows();
$uniquePlayers_seven_day_ago = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE " . getTimestampByDaysAgo(7))->numRows();

$rows = "";

for($i = 1; $i < 30; $i++){
    
    list($start, $end) = getTimestampByDaysAgo($i);
    $qtd = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE `login` >= {$start} AND `login` < {$end}");
    
    $start_str = date("d-m h:m:s", $start);
    $end_str = date("d-m h:m:s", $end);
    
    $rows .= "
    <tr>
        <td>{$start_str}</td>
        <td>{$end_str}</td>
        <td>{$qtd}</td>
    </tr>
    ";
}


$module .= "

<h2>Jogadores ativos</h2>

<table cellspacing='0' cellpadding='0' id='table'>

    <tr>
        <th>Inicio</th>
        <th>Fim</th>
        <th>Quantidade</th>
    </tr>
    
    {$rows}

</table>

";

?>