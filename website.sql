CREATE TABLE `wb_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_blackliststrings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `string` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_changelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_changepasswordkeys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `password_key` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_emailstochange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_iptries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(255) NOT NULL,
  `tries` int(10) unsigned NOT NULL,
  `last_trie` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_itemshop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `itemlist_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `received` int(10) unsigned NOT NULL DEFAULT '0',
  `account_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

CREATE TABLE `wb_itemshop_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `special` varchar(255) NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL,
  `actived` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(45) NOT NULL DEFAULT '0',
  `topic` varchar(255) DEFAULT NULL,
  `post` text,
  `post_data` int(10) unsigned NOT NULL DEFAULT '0',
  `post_update` int(10) unsigned NOT NULL,
  `forum_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `wb_playerdeletion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_secretkeys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `secret_key` varchar(255) NOT NULL,
  `lembrete` varchar(255) NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

ALTER TABLE `accounts` ADD `real_name` VARCHAR(255) AFTER `group_id`;
ALTER TABLE `accounts` ADD `location` VARCHAR(255) AFTER `group_id`;
ALTER TABLE `accounts` ADD `url` VARCHAR(255) AFTER `group_id`;
ALTER TABLE `accounts` ADD `creation` INTEGER NOT NULL DEFAULT 0 AFTER `url`;
ALTER TABLE `guilds` ADD `image` VARCHAR(255) NOT NULL AFTER `motd`;
ALTER TABLE `guilds` ADD `status` INT(10) NOT NULL DEFAULT 0 AFTER `motd`;
ALTER TABLE `guilds` ADD `formationTime` INT(10) NOT NULL DEFAULT 0 AFTER `motd`;
ALTER TABLE `guild_invites` ADD `invitedIn` INT(10) NOT NULL DEFAULT 0 AFTER `guild_id`;
ALTER TABLE `players` CHANGE `description` `description` TEXT;
ALTER TABLE `players` ADD `hidden` INT(10) NOT NULL DEFAULT 0 AFTER `description`;
ALTER TABLE `players` ADD `created` INT(10) NOT NULL DEFAULT 0 AFTER `description`;
ALTER TABLE `players` ADD `guild_join_date` INT(10) NOT NULL DEFAULT 0 AFTER `description`;

