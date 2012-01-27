CREATE TABLE `wb_forum_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1

CREATE TABLE `wb_forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `topic` text NOT NULL,
  `date` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `flags` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1

CREATE TABLE `wb_forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `post` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1

CREATE TABLE `wb_forum_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `end_date` int(11) NOT NULL,
  `flags` int(11) NOT NULL,
  `min_level` int(11) NOT NULL DEFAULT '20',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1

CREATE TABLE `wb_forum_polls_opt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `option` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1

CREATE TABLE `wb_forum_user_votes` (
  `user_Id` int(11) NOT NULL,
  `opt_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `public` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `darghos`.`wb_forum_bans` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER  NOT NULL,
  `date` INTEGER  NOT NULL,
  `type` INTEGER  NOT NULL,
  `author` INTEGER  NOT NULL,
  `reason` TEXT  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;