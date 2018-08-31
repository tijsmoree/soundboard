CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `persons_search`
AS
    SELECT persons.id, CONCAT_WS(' ', first_name, nickname, initials, prefix, last_name, email, mobile_phone, address, postal_code, town, country, phone_number) AS searchblob, 
    COUNT(DISTINCT board_members.board_id)*5+COUNT(DISTINCT committee_members.id)+IF(honorary_members.id IS NOT NULL, YEAR(honorary_members.installation), YEAR(registration))*2 AS relevance 
        FROM persons
        LEFT JOIN board_members ON board_members.person_id = persons.id
        LEFT JOIN committee_members ON committee_members.person_id = persons.id
        LEFT JOIN honorary_members ON honorary_members.person_id = persons.id
        LEFT JOIN normal_members ON normal_members.person_id = persons.id
        LEFT JOIN person_addresses ON persons.id = person_addresses.person_id
        WHERE persons.deleted_at IS NULL AND person_addresses.deleted_at IS NULL
        GROUP BY persons.id, person_addresses.id 