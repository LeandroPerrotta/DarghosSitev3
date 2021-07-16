<?php

$_inputsWhiteList = array("account.login", "guilds.edit");

$_vocation['no-vocation'] = 0;
$_vocation['sorcerer'] = 1;
$_vocation['druid'] = 2;
$_vocation['paladin'] = 3;
$_vocation['knight'] = 4;

$_vocationid[0] = "No Vocation";
$_vocationid[1] = "Sorcerer";
$_vocationid[2] = "Druid";
$_vocationid[3] = "Paladin";
$_vocationid[4] = "Knight";	
$_vocationid[5] = "Master Sorcerer";
$_vocationid[6] = "Elder Druid";
$_vocationid[7] = "Royal Paladin";
$_vocationid[8] = "Elite Knight";	

$_townid = array(
	1 => array("name" => "Quendor", 			"canCreate" => 0, 		"premium" => 0),
	2 => array("name" => "Aracura", 			"canCreate" => 0, 		"premium" => 1),
	3 => array("name" => "Rookgaard", 			"canCreate" => 0, 		"premium" => 0),
	4 => array("name" => "Thorn", 				"canCreate" => 0, 		"premium" => 0),
	5 => array("name" => "Salazart", 			"canCreate" => 0, 		"premium" => 1),
	6 => array("name" => "Island of Peace", 	"canCreate" => 1, 		"premium" => 0),
	7 => array("name" => "Northrend", 			"canCreate" => 0, 		"premium" => 1)
);

$_skill['fist'] = 0;
$_skill['club'] = 1;
$_skill['sword'] = 2;
$_skill['axe'] = 3;
$_skill['distance'] = 4;
$_skill['shield'] = 5;
$_skill['fishing'] = 6;		

$_sex['female'] = 0;
$_sex['male'] = 1;

$_sexid[0] = "Feminino";
$_sexid[1] = "Masculino";

$_itemshopstatus[0] = "Aguardando log-in";
$_itemshopstatus[1] = "Recebido";	

$_contribution['emailadmin'] = (date("d", time()) <= 15) ? "premium@darghos.com" : "admin@darghos.com";

$_contribution['PagSeguro']["7"] = "R$ 2.90";
$_contribution['PagSeguro']["30"] = "R$ 8.90";
$_contribution['PagSeguro']["60"] = "R$ 16.55";
$_contribution['PagSeguro']["90"] = "R$ 22.90";
$_contribution['PagSeguro']["180"] = "R$ 41.55";

$_contribution['PayPal']["7"] = "USD 1.90";
$_contribution['PayPal']["30"] = "USD 5.90";
$_contribution['PayPal']["60"] = "USD 8.55";
$_contribution['PayPal']["90"] = "USD 13.55";
$_contribution['PayPal']["180"] = "USD 24.90";

$_contribution['status'][0] = "Aguardando Pagamento.";
$_contribution['status'][1] = "Confirmado";
$_contribution['status'][2] = "Concluido";
$_contribution['status'][3] = "Cancelado";	

$_banactionid[0] = "Notificado";
$_banactionid[1] = "Nome denunciado";
$_banactionid[2] = "Banido";
$_banactionid[3] = "Nome denunciado + Banido";
$_banactionid[4] = "Aviso final com banimento";
$_banactionid[5] = "Nome denunciado com Banido com Aviso final";
$_banactionid[6] = "Relatrio declarado";
$_banactionid[7] = "Deletado";

$_bantypeid[1] = "IP banido";
$_bantypeid[2] = "Nome bloqueado";
$_bantypeid[3] = "Conta banida";
$_bantypeid[4] = "Conta notificada";
$_bantypeid[5] = "Conta deletada";
	
?>
