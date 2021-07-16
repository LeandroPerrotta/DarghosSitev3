CREATE TABLE `darghos`.`wb_tickets` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` INTEGER UNSIGNED NOT NULL,
  `account` VARCHAR(45) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `question` VARCHAR(255) NOT NULL,
  `send_date` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `closed` VARCHAR(45) NOT NULL, 
  `last_update` VARCHAR(45) NOT NULL,
  `fixed` INTEGER UNSIGNED NOT NULL,
  `attachment` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;

CREATE TABLE `darghos`.`wb_tickets_answers` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` VARCHAR(45) NOT NULL,
  `text` TEXT NOT NULL,
  `by_name` VARCHAR(45) NOT NULL,
  `send_date` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)

ALTER TABLE `darghos`.`wb_tickets` ADD COLUMN `fixed` INTEGER UNSIGNED NOT NULL AFTER `last_update`;
ALTER TABLE `darghos`.`wb_tickets_answers` MODIFY COLUMN `text` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
, ROW_FORMAT = DYNAMIC;
