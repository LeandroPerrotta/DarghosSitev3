<?php

define('MANUTENTION', 0);

define('CONFIG_SITEEMAIL', "http://localhost:8090");
define('SITE_ROOT_DIR', "/darghos_site"); //COMENTE ESTA LINHA CASO NO SEJA NECESSARIO DEFINIR UMA SUBPASTA

define('DIR_DATA', "/home/leandro/otserv/DarghosData/");
define('HOUSES_FILE', "world/-house.xml");
define('MONSTERS_FILE', "monster/monsters.xml");

define('GLOBAL_LANGUAGE', "pt");

define('CONFIG_SITENAME', "Darghos Server");
define('CONFIG_OWNERNAME', "Equipe UltraxSoft");

/*	Configuraes SMTP	 */
define('SMTP_HOST', "smtp-auth.no-ip.com");
define('SMTP_PORT', 3325);
define('SMTP_USER', "darghos.net@noip-smtp");
define('SMTP_PASS', "***REMOVED***");

/* CONFIGURAES PARA STATUS */
define('STATUS_ADDRESS', '127.0.0.1');
define('STATUS_PORT', 7171);

define('SERVER_ID', 1);
define('USEREMOTECONNECTIONS', 0);

/* Entrega de item comprados no ItemShop  feita via Lua Scripts, baseado no StorageID do jogador */
define('STORAGE_SHOPSYS_ITEM_ID', 8985);
define('STORAGE_SHOPSYS_ITEM_COUNT', 8986);
define('STORAGE_SHOPSYS_ID', 8987);
define('STORAGE_REBORNS', 8899);

define('ENCRYPTION_TYPE', "md5");

/* ID PARA IDENTIFICAO DAS PAGINAS DE TEXTO ARMAZENADAS NO BANCO DE DADOS */
define('DBPAGES_HOWPLAY', 1);
define('DBPAGES_ABOUT', 2);
define('DBPAGES_PREMIUMFEATURES', 3);
define('DBPAGES_DARGHOPEDIA_REBORN', 4);
define('DBPAGES_DARGHOPEDIA_QUESTS', 5);

define('PREMTEST_DAYS', 10); //Dias que os jogadores podero receber de PremTest, para quem atingir level 100 e nunca ter possuido uma premium account.
define('DAYS_TO_CHANGE_EMAIL', 15); //Dias de espera necessarios para uma mudana de e-mail agendada
define('DAYS_TO_DELETE_CHARACTER', 30); //Dias de espera necessarios para deletar um personagem da conta
define('SHOW_DEATHS_DAYS_AGO', 30); //Limite de mortes dias atrz que sero exibidas
define('HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS', 0); //Possibilidade de ignorar personagens no rank que no entraram no jogo a X dias
define('HIDE_FORUMLINKS', 0); //Esconde os links ligados a forums, como o Comentar nas noticias.
define('SHOW_SHOPFEATURES', 1); //Exibi features de Shop, como o Item Shop, Change Name, Change Sex e etc (0 = hide, 1 = show)

define('ENABLE_REBORN_SYSTEM', 1); //Ativa o sistema de reborn
define('FIRST_REBORN_LEVEL', 200); //level minimo para o primeiro reborn
/*
define('SHOW_PREMIUMFEATURES', 1); //Exibi as opes de Premium como Vantagens, Sistema de Compra, Historico e etc (0 = hide, 1 = show)
*/

//Ativa a validação por email para modulos como criao de personagem e recuperação de contas entre outros IMPORTANTE: ATIVAO REQUER SERVIDOR SMTP CONFIGURADO
define('USE_EMAILVALIDATION', true); 

// Constantes para Guildas
define('GUILDS_FORMATION_DAYS', 5); //Limite de dias para guildas serem formadas
define('GUILDS_VICELEADERS_NEEDED', 4); //Quantidade de vice-leaders necessarios para uma guild ser formada
define('GUILD_IMAGE_DIR', "files/guildImages/");

// E-mails Constantes
define('EMAIL_REGISTER', 1);
define('EMAIL_RECOVERY_ACCOUNT', 2);
define('EMAIL_RECOVERY_PASSWORDKEY', 3);
define('EMAIL_RECOVERY_BOTH', 4);
define('EMAIL_RECOVERY_PASSWORD', 5);

//FEATURES SHOP
define('PREMDAYS_TO_CHANGENAME', 15);
define('PREMDAYS_TO_CHANGESEX', 10);

define('CONTRIBUTE_PAYPALEMAIL', "premium@darghos.com");
define('CONTRIBUTE_PAYPALURL', "https://www.paypal.com/cgi-bin/webscr");
define('CONTRIBUTE_PAGSEGUROURL', "https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx");

define('SLOT_HEAD', 1);
define('SLOT_BACKPACK', 3);
define('SLOT_ARMOR', 4);
define('SLOT_RIGHTHAND', 5);
define('SLOT_LEFTHAND', 6);
define('SLOT_LEGS', 7);
define('SLOT_FEET', 8);
define('SLOT_AMMO', 10);	

define('BANTYPE_IP_BANISHMENT', 1);
define('BANTYPE_NAMELOCK', 2);
define('BANTYPE_BANISHMENT', 3);
define('BANTYPE_NOTATION', 4);
define('BANTYPE_DELETION', 5);

//Outras
define('CHARACTERS_AJAX_REQUEST', 5); //numero de players retornados pela consulta ajax
?>