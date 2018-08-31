INSERT INTO members.persons (id, student_number, first_name, nickname, initials, prefix, last_name, sex, date_of_birth, date_of_death, email, mobile_phone, comments, created_at, updated_at)
SELECT _id.id AS id, IF(LENGTH(l.indexnummer)=7,l.indexnummer,NULL) AS student_number,
roepnaam AS first_name, '' as nickname, voorletters AS initials, voorvoegsels AS prefix,
naam AS last_name, IF(geslacht LIKE "Man", "m", "f") AS sex,
IF(geboortedatum="0000-00-00",NULL,geboortedatum) AS date_of_birth,
IF(ter_ziele=1,IF(DATE(overlijdensdatum) IS NULL,"0000-00-00",DATE(overlijdensdatum)),NULL) AS date_of_death,
IF(email IS NULL,'',email) AS email, IF(mobiel IS NULL,'',mobiel) AS mobile_phone, commentaar AS comments,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l INNER JOIN members._id ON l.indexnummer = _id.indexnummer;

INSERT INTO members.person_addresses (person_id, `type`, address, postal_code, town, country, phone_number, deleted_at, created_at, updated_at)
SELECT _id.id AS person_id, 'home' AS `type`, adres1 AS address,
postcode1 AS postal_code, plaats1 AS town, IF(land1 IS NULL,'',land1) AS country,
IF(telefoon1 IS NULL,'',telefoon1) AS phone_number,
IF(incorrect_adres1=1,NOW(),NULL) AS deleted_at, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l INNER JOIN members._id ON l.indexnummer = _id.indexnummer
WHERE NOT(adres1 = '' AND postcode1 = '' AND plaats1 = '') AND NOT(adres1 = '?');

INSERT INTO members.person_addresses (person_id, `type`, address, postal_code, town, country, phone_number, deleted_at, created_at, updated_at)
SELECT _id.id AS person_id, 'parents' AS `type`, adres2 AS address,
postcode2 AS postal_code, plaats2 AS town, IF(land2 IS NULL,'',land2) AS country,
IF(telefoon2 IS NULL,'',telefoon2) AS phone_number,
IF(incorrect_adres2=1,NOW(),NULL) AS deleted_at, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l INNER JOIN members._id ON l.indexnummer = _id.indexnummer
WHERE NOT(adres2 = '' AND postcode2 = '' AND plaats2 = '') AND NOT(adres2 = '?');

INSERT INTO members.titles (title, form_of_address, rank, front, created_at, updated_at)
SELECT titel AS title, aanspreek as form_of_address, (id-13) AS rank,
IF(titel = 'm.sc.' || titel = 'msc.' || titel = 'phd', 0, 1) AS front,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.static_aanspreek
WHERE titel != 'feut.' AND titel != '' AND titel != 'msc.'
ORDER BY rank ASC;

INSERT INTO members.titles (title, form_of_address, rank, front, created_at, updated_at) VALUES
('b.sc.', 'De weledelgeboren', 11, 0, NOW(), NOW()),
('em.', 'De hooggeleerde', 2, 1, NOW(), NOW()),
('b.eng.', 'De weledelgeboren', 11, 0, NOW(), NOW()),
('eng.', 'De weledelgeboren', 11, 0, NOW(), NOW()),
('b.ict.', 'De weledelgeboren', 11, 0, NOW(), NOW()),
('b.ing.', 'De weledelgeboren', 11, 0, NOW(), NOW()),
('m.ed.', 'De weledelgestrenge', 8, 0, NOW(), NOW()),
('m.eng.', 'De weledelgestrenge', 8, 0, NOW(), NOW()),
('ing.', 'De weledelgeboren', 11, 1, NOW(), NOW());

INSERT INTO members.title_person_links (person_id, title_id, created_at, updated_at)
SELECT _id.id AS person_id, t.id AS title_id,
#REPLACE(REPLACE(REPLACE(l.titel, 'Msc', 'm.sc'), 'Ph.D.', 'PhD'), 'B.Eng', 'b.eng.') AS title1, reverse(substr(reverse(t.title),2)) AS title2,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l INNER JOIN members._id ON l.indexnummer = _id.indexnummer
INNER JOIN members.titles t
	ON REPLACE(REPLACE(REPLACE(l.titel, 'Msc', 'm.sc'), 'Ph.D.', 'PhD'), 'B.Eng', 'b.eng.')
	LIKE CONCAT("%",reverse(substr(reverse(t.title),2)),"%")
WHERE NOT(l.titel = 'B.Ing.' AND t.title = 'ing.')
	AND NOT(l.titel LIKE "%drs%" AND t.title = 'dr.')
	AND NOT(l.titel LIKE "%b.eng%" AND t.title = 'eng.')
	AND NOT(l.titel LIKE "%m.eng%" AND t.title = 'eng.')
