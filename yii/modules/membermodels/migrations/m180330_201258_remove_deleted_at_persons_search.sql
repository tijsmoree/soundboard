SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;
SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';

CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `persons_search`
AS
	SELECT persons.id AS id,
	CONCAT_WS(
		' ',
		persons.first_name,
		persons.nickname,
		persons.initials,
		persons.prefix,
		persons.last_name,
		persons.email,
		persons.mobile_phone,
		person_addresses.address,
		person_addresses.postal_code,
		person_addresses.town,
		person_addresses.country,
		person_addresses.phone_number) AS searchblob,
	CONCAT_WS(
		' ',
		persons.first_name,
		persons.nickname,
		persons.initials,
		persons.prefix,
		persons.last_name,
		persons.email,
		persons.mobile_phone,
		person_addresses.address,
		person_addresses.postal_code,
		person_addresses.town,
		person_addresses.country,
		person_addresses.phone_number,
		persons.student_number,
		IF(
			persons.sex = 'm',
			'Man',
			IF(
				persons.sex = 'f',
				'Vrouw',
				'Anders'
			)
		),
		persons.date_of_birth,
		persons.comments,
		YEAR(normal_members.registration)
	) AS searchblob_advanced,
	(
		(
			COUNT(
				DISTINCT board_members.board_id
			) * 8 + IF(
			 	COUNT(
		 			DISTINCT committee_members.id
		 		) > 0,
				GREATEST(
					COUNT(
						DISTINCT committee_members.id
					) * 2,
					5
				),
				0
			)
		) + IFNULL(
		 	IFNULL(
		 		YEAR(normal_members.registration),
				(
					YEAR(honorary_members.installation) +
					IF(
						honorary_members.type = 'evv' OR
						honorary_members.type = 'erevoorzitter',
						30,
						0
					)
				)
			),
			(YEAR(persons.date_of_birth) + 16)
		)
	) AS relevance
	FROM persons 
	LEFT JOIN board_members
		ON board_members.person_id = persons.id
	LEFT JOIN committee_members
		ON committee_members.person_id = persons.id AND ISNULL(committee_members.discharge)
	LEFT JOIN honorary_members
		ON honorary_members.person_id = persons.id
	LEFT JOIN normal_members
		ON normal_members.person_id = persons.id
	LEFT JOIN person_addresses
		ON persons.id = person_addresses.person_id
	GROUP BY persons.id, person_addresses.id