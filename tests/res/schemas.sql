SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `lemon-test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `lemon-test` ;

-- -----------------------------------------------------
-- Table `lemon-test`.`tree`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lemon-test`.`tree` ;

CREATE  TABLE IF NOT EXISTS `lemon-test`.`tree` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `age` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lemon-test`.`tree_has_parent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lemon-test`.`tree_has_parent` ;

CREATE  TABLE IF NOT EXISTS `lemon-test`.`tree_has_parent` (
  `tree_parent_id` INT NOT NULL  ,
  `tree_child_id` INT NOT NULL  ,
  `age` INT NULL ,
  PRIMARY KEY (`tree_parent_id`,`tree_child_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lemon-test`.`lemon`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lemon-test`.`lemon` ;

CREATE  TABLE IF NOT EXISTS `lemon-test`.`lemon` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `tree_id` INT NOT NULL ,
  `mature` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_lemon_tree1` (`tree_id` ASC) ,
  CONSTRAINT `fk_lemon_tree1`
    FOREIGN KEY (`tree_id` )
    REFERENCES `lemon-test`.`tree` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lemon-test`.`seed`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lemon-test`.`seed` ;

CREATE  TABLE IF NOT EXISTS `lemon-test`.`seed` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `lemon_id` INT NOT NULL ,
  `fertil` TINYINT NULL ,
  INDEX `fk_seed_lemon1` (`lemon_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_seed_lemon1`
    FOREIGN KEY (`lemon_id` )
    REFERENCES `lemon-test`.`lemon` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lemon-test`.`leaf`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lemon-test`.`leaf` ;

CREATE  TABLE IF NOT EXISTS `lemon-test`.`leaf` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `length` INT NULL ,
  `tree_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_leaf_tree` (`tree_id` ASC) ,
  CONSTRAINT `fk_leaf_tree`
    FOREIGN KEY (`tree_id` )
    REFERENCES `lemon-test`.`tree` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
