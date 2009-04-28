<?
set_time_limit(0);

$contribute = $core->loadClass("Contribute");
$contribute->importPayments();
?>