CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `persons_search`
AS
	SELECT persons.id, CONCAT_WS(' ',
	first_name, nickname, initials, prefix, last_name, email, mobile_phone, address, postal_code, town, country, phone_number
	) AS searchblob
	FROM persons
	LEFT JOIN person_addresses on persons.id = person_addresses.person_id
	WHERE persons.deleted_at is null AND person_addresses.deleted_at is NULL 