<?

	define(CONFIG_SITENAME, "Darghos Tenerian");
	define(CONFIG_SITEEMAIL, "http://tenerian.darghos.com.br");
	
	define(SERVER_ID, 1);

	define(DB_TENERIAN_HOST, "localhost:3309");
	define(DB_TENERIAN_USER, "root");
	define(DB_TENERIAN_PASS, "W2e8EQaR");
	define(DB_TENERIAN_SCHEMA, "tenerian");
	define(DB_WEBSITE_PREFIX, "wb_");
	
	define(DB_ULTRAXSOFT_HOST, "174.36.198.61:3309");
	define(DB_ULTRAXSOFT_USER, "external");
	define(DB_ULTRAXSOFT_PASS, "v5cruzet");
	define(DB_ULTRAXSOFT_SCHEMA, "ultraxsoft_admin");
	
	define(SMTP_HOST, "smtp-auth.no-ip.com");
	define(SMTP_PORT, 587);
	define(SMTP_USER, "darghos.net@noip-smtp");
	define(SMTP_PASS, "***REMOVED***");
	
	define(ENCRYPTION_TYPE, "md5");
	
	define(DAYS_TO_CHANGE_EMAIL, 15);
	define(SHOW_DEATHS_DAYS_AGO, 30);
	
	define(EMAIL_REGISTER, 1);
	
	define(CONTRIBUTE_EMAILADMIN, "premium@darghos.com");
	define(CONTRIBUTE_PAYPALURL, "https://www.paypal.com/cgi-bin/webscr");
	define(CONTRIBUTE_PAGSEGUROURL, "https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx");
	
	define('SLOT_HEAD', 1);
	define('SLOT_BACKPACK', 3);
	define('SLOT_ARMOR', 4);
	define('SLOT_RIGHTHAND', 5);
	define('SLOT_LEFTHAND', 6);
	define('SLOT_LEGS', 7);
	define('SLOT_FEET', 8);
	define('SLOT_AMMO', 10);		
	
	$_inputsWhiteList = array();
	
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
	
	$_townid[1] = "Quendor";
	$_townid[2] = "Aracura";
	$_townid[3] = "Rookgaard";
	$_townid[4] = "Thorn";
	$_townid[5] = "Salazart";
	$_townid[7] = "Northrend";
	
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
	
	$_contribution['PagSeguro']["30"] = "R$ 14.55";
	$_contribution['PagSeguro']["60"] = "R$ 25.55";
	$_contribution['PagSeguro']["90"] = "R$ 35.55";
	$_contribution['PagSeguro']["180"] = "R$ 49.55";
	$_contribution['PagSeguro']["360"] = "R$ 75.55";
	
	$_contribution['PayPal']["30"] = "USD 6.45";
	$_contribution['PayPal']["60"] = "USD 11.45";
	$_contribution['PayPal']["90"] = "USD 20.45";
	$_contribution['PayPal']["180"] = "USD 29.95";
	$_contribution['PayPal']["360"] = "USD 39.95";	
	
	$_contribution['status'][0] = "Aguardando Pagamento.";
	$_contribution['status'][1] = "Confirmado";
	$_contribution['status'][2] = "Concluido";
	$_contribution['status'][3] = "Cancelado";
	
?>