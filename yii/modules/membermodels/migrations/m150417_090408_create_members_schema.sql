-- -----------------------------------------------------
-- Table `boards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `boards` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `number` SMALLINT(3) NOT NULL,
  `adjective` VARCHAR(100) NOT NULL,
  `motto` VARCHAR(200) NOT NULL,
  `color` CHAR(6) NOT NULL DEFAULT '000000',
  `lustrum` TINYINT(1) NOT NULL DEFAULT 0,
  `installation` DATE NULL DEFAULT NULL,
  `installation_precision` ENUM('day','month','year') NOT NULL,
  `discharge` DATE NULL DEFAULT NULL,
  `discharge_precision` ENUM('day','month','year') NOT NULL,
  `description` TEXT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  UNIQUE INDEX `nr` (`number` ASC),
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `committees`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `committees` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `short_name` VARCHAR(45) NOT NULL,
  `long_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `fake` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `persons`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `persons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_number` INT(7) UNSIGNED NULL DEFAULT NULL,
  `first_name` VARCHAR(100) NOT NULL DEFAULT '',
  `nickname` VARCHAR(100) NOT NULL DEFAULT '',
  `initials` VARCHAR(100) NOT NULL DEFAULT '',
  `prefix` VARCHAR(100) NOT NULL DEFAULT '',
  `last_name` VARCHAR(100) NOT NULL DEFAULT '',
  `sex` ENUM('m','f', 'other') NOT NULL,
  `date_of_birth` DATE NULL DEFAULT NULL,
  `dead` TINYINT(1) NOT NULL DEFAULT 0,
  `date_of_death` DATE NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL DEFAULT '',
  `mobile_phone` VARCHAR(45) NOT NULL DEFAULT '',
  `work_phone` VARCHAR(45) NOT NULL DEFAULT '',
  `iban` VARCHAR(45) NOT NULL DEFAULT '',
  `building_access` ENUM('normal','weekend','always') NOT NULL DEFAULT 'normal',
  `debtor_id` INT NULL,
  `comments` TEXT,
  `draft` DATETIME NULL DEFAULT NULL,
  `deleted_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `student_number_UNIQUE` (`student_number` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `associate_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `associate_members` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `registration` DATE NOT NULL,
  `deregistration` DATE NULL DEFAULT NULL,
  `expiration` DATE NOT NULL,
  `description` TEXT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_group_buitengewoon_lid_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_group_buitengewoon_lid_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `honorary_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `honorary_members` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `type` ENUM('evv','evb','erevoorzitter', 'lvv') NOT NULL,
  `installation` DATE NOT NULL,
  `discharge` DATE NULL DEFAULT NULL,
  `description` TEXT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_honorary_members_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_honorary_members_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `normal_member_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `normal_member_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `type` ENUM('bsc','msc','other') NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `normal_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `normal_members` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `type_id` INT NULL,
  `registration` DATE NOT NULL,
  `deregistration` DATE NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_normal_member_persons1_idx` (`person_id` ASC),
  INDEX `fk_normal_members_educations1_idx` (`type_id` ASC),
  CONSTRAINT `fk_normal_member_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_normal_members_educations1`
  FOREIGN KEY (`type_id`)
  REFERENCES `normal_member_types` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `alumni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alumni` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `registration` DATE NULL DEFAULT NULL,
  `deregistration` DATE NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_alumni_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_alumni_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `board_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `board_members` (
  `person_id` INT NOT NULL,
  `board_id` INT NOT NULL,
  `function_name` VARCHAR(100) NOT NULL,
  `function_number` TINYINT(1) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`person_id`, `board_id`),
  INDEX `fk_persons_has_boards_boards1_idx` (`board_id` ASC),
  INDEX `fk_persons_has_boards_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_persons_has_boards_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_persons_has_boards_boards1`
  FOREIGN KEY (`board_id`)
  REFERENCES `boards` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `committee_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `committee_members` (
  `person_id` INT NOT NULL,
  `committee_id` INT NOT NULL,
  `installation` DATE NULL DEFAULT NULL,
  `discharge` DATE NULL DEFAULT NULL,
  `function_number` TINYINT(1) NULL DEFAULT NULL,
  `function_name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`person_id`, `committee_id`),
  INDEX `fk_persons_has_committees_committees1_idx` (`committee_id` ASC),
  INDEX `fk_persons_has_committees_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_persons_has_committees_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_persons_has_committees_committees1`
  FOREIGN KEY (`committee_id`)
  REFERENCES `committees` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person_addresses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `person_addresses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `type` ENUM('home','parents') NOT NULL,
  `street` VARCHAR(100) NOT NULL,
  `house_number` VARCHAR(100) NOT NULL,
  `postal_code` VARCHAR(100) NOT NULL,
  `town` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(100) NOT NULL,
  `deleted_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_adresses_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `options`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `options` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` TEXT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `option_person_links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `option_person_links` (
  `person_id` INT NOT NULL,
  `option_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`person_id`, `option_id`),
  INDEX `fk_persons_has_ticks_ticks1_idx` (`option_id` ASC),
  INDEX `fk_persons_has_ticks_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_persons_has_ticks_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_persons_has_ticks_ticks1`
  FOREIGN KEY (`option_id`)
  REFERENCES `options` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rooms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(45) NOT NULL,
  `deleted_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `room_code_UNIQUE` (`code` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `room_access`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `room_access` (
  `person_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `deleted_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`person_id`, `room_id`),
  INDEX `fk_persons_has_rooms_rooms1_idx` (`room_id` ASC),
  INDEX `fk_persons_has_rooms_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_persons_has_rooms_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_persons_has_rooms_rooms1`
  FOREIGN KEY (`room_id`)
  REFERENCES `rooms` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person_pictures`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `person_pictures` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `main` TINYINT(1) NOT NULL DEFAULT 0,
  `file_name` VARCHAR(100) NOT NULL,
  `taken_at` DATE NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_person_pictures_persons1_idx` (`person_id` ASC),
  CONSTRAINT `fk_person_pictures_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `associations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `associations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('study','recreation','other') NOT NULL,
  `study` VARCHAR(100) NOT NULL,
  `mail_street` VARCHAR(100) NOT NULL,
  `mail_house_number` VARCHAR(45) NOT NULL,
  `mail_postal_code` VARCHAR(45) NOT NULL,
  `mail_town` VARCHAR(100) NOT NULL,
  `mail_country` VARCHAR(100) NOT NULL,
  `visit_street` VARCHAR(100) NOT NULL,
  `visit_house_number` VARCHAR(45) NOT NULL,
  `visit_postal_code` VARCHAR(45) NOT NULL,
  `visit_town` VARCHAR(100) NOT NULL,
  `visit_country` VARCHAR(100) NOT NULL,
  `phone_number1` VARCHAR(45) NOT NULL,
  `phone_number2` VARCHAR(45) NOT NULL,
  `fax` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `form_of_address` VARCHAR(100) NOT NULL,
  `salutation` VARCHAR(100) NOT NULL,
  `website` VARCHAR(100) NOT NULL,
  `magazine` VARCHAR(100) NOT NULL,
  `commentaar` TEXT,
  `deleted_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `option_association_links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `option_association_links` (
  `association_id` INT NOT NULL,
  `option_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`association_id`, `option_id`),
  INDEX `fk_options_has_associations_options1_idx` (`option_id` ASC),
  CONSTRAINT `fk_options_has_associations_options1`
  FOREIGN KEY (`option_id`)
  REFERENCES `options` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_option_association_links_associations1`
  FOREIGN KEY (`association_id`)
  REFERENCES `associations` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `titles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `titles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `form_of_address` VARCHAR(45) NOT NULL,
  `rank` INT NOT NULL,
  `front` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC),
  INDEX `priority` (`front` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `faculty_departments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `faculty_departments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(45) NOT NULL,
  `comments` TEXT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `faculty_employees`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `faculty_employees` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NOT NULL,
  `room` VARCHAR(45) NULL,
  `tu_phone` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `fk_persons_has_faculty_departments_persons1_idx` (`person_id` ASC),
  UNIQUE INDEX `person_id_UNIQUE` (`person_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_persons_has_faculty_departments_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `faculty_employee_departments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `faculty_employee_departments` (
  `faculty_employees_id` INT NOT NULL,
  `faculty_departments_id` INT NOT NULL,
  `function` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`faculty_employees_id`, `faculty_departments_id`),
  INDEX `fk_faculty_employees_has_faculty_departments_faculty_depart_idx` (`faculty_departments_id` ASC),
  INDEX `fk_faculty_employees_has_faculty_departments_faculty_employ_idx` (`faculty_employees_id` ASC),
  CONSTRAINT `fk_faculty_employees_has_faculty_departments_faculty_employees1`
  FOREIGN KEY (`faculty_employees_id`)
  REFERENCES `faculty_employees` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_faculty_employees_has_faculty_departments_faculty_departme1`
  FOREIGN KEY (`faculty_departments_id`)
  REFERENCES `faculty_departments` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `title_person_links` (
  `person_id` INT NOT NULL,
  `title_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`person_id`, `title_id`),
  CONSTRAINT `fk_titles_has_persons1`
  FOREIGN KEY (`person_id`)
  REFERENCES `persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_titles_has_titles`
  FOREIGN KEY (`title_id`)
  REFERENCES `titles` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
  ENGINE = InnoDB;