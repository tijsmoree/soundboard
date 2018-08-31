# Temporary unique ID conversion table
CREATE TABLE members.`_id` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`indexnummer` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `indexnummer` (`indexnummer`),
	INDEX `indexnummer_index` (`indexnummer`)
);

# Fill id-conversion table with current new id's which can be matched to old persons (with student number)
INSERT INTO members._id (id, indexnummer)
SELECT p.id, l.indexnummer FROM leden_new.leden l
INNER JOIN members.persons p ON p.student_number = l.indexnummer;

# Fill conversion table with old ID to new ID
INSERT INTO members._id (indexnummer)
SELECT l.indexnummer FROM leden_new.leden l
LEFT JOIN members._id id ON id.indexnummer = l.indexnummer
WHERE id.id IS NULL;
