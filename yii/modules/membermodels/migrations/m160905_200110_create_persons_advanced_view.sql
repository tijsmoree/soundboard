CREATE OR REPLACE
  ALGORITHM = UNDEFINED
  SQL SECURITY INVOKER
VIEW `persons_advanced`
  AS

SELECT
p.id, student_number, first_name, nickname, initials, prefix, last_name, sex, IF(sex='m','heer', IF(sex='f','mevrouw','')) AS gender, CONCAT(IFNULL(CONCAT(GROUP_CONCAT(CONCAT(UCASE(
LEFT(t_front.title, 1)), SUBSTRING(t_front.title, 2))
ORDER BY t_front.rank SEPARATOR ' '),' '),''),
	initials, IF(prefix!='', ' ',''),
	prefix, ' ',
	last_name, IFNULL(CONCAT(' ', GROUP_CONCAT(CONCAT(UCASE(
LEFT(t_back.title, 1)), SUBSTRING(t_back.title, 2))
ORDER BY t_back.rank SEPARATOR ' ')),'')
) AS name_with_titles, CONCAT(IFNULL(SUBSTRING_INDEX(GROUP_CONCAT(t.form_of_address
ORDER BY t.rank),',',1), 'De weledelgeboren'), IFNULL(CONCAT(' ',IF(sex='m','heer', IF(sex='f','mevrouw',''))),'')) AS form_of_address,
CONCAT('Geachte', IFNULL(CONCAT(' ',IF(sex='m','heer', IF(sex='f','mevrouw',''))),'')) AS salutation,
date_of_birth, date_of_death, email, mobile_phone, iban, building_access, debtor_code, comments, draft, p.created_at, p.updated_at
FROM persons p
LEFT JOIN title_person_links tpl ON tpl.person_id = p.id
LEFT JOIN titles t ON t.id = tpl.title_id
LEFT JOIN titles t_front ON t_front.id = tpl.title_id AND t_front.front = 1
LEFT JOIN titles t_back ON t_back.id = tpl.title_id AND t_back.front = 0
WHERE p.deleted_at IS NULL
GROUP BY p.id;
