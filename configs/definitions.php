<?php

define('MANUTENTION', true);
define('MANUTENTION_TEST_SERVER', false);
define('MANUTENTION_TITLE', "Em manutenção");
define('MANUTENTION_BODY', "Estamos em manutenção, voltaremos em breve.");

define('CONFIG_SITEEMAIL', "http://www.darghos.com.br");
#define('SITE_ROOT_DIR', "/darghos_site"); //COMENTE ESTA LINHA CASO NO SEJA NECESSARIO DEFINIR UMA SUBPASTA

define('DISTRO_TFS', 'tfs');
define('DISTRO_OPENTIBIA', 'opentibia');

define('SERVER_DISTRO', DISTRO_TFS);

define('DIR_DATA', "C:/otserv/tfs/8.6/data/");
define('HOUSES_FILE', "world/-house.xml");
define('MONSTERS_FILE', "monster/monsters.xml");

define('GLOBAL_LANGUAGE', "pt");

define('CONFIG_SITENAME', "Darghos Server");
define('CONFIG_OWNERNAME', "Equipe UltraxSoft");

/*	Configuraes SMTP	 */
/*define('SMTP_HOST', "smtp-auth.no-ip.com");
define('SMTP_PORT', 3325);
define('SMTP_USER', "darghos.net@noip-smtp");
define('SMTP_PASS', "***REMOVED***");*/

define('SMTP_HOST', "mail.darghos.com.br");
define('SMTP_PORT', 25);
define('SMTP_USER', "webadmin@darghos.com.br");
define('SMTP_PASS', "***REMOVED***");

/* CONFIGURAES PARA STATUS */
define('STATUS_ADDRESS', 'darghos.com.br');
define('STATUS_PORT', 7171);

define('SERVER_ID', 1);
define('USEREMOTECONNECTIONS', 0);

/* Entrega de item comprados no ItemShop  feita via Lua Scripts, baseado no StorageID do jogador */
define('STORAGE_SHOPSYS_ITEM_ID', 8985);
define('STORAGE_SHOPSYS_ITEM_COUNT', 8986);
define('STORAGE_SHOPSYS_ID', 8987);
define('STORAGE_REBORNS', 15300);

define('ENCRYPTION_TYPE', "md5");

/* ID PARA IDENTIFICAO DAS PAGINAS DE TEXTO ARMAZENADAS NO BANCO DE DADOS */
define('DBPAGES_HOWPLAY', 1);
define('DBPAGES_ABOUT', 2);
define('DBPAGES_PREMIUMFEATURES', 3);
define('DBPAGES_DARGHOPEDIA_REBORN', 4);
define('DBPAGES_DARGHOPEDIA_QUESTS', 5);
define('DBPAGES_DARGHOPEDIA_WORLD', 6);
define('DBPAGES_DARGHOPEDIA_PVP_ARENAS', 7);
define('DBPAGES_DARGHOPEDIA_WEEK_EVENTS', 8);

define('PREMTEST_DAYS', 10); //Dias que os jogadores podero receber de PremTest, para quem atingir level 100 e nunca ter possuido uma premium account.
define('DAYS_TO_CHANGE_EMAIL', 15); //Dias de espera necessarios para uma mudana de e-mail agendada
define('DAYS_TO_DELETE_CHARACTER', 30); //Dias de espera necessarios para deletar um personagem da conta
define('SHOW_DEATHS_DAYS_AGO', 30); //Limite de mortes dias atrz que sero exibidas
define('HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS', 2); //Possibilidade de ignorar personagens no rank que no entraram no jogo a X dias
define('ENABLE_NEW_COMMENTS', 1); //Habilitar ou não a possibilidade dos jogadores comentarem em noticias.
define('SHOW_SHOPFEATURES', 1); //Exibi features de Shop, como o Item Shop, Change Name, Change Sex e etc (0 = hide, 1 = show)
define('ENABLE_BUY_STAMINA', 0); //Ativa o sistema de comprar stamina (0 = disabled, 1 = enabled)
define('ENABLE_GUILD_READ_ONLY', 0); //Se ativo no site só será exibido informações da guilda, mas não será possivel fazer qualqeur alteração nela
define('ENABLE_GUILD_WARS', 0); //Ativa o sistema de Guild Wars pelo site
define('ENABLE_GUILD_FORMATION', 1); //Ativa a opção que as guilds são exibidas separadas em categorias por ativas e em formação, estão opção deve ser desativada para TFS (0 = disabled, 1 = enabled)
define('ENABLE_GUILD_POINTS', 0); //Sistema de pontuação de guildas (0 = disabled, 1 = enabled)
define('SHOW_NEWS', 6); //quantidade de noticias por pagina que deverá ser exibida na pagina inicial

define('ENABLE_REBORN_SYSTEM', 0); //Ativa o sistema de reborn
define('FIRST_REBORN_LEVEL', 200); //level minimo para o primeiro reborn

define('ENABLE_PVP_SWITCH', 1); //ativa o sistema de troca de pvp
/*
define('SHOW_PREMIUMFEATURES', 1); //Exibi as opes de Premium como Vantagens, Sistema de Compra, Historico e etc (0 = hide, 1 = show)
*/

//Ativa a validação por email para modulos como criao de personagem e recuperação de contas entre outros IMPORTANTE: ATIVAO REQUER SERVIDOR SMTP CONFIGURADO
define('USE_EMAILVALIDATION', true); 

define('REMOVE_AFK_FROM_STATUS', true);

// Constantes para Guildas
define('GUILDS_FORMATION_DAYS', 5); //Limite de dias para guildas serem formadas
define('GUILDS_VICELEADERS_NEEDED', 4); //Quantidade de vice-leaders necessarios para uma guild ser formada
define('GUILD_IMAGE_DIR', "files/guildImages/");

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