ORDER BY _id.id;


CREATE TABLE members.`_colors` (
	`name` VARCHAR(30) NOT NULL,
	`hex` CHAR(6) NULL DEFAULT NULL,
	PRIMARY KEY (`name`)
);
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('aquamarine', '7fffd4');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('black', '000000');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('blue', '0000ff');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('darkturquoise', '00ced1');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('deepskyblue', '00bfff');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('gold', 'ffd700');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('green', '008000');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('grey', '808080');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('lime', '00ff00');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('orange', 'ffa500');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('purple', '800080');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('red', 'ff0000');
INSERT INTO members.`_colors` (`name`, `hex`) VALUES ('yellow', 'ffff00');

INSERT INTO members.boards (number, adjective, motto, color, lustrum, installation, installation_precision,
discharge, discharge_precision, description, created_at, updated_at)
SELECT nr AS number, kenmerk AS adjective, zinspreuk AS motto,
IF(c.hex IS NULL,LOWER(REPLACE(kleur, "#", "")),c.hex) AS color,
IF(lustrum = 0, 0, 1) AS lustrum,
DATE(DATE_ADD(FROM_UNIXTIME(10000), interval begin second)) AS installation,
IF(precisie_begin='dag','day',IF(precisie_begin='maand','month','year')) AS installation_precision,
DATE(DATE_ADD(FROM_UNIXTIME(10000), interval eind second)) AS discharge,
IF(precisie_eind='dag' || precisie_eind is null,'day',IF(precisie_eind='maand','month','year')) AS discharge_precision,
commentaar AS description, NOW() as created_at, NOW() AS updated_at
FROM leden_new.besturen b
LEFT JOIN members._colors c ON b.kleur = c.name
ORDER BY begin ASC;

DROP TABLE members._colors;

INSERT INTO members.board_members (person_id, board_id, function_name, function_number, created_at, updated_at)
SELECT _id.id AS person_id, b.id AS board_id, functie AS function_name,
functienummer AS function_number, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_bestuurslid bl
LEFT JOIN members._id ON _id.indexnummer = bl.indexnummer
LEFT JOIN members.boards b ON b.number = bl.bestuur
ORDER BY board_id, function_number;

INSERT INTO members.board_pictures (board_id, priority, description, file_name, created_at, updated_at)
SELECT b.id AS board_id, Volgorde AS priority,
Beschrijving AS description, Bestandsnaam AS file_name, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.fotos f
LEFT JOIN leden_new.besturen b_old ON b_old.id = f.Bestuur
LEFT JOIN members.boards b ON b.number = b_old.nr
ORDER BY board_id, priority;

UPDATE members.boards SET number = NULL WHERE number < 123;

INSERT INTO members.honorary_members (person_id, type, installation, discharge, description, created_at, updated_at)
SELECT _id.id AS person_id, IF(`type`=0,'evb',IF(`type`=1,'evv','erevoorzitter')) AS `type`,
DATE(IF(installatie='0000','0000-00-00',CONCAT(installatie,"-01-01"))) AS installation, p.date_of_death AS discharge,
commentaar AS description, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_erelid e
INNER JOIN members._id ON _id.indexnummer = e.indexnummer
INNER JOIN members.persons p ON p.id = _id.id
ORDER BY FIELD(`type`, 2, 1, 0), installation ASC;

INSERT INTO members.associate_members (person_id, registration, deregistration, expiration, description, created_at, updated_at)
SELECT _id.id AS person_id, DATE('0000-00-00') AS registration, NULL AS deregistration,
DATE(verloopdatum) AS expiration, '' AS description, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_buitengewoon_lid bl
LEFT JOIN members._id ON _id.indexnummer = bl.indexnummer;

INSERT INTO members.alumni (person_id, registration, deregistration, created_at, updated_at)
SELECT _id.id AS person_id,
IF(l.jaar_uitschrijving != 0, DATE(CONCAT(l.jaar_uitschrijving,'-01-01')), DATE('0000-00-00')) AS registration,
IF(l.ter_ziele = 1, DATE(l.overlijdensdatum), NULL) AS deregistration, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_reunist r
LEFT JOIN leden_new.leden l ON l.indexnummer = r.indexnummer
LEFT JOIN members._id ON _id.indexnummer = r.indexnummer
GROUP BY _id.id
ORDER BY l.indexnummer;

