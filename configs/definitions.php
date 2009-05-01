<?php

define('MANUTENTION', 1);

define('CONFIG_SITEEMAIL', "http://localhost/DarghosNew2.0");

define('DIR_DATA', "C:/Server/server/data/");
define('HOUSES_FILE', "world/test-house.xml");
define('MONSTERS_FILE', "monster/monsters.xml");

define('CONFIG_SITENAME', "Darghos Tenerian");
define('CONFIG_OWNERNAME', "Equipe UltraxSoft");

/*	Configurações SMTP	 */
define('SMTP_HOST', "darghos.net");
define('SMTP_PORT', 25);
define('SMTP_USER', "no-reply@darghos.net");
define('SMTP_PASS', "gu166887");

define('SERVER_ID', 1);
define('USEREMOTECONNECTIONS', 0);

/* Entrega de item comprados no ItemShop é feita via Lua Scripts, baseado no StorageID do jogador */
define('STORAGE_ID_ITEMSHOP', 8987);

define('ENCRYPTION_TYPE', "md5");

/* ID PARA IDENTIFICAÇÃO DAS PAGINAS DE TEXTO ARMAZENADAS NO BANCO DE DADOS */
define('DBPAGES_HOWPLAY', 1);
define('DBPAGES_ABOUT', 2);
define('DBPAGES_PREMIUMFEATURES', 3);

define('DAYS_TO_CHANGE_EMAIL', 15); //Dias de espera necessarios para uma mudança de e-mail agendada
define('DAYS_TO_DELETE_CHARACTER', 30); //Dias de espera necessarios para deletar um personagem da conta
define('SHOW_DEATHS_DAYS_AGO', 30); //Limite de mortes dias atráz que serão exibidas
define('HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS', 1); //Possibilidade de ignorar personagens no rank que não entraram no jogo a X dias
define('HIDE_FORUMLINKS', 1); //Esconde os links ligados a forums, como o Comentar nas noticias.
define('SHOW_SHOPFEATURES', 1); //Exibi features de Shop, como o Item Shop, Change Name, Change Sex e etc (0 = hide, 1 = show)
/*
define('SHOW_PREMIUMFEATURES', 1); //Exibi as opções de Premium como Vantagens, Sistema de Compra, Historico e etc (0 = hide, 1 = show)

define('USE_VALIDATIONBYEMAIL', 1); //Ativa validação por email para modulos como criação de personagem e recuperação de contas e etc (0 = hide, 1 = show) IMPORTANTE: ATIVAÇÃO REQUER SERVIDOR SMTP CONFIGURADO
*/

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
?>