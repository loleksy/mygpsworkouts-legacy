SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `username_canonical` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_canonical` VARCHAR(255) NOT NULL,
  `enabled` TINYINT(1) NOT NULL,
  `salt` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `last_login` DATETIME NULL DEFAULT NULL,
  `locked` TINYINT(1) NOT NULL,
  `expired` TINYINT(1) NOT NULL,
  `expires_at` DATETIME NULL DEFAULT NULL,
  `confirmation_token` VARCHAR(255) NULL DEFAULT NULL,
  `password_requested_at` DATETIME NULL DEFAULT NULL,
  `roles` LONGTEXT NOT NULL,
  `credentials_expired` TINYINT(1) NOT NULL,
  `credentials_expire_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sport`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sport` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(255) NOT NULL,
  `color` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sports_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_sports_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `workout`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `workout` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `sport_id` INT(11) NOT NULL,
  `start_datetime` DATETIME NOT NULL,
  `total_time_seconds` INT(11) NOT NULL,
  `distance_meters` INT(11) NOT NULL,
  `calories` INT(11) NULL DEFAULT NULL,
  `average_heart_rate_bpm` INT(11) NULL DEFAULT NULL,
  `maximum_heart_rate_bpm` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_table1_users1_idx` (`user_id` ASC),
  INDEX `fk_table1_sports1_idx` (`sport_id` ASC),
  CONSTRAINT `fk_table1_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_sports1`
    FOREIGN KEY (`sport_id`)
    REFERENCES `sport` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `trackpoint`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trackpoint` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `workout_id` BIGINT(20) NOT NULL,
  `index` INT(11) NOT NULL,
  `datetime` DATETIME NOT NULL,
  `lat` DECIMAL(9,6) NOT NULL,
  `lng` DECIMAL(9,6) NOT NULL,
  `altitude_meters` INT(11) NULL DEFAULT NULL,
  `heart_rate_bpm` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_trackpoints_workouts1_idx` (`workout_id` ASC),
  CONSTRAINT `fk_trackpoints_workouts1`
    FOREIGN KEY (`workout_id`)
    REFERENCES `workout` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
