-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Gegenereerd op: 30 aug 2015 om 16:25
-- Serverversie: 5.6.25
-- PHP-versie: 5.6.11

INSERT INTO `persons` (`id`, `student_number`, `first_name`, `nickname`, `initials`, `prefix`, `last_name`, `sex`, `date_of_birth`, `dead`, `date_of_death`, `email`, `mobile_phone`, `work_phone`, `iban`, `building_access`, `debtor_id`, `comments`, `draft`, `deleted_at`, `created_at`, `updated_at`) VALUES
(201, 4097718, 'Leon', '', 'L.A.', '', 'Loopik', 'm', '1992-01-22', 0, NULL, 'leon@loopik.nl', '06-48158008', '', 'NL02INGB000800040', 'always', NULL, 'Baas', NULL, NULL, '2015-08-25 14:08:18', '2015-08-25 14:08:18');

INSERT INTO `committees` (`id`, `short_name`, `long_name`, `description`, `fake`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Dies', 'Dies Natalis Commissie', 'De commissie die de Dies organiseerd', 0, NULL, '2015-08-25 17:18:26', '2015-08-25 17:18:26'),
(2, 'HoCo', 'Homepage Commissie', 'Maakt de Website', 0, NULL, '2015-08-25 17:18:54', '2015-08-25 17:18:54');

INSERT INTO `committee_members` (`person_id`, `committee_id`, `installation`, `discharge`, `function_number`, `function_name`, `created_at`, `updated_at`) VALUES
(201, 1, '2013-07-25', '2013-09-25', 1, 'President', '2015-08-25 17:19:44', '2015-08-25 17:19:44'),
(201, 2, '2014-08-05', NULL, NULL, '', '2015-08-25 17:20:09', '2015-08-25 17:20:09');

INSERT INTO `person_addresses` (`id`, `person_id`, `type`, `street`, `house_number`, `postal_code`, `town`, `country`, `phone_number`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 201, 'home', 'Jacoba van Beierenlaan', '163', '2613 JE', 'Delft', 'Nederland', '', NULL, '2015-08-25 14:10:11', '2015-08-25 14:10:11'),
(2, 201, 'parents', 'Leeuwendaallaan', '6', '2281 GN', 'Rijswijk', 'Nederland', '', NULL, '2015-08-25 14:10:11', '2015-08-25 14:10:11');

INSERT INTO `titles` (`id`, `title`, `form_of_address`, `rank`, `front`, `created_at`, `updated_at`) VALUES (NULL, 'B.Sc.', 'weledelgeboren', '5', '0', NOW(), NOW());

INSERT INTO `title_person_links` (`person_id`, `title_id`) VALUES ('201', '1', NOW(), NOW())