INSERT INTO members.committees (id, short_name, long_name, description, fake, deleted_at, created_at, updated_at)
SELECT id AS id, naam AS short_name, naam_voluit AS long_name, beschrijving AS description, IF(echt=1,0,1) AS fake,
NULL AS deleted_at, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.commissies
ORDER BY id;

INSERT INTO members.committee_members (person_id, committee_id, installation, discharge, function_number, function_name, created_at, updated_at)
SELECT _id.id AS person_id, c.commissie AS committee_id,
DATE(IF(jaar IS NULL OR jaar=0, '0000-00-00', CONCAT(jaar,'-01-01'))) AS installation,
IF(geinstalleerd=0,DATE('0000-00-00'),NULL) AS discharge, IF(functienummer=0 OR functienummer > 20,NULL,functienummer) AS function_number,
IF(functie IS NULL,'',functie) AS function_name, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_commissielid c
INNER JOIN members._id ON _id.indexnummer = c.indexnummer
INNER JOIN members.committees co ON co.id = c.commissie
ORDER BY committee_id, installation, function_number;

# import existing member types
INSERT INTO members.normal_member_types (name, `type`, created_at, updated_at)
SELECT instroom AS name, IF(instroom='Bachelor','bsc',IF(instroom='Master','msc','other')) AS `type`,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.static_instroom i
WHERE instroom != 'Niet van toepassing';

INSERT INTO members.normal_member_types (name, `type`, created_at, updated_at) VALUES
('Sustainable Energy Technology', 'msc', NOW(), NOW()),
('Onbekend', 'other', NOW(), NOW());

# all current members
INSERT INTO members.normal_members (person_id, type_id, registration, deregistration, created_at, updated_at)
SELECT _id.id AS person_id,
IF(g.lid_dispuut_set,set_t.id,IFNULL(t.id,unknown_t.id)) AS type_id, DATE(CONCAT(l.jaar_inschrijving,'-01-01')) AS registration,
IF(jaar_uitschrijving=0,NULL,DATE(CONCAT(l.jaar_uitschrijving,'-01-01'))) AS deregistration,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_gewoon_lid g
INNER JOIN leden_new.leden l ON l.indexnummer = g.indexnummer
INNER JOIN members._id ON _id.indexnummer = g.indexnummer
LEFT JOIN members.normal_member_types t ON t.name = l.instroom
LEFT JOIN members.normal_member_types set_t ON set_t.name = 'Sustainable Energy Technology'
LEFT JOIN members.normal_member_types unknown_t ON unknown_t.name = 'Onbekend'
ORDER BY person_id;

# all old members
INSERT INTO members.normal_members (person_id, type_id, registration, deregistration, created_at, updated_at)
SELECT _id.id AS person_id,
t.id AS type_id, DATE(CONCAT(l.jaar_inschrijving,'-01-01')) AS registration,
IF(jaar_uitschrijving=0,'0000-00-00',DATE(CONCAT(l.jaar_uitschrijving,'-01-01'))) AS deregistration,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l
INNER JOIN members._id ON _id.indexnummer = l.indexnummer
LEFT JOIN leden_new.group_gewoon_lid g ON g.indexnummer = l.indexnummer
LEFT JOIN members.normal_member_types t ON t.name = l.instroom
WHERE g.id IS NULL AND t.id IS NOT NULL
ORDER BY person_id;


INSERT INTO members.options (name, description, created_at, updated_at) VALUES
('Maxwell', 'Ter versturen van de periodiek', NOW(), NOW()),
('EESTEC Mail', 'Ter versturen van de mail over aankomende EESTEC-activiteiten', NOW(), NOW()),
('Bedrijvenmail', 'Ter versturen van de mail over aankomende Bedrijfsactiviteiten', NOW(), NOW()),
('Online opt-out', 'Of deze persoon niet online te vinden wil worden', NOW(), NOW()),
('Reunistendag', 'Of deze persoon uitgenodigd wilt worden voor de Reunistendag', NOW(), NOW()),
('Jaarboek', 'Ter versturen van het jaarboek', NOW(), NOW()),
('Symposium', 'Of deze persoon informatie over het symposium wilt ontvangen', NOW(), NOW()),
('Gala', 'Of deze persoon een uitnodiging wilt ontvangen over het Gala', NOW(), NOW()),
('Kaartje', 'Ter versturen van een constitutie- of kerstkaartje', NOW(), NOW()),
('ETV-nieuwsbrief', 'Ter versturen van de algemene ETV-nieuwsbrief', NOW(), NOW()),
('Feestjes', 'Ter versturen van een mail/update van aankomende feestjes', NOW(), NOW());

