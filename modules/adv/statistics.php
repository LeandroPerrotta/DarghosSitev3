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
    
    return "`login` >= {$start} AND `login` < {$end}";
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

$module .= "

<h2>Jogadores ativos</h2>

<table cellspacing='0' cellpadding='0' id='table'>

    <tr>
        <th>Agora</th>
        <th>Hoje</th>
        <th>Ontem</th>
        <th>2 dias</th>
        <th>3 dias</th>
        <th>4 dias</th>
        <th>5 dias</th>
        <th>6 dias</th>
        <th>7 dias</th>
    </tr>
    
    <tr>
        <td>{$online_uniquePlayers}</td>
        <td>{$uniquePlayers_today}</td>
        <td>{$uniquePlayers_one_day_ago}</td>
        <td>{$uniquePlayers_two_day_ago}</td>   
        <td>{$uniquePlayers_three_day_ago}</td>   
        <td>{$uniquePlayers_four_day_ago}</td>   
        <td>{$uniquePlayers_five_day_ago}</td>   
        <td>{$uniquePlayers_six_day_ago}</td>   
        <td>{$uniquePlayers_seven_day_ago}</td>   
    </tr>

</table>

";

?>