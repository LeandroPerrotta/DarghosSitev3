<?

	define(CONFIG_SITENAME, "Darghos Tenerian");
	define(CONFIG_SITEEMAIL, "http://tenerian.darghos.com.br/");

	define(DB_TENERIAN_HOST, "localhost:3309");
	define(DB_TENERIAN_USER, "root");
	define(DB_TENERIAN_PASS, "W2e8EQaR");
	define(DB_TENERIAN_SCHEMA, "tenerian");
	define(DB_WEBSITE_PREFIX, "wb_");
	
	define(SMTP_HOST, "smtp-auth.no-ip.com");
	define(SMTP_PORT, 587);
	define(SMTP_USER, "darghos.net@noip-smtp");
	define(SMTP_PASS, "***REMOVED***");
	
	define(ENCRYPTION_TYPE, "md5");
	
	define(EMAIL_REGISTER, 1);
	
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
	
	$_sex['female'] = 0;
	$_sex['male'] = 1;
	
	$_sexid[0] = "Feminino";
	$_sexid[1] = "Masculino";
	

?>