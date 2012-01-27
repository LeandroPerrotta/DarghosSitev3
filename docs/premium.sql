CREATE TABLE `wb_orders` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `period` varchar(255) NOT NULL,
  `cost` varchar(255) NOT NULL,
  `server` int(10) unsigned NOT NULL,
  `generated_by` int(10) unsigned NOT NULL,
  `generated_in` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `lastupdate_in` int(10) unsigned NOT NULL DEFAULT '0',
  `target_account` int(10) unsigned NOT NULL,
  `auth` varchar(255) NOT NULL DEFAULT '0',
  `email_vendor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;