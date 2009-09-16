CREATE TABLE `darghos`.`wb_tickets` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `player` VARCHAR(45) NOT NULL,
  `account` VARCHAR(45) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `question` VARCHAR(255) NOT NULL,
  `send_date` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `closed` VARCHAR(45) NOT NULL, 
  `last_update` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;

CREATE TABLE `darghos`.`wb_ticket_answers` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticked_id` VARCHAR(45) NOT NULL,
  `text` VARCHAR(255) NOT NULL,
  `by_name` VARCHAR(45) NOT NULL,
  `send_date` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
