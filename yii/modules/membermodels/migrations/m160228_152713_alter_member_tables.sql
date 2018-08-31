# Some adaptions to the current new member database model
CREATE TABLE `board_pictures` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`board_id` INT(11) NOT NULL,
	`priority` INT(3) NOT NULL DEFAULT '0',
	`description` TEXT NOT NULL,
	`file_name` VARCHAR(100) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `fp_board_pictures_board_id` (`board_id`),
	CONSTRAINT `fp_board_pictures_board_id` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`)
);

ALTER TABLE `persons`
	DROP COLUMN `dead`,
	DROP COLUMN `work_phone`;

ALTER TABLE `person_addresses`
	ALTER `street` DROP DEFAULT;
ALTER TABLE `person_addresses`
	CHANGE COLUMN `street` `address` VARCHAR(100) NOT NULL AFTER `type`,
	DROP COLUMN `house_number`;

ALTER TABLE `boards`
	ALTER `number` DROP DEFAULT;
ALTER TABLE `boards`
	CHANGE COLUMN `number` `number` SMALLINT(3) NULL AFTER `id`;

ALTER TABLE `committee_members`
	ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`id`);

ALTER TABLE `associations`
	ALTER `type` DROP DEFAULT,
	ALTER `mail_street` DROP DEFAULT,
	ALTER `visit_street` DROP DEFAULT;
ALTER TABLE `associations`
	CHANGE COLUMN `type` `type` VARCHAR(100) NOT NULL AFTER `name`,
	CHANGE COLUMN `mail_street` `mail_address` VARCHAR(100) NOT NULL AFTER `study`,
	ADD COLUMN `mail_internal` TINYINT(1) NOT NULL DEFAULT '0' AFTER `mail_country`,
	CHANGE COLUMN `visit_street` `visit_address` VARCHAR(100) NOT NULL AFTER `mail_internal`,
	CHANGE COLUMN `commentaar` `comments` TEXT NOT NULL AFTER `magazine`,
	DROP COLUMN `mail_house_number`,
	DROP COLUMN `visit_house_number`;