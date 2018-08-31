SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;
SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';

CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `persons_advanced`
AS
	SELECT p.id AS id,
	p.student_number AS student_number,
	CONCAT_WS(
		' ',
		IF(
			p.first_name <> '',
			p.first_name,
			p.initials
		),
		NULLIF(
			p.prefix,
			''
		),
		p.last_name
	) AS full_name,
	CONCAT_WS(
		' ',
		CONCAT(
			UPPER(
				LEFT(
					GROUP_CONCAT(
						DISTINCT t_front.title
						ORDER BY t_front.rank ASC
						SEPARATOR ' '
					),
					1
				)
			),
			MID(
				GROUP_CONCAT(
					DISTINCT t_front.title
					ORDER BY t_front.rank ASC
					SEPARATOR ' '
				),
				2
			)
		),
		p.initials,
		NULLIF(
			p.prefix,
			''
		),
		p.last_name,
		GROUP_CONCAT(
			DISTINCT t_back.title
			ORDER BY t_back.rank ASC
			SEPARATOR ' '
		)
	) AS formal_name,
	ISNULL(p.date_of_death) AS is_alive,
	ISNULL(n.id) AS is_normal_member,
	ISNULL(a.id) AS is_alumnus,
	ISNULL(a_m.id) AS is_associate_member,
	ISNULL(b_m.person_id) AS is_or_was_board_member,
	ISNULL(c_m.person_id) AS is_committee_member,
	ISNULL(c_m2.person_id) AS is_or_was_committee_member,
	ISNULL(h.person_id) AS is_honorary_member,
	CASE
		WHEN p.sex = 'm' THEN 'heer'
		WHEN p.sex = 'f' THEN 'mevrouw'
		ELSE ''
	END AS gender,
	CONCAT(
		IFNULL(
			SUBSTRING_INDEX(
				GROUP_CONCAT(
					t.form_of_address
					ORDER BY t.rank ASC
					SEPARATOR ','
				),
				',',
				1
			),
			'De weledelgeboren'
		),
		CASE
			WHEN p.sex = 'm' THEN ' heer'
			WHEN p.sex = 'f' THEN ' mevrouw'
			ELSE ' '
		END
	) AS form_of_address,
	CONCAT(
		'Geachte',
		CASE
			WHEN p.sex = 'm' THEN ' heer'
			WHEN p.sex = 'f' THEN ' mevrouw'
			ELSE ' '
		END
	) AS salutation,
	p.first_name AS first_name,
	p.nickname AS nickname,
	p.initials AS initials,
	p.prefix AS prefix,
	p.last_name AS last_name,
	p.sex AS sex,
	p.date_of_birth AS date_of_birth,
	p.date_of_death AS date_of_death,
	p.email AS email,
	p.mobile_phone AS mobile_phone,
	p.iban AS iban,
	p.building_access AS building_access,
	p.debtor_code AS debtor_code,
	p.comments AS comments,
	p.draft AS draft,
	p.created_at AS created_at,
	p.updated_at AS updated_at
	FROM persons p
	LEFT JOIN title_person_links tpl
		ON tpl.person_id = p.id
	LEFT JOIN titles t
		ON t.id = tpl.title_id
	LEFT JOIN titles t_front
		ON t_front.id = tpl.title_id AND t_front.front = 1
	LEFT JOIN titles t_back
		ON t_back.id = tpl.title_id AND t_back.front = 0
	LEFT JOIN normal_members n
		ON n.person_id = p.id AND ISNULL(n.deregistration)
	LEFT JOIN alumni a
		ON a.person_id = p.id AND ISNULL(a.deregistration)
	LEFT JOIN associate_members a_m
		ON a_m.person_id = p.id AND ISNULL(a_m.deregistration)
	LEFT JOIN board_members b_m
		ON b_m.person_id = p.id
	LEFT JOIN committee_members c_m
		ON c_m.person_id = p.id AND ISNULL(c_m.discharge)
	LEFT JOIN committee_members c_m2
		ON c_m2.person_id = p.id
	LEFT JOIN honorary_members h
		ON h.person_id = p.id AND ISNULL(h.discharge)
	GROUP BY p.id