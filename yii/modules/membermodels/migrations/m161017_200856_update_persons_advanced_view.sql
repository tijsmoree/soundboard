SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;
SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';

CREATE OR REPLACE
  ALGORITHM = UNDEFINED
  SQL SECURITY INVOKER
VIEW `persons_advanced`
  AS

SELECT
p.id, student_number,
CONCAT(IF(first_name!='',first_name,initials), IF(prefix!='', ' ',''), prefix, ' ', last_name) AS full_name,
CONCAT(IFNULL(CONCAT(GROUP_CONCAT(DISTINCT CONCAT(UCASE(
LEFT(t_front.title, 1)), SUBSTRING(t_front.title, 2))
ORDER BY t_front.rank SEPARATOR ' '),' '),''),
	initials, IF(prefix!='', ' ',''),
	prefix, ' ',
	last_name, IFNULL(CONCAT(' ', GROUP_CONCAT(DISTINCT CONCAT(UCASE(
LEFT(t_back.title, 1)), SUBSTRING(t_back.title, 2))
ORDER BY t_back.rank SEPARATOR ' ')),'')) AS formal_name,
IF(p.date_of_death IS NULL, 1, 0) is_alive,
IF(n.id IS NULL, 0, 1) AS is_normal_member,
IF(a.id IS NULL, 0, 1) AS is_alumnus,
IF(a_m.id IS NULL, 0, 1) AS is_associate_member,
IF(b_m.person_id IS NULL, 0, 1) AS is_or_was_board_member,
IF(c_m.person_id IS NULL, 0, 1) AS is_committee_member,
IF(c_m2.person_id IS NULL, 0, 1) AS is_or_was_committee_member,
IF(h.person_id IS NULL, 0, 1) AS is_honorary_member,
IF(sex='m','heer', IF(sex='f','mevrouw','')) AS gender,
CONCAT(IFNULL(SUBSTRING_INDEX(GROUP_CONCAT(t.form_of_address ORDER BY t.rank),',',1), 'De weledelgeboren'), IFNULL(CONCAT(' ', IF(sex='m','heer', IF(sex='f','mevrouw',''))),'')) AS form_of_address,
CONCAT('Geachte', IFNULL(CONCAT(' ', IF(sex='m','heer', IF(sex='f','mevrouw',''))),'')) AS salutation,
first_name, nickname, initials, prefix, last_name, sex,
date_of_birth, date_of_death, email, mobile_phone, iban, building_access, debtor_code, comments, draft, p.created_at, p.updated_at
FROM persons p
LEFT JOIN title_person_links tpl ON tpl.person_id = p.id
LEFT JOIN titles t ON t.id = tpl.title_id
LEFT JOIN titles t_front ON t_front.id = tpl.title_id AND t_front.front = 1
LEFT JOIN titles t_back ON t_back.id = tpl.title_id AND t_back.front = 0
LEFT JOIN normal_members n ON n.person_id = p.id AND deregistration IS NULL
LEFT JOIN alumni a ON a.person_id = p.id AND a.deregistration IS NULL
LEFT JOIN associate_members a_m ON a_m.person_id = p.id AND a_m.deregistration IS NULL
LEFT JOIN board_members b_m ON b_m.person_id = p.id
LEFT JOIN committee_members c_m ON c_m.person_id = p.id AND c_m.discharge IS NULL
LEFT JOIN committee_members c_m2 ON c_m2.person_id = p.id
LEFT JOIN honorary_members h ON h.person_id = p.id AND h.discharge IS NULL
WHERE p.deleted_at IS NULL
GROUP BY p.id