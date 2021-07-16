CREATE TABLE  `wb_auctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `min_bid` int(11) NOT NULL,
  `current_bid` int(11) DEFAULT '0',
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB

CREATE TABLE  `wb_auction_items` (
  `auction_id` int(11) NOT NULL,
  `itemtype` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `attributes` text NOT NULL,
  KEY `wb_auction_items_ibkf_1` (`auction_id`),
  CONSTRAINT `wb_auction_items_ibkf_1` FOREIGN KEY (`auction_id`) REFERENCES `wb_auctions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB

CREATE TABLE  `wb_auction_bids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wb_auction_bids_fk_1` (`auction_id`),
  KEY `wb_auction_bids_fk_2` (`player_id`),
  CONSTRAINT `wb_auction_bids_fk_1` FOREIGN KEY (`auction_id`) REFERENCES `wb_auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wb_auction_bids_fk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB