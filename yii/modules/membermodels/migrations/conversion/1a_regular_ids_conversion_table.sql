# Temporary unique ID conversion table
CREATE TABLE members.`_id` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`indexnummer` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `indexnummer` (`indexnummer`),
	INDEX `indexnummer_index` (`indexnummer`)
);

# Fill conversion table with old ID to new ID
INSERT INTO members._id (indexnummer)
SELECT indexnummer FROM leden_new.leden;
