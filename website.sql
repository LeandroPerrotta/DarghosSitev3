ALTER TABLE `darghos`.`wb_iptries` MODIFY COLUMN `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
 CHANGE COLUMN `tries` `date` INTEGER UNSIGNED NOT NULL,
 DROP COLUMN `last_trie`;


/*
 * AUCTION ITEMS
 */

CREATE TABLE `wb_auction_bids` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `auction_id` INTEGER  NOT NULL,
  `player_id` INTEGER  NOT NULL,
  `bid` INTEGER  NOT NULL,
  `date` INTEGER  NOT NULL,
  `enabled` INTEGER  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;

CREATE TABLE `wb_auction_products` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `type` INTEGER  NOT NULL,
  `name` VARCHAR(255)  NOT NULL,
  `description` TEXT  NOT NULL,
  `params` TEXT  NOT NULL,
  `min_bid` INTEGER  NOT NULL DEFAULT 0,
  `creation` INTEGER  NOT NULL,
  `start_in` INTEGER  NOT NULL,
  `end_in` INTEGER  NOT NULL,
  PRIMARY KEY (`id`)
)

/*
 * END
 */

CREATE TABLE `wb_guild_wars` (
  `war_id` INTEGER  NOT NULL,
  `reply` INTEGER  NOT NULL DEFAULT 0,
  `comment` INTEGER  NOT NULL
)
ENGINE = InnoDB;

CREATE TABLE `wb_guilds` (
  `guild_id` INTEGER  NOT NULL,
  `image` VARCHAR(255)  NOT NULL,
  `status` INTEGER  NOT NULL,
  `formationTime` INTEGER  NOT NULL,
  `guild_points` INTEGER  NOT NULL,
  `guild_better_points` INTEGER  NOT NULL
)
ENGINE = InnoDB;

DELIMITER |
CREATE TRIGGER `oncreate_guilds`
AFTER INSERT
ON `guilds`
FOR EACH ROW
BEGIN
	INSERT INTO `guild_ranks` (`name`, `level`, `guild_id`) VALUES ('Leader', 6, NEW.`id`);
	INSERT INTO `guild_ranks` (`name`, `level`, `guild_id`) VALUES ('Vice-Leader', 5, NEW.`id`);
	INSERT INTO `guild_ranks` (`name`, `level`, `guild_id`) VALUES ('Member', 4, NEW.`id`);
END |

CREATE TABLE `wb_players` (
  `player_id` INTEGER  NOT NULL,
  `creation` INTEGER  NOT NULL,
  `visible` INTEGER  NOT NULL,
  `comment` text,
  `guildjoin` INTEGER  NOT NULL DEFAULT 0
)
ENGINE = InnoDB;

CREATE TABLE `wb_accounts_personal` (
  `account_id` INTEGER  NOT NULL,
  `real_name` VARCHAR(255)  NOT NULL,
  `location` VARCHAR(255)  NOT NULL,
  `url` VARCHAR(255)  NOT NULL,
  `creation` INTEGER  NOT NULL
)
ENGINE = InnoDB;

CREATE TABLE `wb_tutortest_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_tutortest_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 	
  `question_id` int(10) unsigned NOT NULL,
  `answer` varchar(255) NOT NULL DEFAULT '0',
  `correct` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE `wb_fastnews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(45) NOT NULL DEFAULT '0',
  `post` text,
  `post_data` int(10) unsigned NOT NULL DEFAULT '0',
  `post_update` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `wb_premiumtest` (
  `account_id` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL
) ENGINE=InnoDB;

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

CREATE TABLE `wb_itemshop_use_log` (
  `log_id` INTEGER  NOT NULL,
  `player_id` INTEGER  NOT NULL,
  `date` INTEGER  NOT NULL
)
ENGINE = InnoDB;

CREATE TABLE `wb_itemshop_log` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `shop_id` INTEGER  NOT NULL,
  `date` INTEGER  NOT NULL,
  `player_id` INTEGER  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `wb_itemshop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `added_in` int(10) unsigned NOT NULL,
  `enabled` int(10) unsigned NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '0',
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

CREATE TABLE `wb_pingtest` (
  `latency` varchar(255) unsigned NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `guild_invites` (
  `player_id` int(10) unsigned NOT NULL,
  `guild_id` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `guild_invites` ADD `date` INTEGER NOT NULL AFTER `guild_id`;
ALTER TABLE `guilds` ADD `motd` VARCHAR(255) NOT NULL AFTER `creationdata`;
ALTER TABLE `guilds` ADD `image` VARCHAR(255) NOT NULL AFTER `motd`;
ALTER TABLE `guilds` ADD `status` INT(10) NOT NULL DEFAULT 0 AFTER `image`;
ALTER TABLE `guilds` ADD `formationTime` INT(10) NOT NULL DEFAULT 0 AFTER `status`;
ALTER TABLE `players` ADD `description` VARCHAR(255) NOT NULL AFTER `stamina`;
ALTER TABLE `players` ADD `hidden` INT(10) NOT NULL DEFAULT 0 AFTER `description`;
ALTER TABLE `players` ADD `created` INT(10) NOT NULL DEFAULT 0 AFTER `description`;
ALTER TABLE `players` ADD `guild_join_date` INT(10) NOT NULL DEFAULT 0 AFTER `description`;
ALTER TABLE `players` ADD `is_spoof` TINYINT(1) NOT NULL DEFAULT 0;