INSERT INTO members.option_person_links (person_id, option_id, created_at, updated_at)
SELECT _id.id AS person_id, o.id AS option_id, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_gewoon_lid g
INNER JOIN members._id ON _id.indexnummer = g.indexnummer
JOIN members.options o ON
	(maxwell = 1 AND o.name='Maxwell') OR
	(eestec_mail = 1 AND o.name='EESTEC Mail') OR
	(bedrijven_mail = 1 AND o.name='Bedrijvenmail') OR
	(niet_online = 1 AND o.name='Online opt-out')
ORDER BY _id.id, o.id;

INSERT INTO members.option_person_links (person_id, option_id, created_at, updated_at)
SELECT _id.id AS person_id, o.id AS option_id, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_buitengewoon_lid g
INNER JOIN members._id ON _id.indexnummer = g.indexnummer
JOIN members.options o ON
	(bedrijven_mail = 1 AND o.name='Bedrijvenmail')
ORDER BY _id.id, o.id;

INSERT IGNORE INTO members.option_person_links (person_id, option_id, created_at, updated_at)
SELECT _id.id AS person_id, o.id AS option_id, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.group_reunist g
INNER JOIN members._id ON _id.indexnummer = g.indexnummer
JOIN members.options o ON
	(reunistendag = 1 AND o.name='Reunistendag') OR
	(jaarboek = 1 AND o.name='Jaarboek') OR
	(maxwell = 1 AND o.name='Maxwell') OR
	(symposium = 1 AND o.name='Symposium') OR
	(gala = 1 AND o.name='Gala')
ORDER BY _id.id, o.id;

INSERT INTO members.associations (name, type, study, mail_address, mail_postal_code, mail_town, mail_country, mail_internal, visit_address,
visit_postal_code, visit_town, visit_country, phone_number1, phone_number2, fax, email, form_of_address, salutation, website,
magazine, comments, deleted_at, created_at, updated_at)
SELECT naam AS name, soort AS `type`, studie AS study, adres1 AS address, postcode1 AS mail_postal_code,
plaats1 AS mail_town, land1 AS mail_country, intern AS mail_internal, adres2 AS visit_address, postcode2 as visit_postal_code,
plaats2 AS visit_town, land2 AS visit_country, telefoon1 AS phone_number1, telefoon2 AS phone_number2, fax AS fax,
email AS email, aanhef AS form_of_address, aanspreek AS salutation, website AS website, '' AS magazine,
REPLACE(commentaar, '<br />', '\n') AS comments, NULL AS deleted_at, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.verenigingen;

INSERT INTO members.option_association_links (association_id, option_id, created_at, updated_at)
SELECT a.id, o.id, NOW() AS created_at, NOW() AS updated_at
FROM leden_new.verenigingen g
INNER JOIN members.associations a ON g.naam = a.name
JOIN members.options o ON
	(jaarboek = 1 AND o.name='Jaarboek') OR
	(maxwell = 1 AND o.name='Maxwell') OR
	(kaartje = 1 AND o.name='Kaartje')
ORDER BY a.id, o.id;

INSERT INTO members.person_pictures (person_id, main, file_name, created_at, updated_at)
SELECT _id.id AS person_id, 1 AS main,
CONCAT('LedenFotos/',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(naam,roepnaam,jaar_inschrijving),'ƒ','f'),'ÿ','y'),'þ','b'),'ý','y'),'ý','y'),'û','u'),'ú','u'),'ù','u'),'ø','o'),'ö','o'),'õ','o'),'ô','o'),'ó','o'),'ò','o'),'ñ','n'),'ð','o'),'ï','i'),'î','i'),'í','i'),'ì','i'),'ë','e'),'ê','e'),'é','e'),'è','e'),'ç','c'),'æ','a'),'å','a'),'ä','a'),'ã','a'),'â','a'),'á','a'),'à','a'),'ß','Ss'),'Þ','B'),'Ý','Y'),'Ü','U'),'Û','U'),'Ú','U'),'Ù','U'),'Ø','O'),'Ö','O'),'Õ','O'),'Ô','O'),'Ó','O'),'Ò','O'),'Ñ','N'),'Ï','I'),'Î','I'),'Í','I'),'Ì','I'),'Ë','E'),'Ê','E'),'É','E'),'È','E'),'Ç','C'),'Æ','A'),'Å','A'),'Ä','A'),'Ã','A'),'Â','A'),'Á','A'),'À','A'),'ž','z'),'Ž','Z'),'Ð','Dj'),'š','s'),'Š','S'),' ','_'),'.',''),'.jpg') AS file_name,
NOW() AS created_at, NOW() AS updated_at
FROM leden_new.leden l
INNER JOIN members._id ON _id.indexnummer = l.indexnummer
WHERE has_picture = 1;

DROP TABLE members._id;
