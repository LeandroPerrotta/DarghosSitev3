<?php

$total = explode(" ", microtime());
$_miliseconds = explode(".", $total[0]);
$msnow = str_split($_miliseconds[1], 3);
$miliseconds = $total[1].$msnow[0];
$ping = $miliseconds - $value;

$db->query("INSERT INTO wb_pingtest VALUES ('{$ping}', '{$_SERVER['REMOTE_ADDR']}', '{$total[1]}')");

echo $ping." ms";
?>