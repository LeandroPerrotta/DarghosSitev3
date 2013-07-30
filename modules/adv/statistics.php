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
    
    return "`lastlogin` >= {$start} AND `lastlogin` < {$end}";
}

$online_uniquePlayers = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE `online` = 1 AND `group_id` < 3 ORDER BY `lastip`")->numRows();


$uniquePlayers_today = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE `lastlogin` >= {$timestamps["today"]} AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_one_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(1) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_two_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(2) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_three_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(3) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_four_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(4) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_five_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(5) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_six_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(6) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();
$uniquePlayers_seven_day_ago = $db->query("SELECT DISTINCT `lastip` FROM `players` WHERE " . getTimestampByDaysAgo(7) . "AND `group_id` < 3 ORDER BY `lastip`")->numRows();

$module .= "

<h2>Jogadores ativos</h2>

<table>

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
        <th>{$online_uniquePlayers}</th>
        <th>{$uniquePlayers_today}</th>
        <th>{$uniquePlayers_one_day_ago}</th>
        <th>{$uniquePlayers_two_day_ago}</th>   
        <th>{$uniquePlayers_three_day_ago}</th>   
        <th>{$uniquePlayers_four_day_ago}</th>   
        <th>{$uniquePlayers_five_day_ago}</th>   
        <th>{$uniquePlayers_six_day_ago}</th>   
        <th>{$uniquePlayers_seven_day_ago}</th>   
    </tr>

</table>

";

?>