CREATE OR REPLACE
  ALGORITHM = UNDEFINED
  SQL SECURITY INVOKER
VIEW `persons_search`
  AS

SELECT `persons`.`id` AS `id`, CONCAT_WS(' ',`persons`.`first_name`,`persons`.`nickname`,`persons`.`initials`,`persons`.`prefix`,`persons`.`last_name`,`persons`.`email`,`persons`.`mobile_phone`,`person_addresses`.`address`,`person_addresses`.`postal_code`,`person_addresses`.`town`,`person_addresses`.`country`,`person_addresses`.`phone_number`) AS `searchblob`,(((COUNT(DISTINCT `board_members`.`board_id`) * 5) + COUNT(DISTINCT `committee_members`.`id`)) + (IF((`honorary_members`.`id` IS NOT NULL), YEAR(`honorary_members`.`installation`), YEAR(`normal_members`.`registration`)) * 2)) AS `relevance`
FROM (((((`persons`
LEFT JOIN `board_members` ON((`board_members`.`person_id` = `persons`.`id`)))
LEFT JOIN `committee_members` ON((`committee_members`.`person_id` = `persons`.`id`)))
LEFT JOIN `honorary_members` ON((`honorary_members`.`person_id` = `persons`.`id`)))
LEFT JOIN `normal_members` ON((`normal_members`.`person_id` = `persons`.`id`)))
LEFT JOIN `person_addresses` ON((`persons`.`id` = `person_addresses`.`person_id`) AND ISNULL(`person_addresses`.`deleted_at`)))
WHERE (ISNULL(`persons`.`deleted_at`))
GROUP BY `persons`.`id`,`person_addresses`.`id`