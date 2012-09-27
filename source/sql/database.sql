SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `solviv` ;
CREATE SCHEMA IF NOT EXISTS `solviv` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `solviv` ;

-- -----------------------------------------------------
-- Table `solviv`.`sys_modules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`sys_modules` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`sys_modules` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`sys_controllers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`sys_controllers` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`sys_controllers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  `sys_modules_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_sys_controllers_sys_modules`
    FOREIGN KEY (`sys_modules_id` )
    REFERENCES `solviv`.`sys_modules` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`sys_typeactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`sys_typeactions` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`sys_typeactions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` BIT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`sys_actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`sys_actions` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`sys_actions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  `sys_controllers_id` INT NOT NULL ,
  `sys_typeactions_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_sys_actions_sys_controllers1`
    FOREIGN KEY (`sys_controllers_id` )
    REFERENCES `solviv`.`sys_controllers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sys_actions_sys_typeactions1`
    FOREIGN KEY (`sys_typeactions_id` )
    REFERENCES `solviv`.`sys_typeactions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`roles` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`permisos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`permisos` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`permisos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `roles_id` INT NOT NULL ,
  `sys_actions_id` INT NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_permisos_roles1`
    FOREIGN KEY (`roles_id` )
    REFERENCES `solviv`.`roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_permisos_sys_actions1`
    FOREIGN KEY (`sys_actions_id` )
    REFERENCES `solviv`.`sys_actions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`tablas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`tablas` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`tablas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombres` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`usuarios` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `roles_id` INT NOT NULL ,
  `nombres` VARCHAR(150) NOT NULL ,
  `apellidoPaterno` VARCHAR(150) NOT NULL ,
  `apellidoMaterno` VARCHAR(150) NULL ,
  `email` VARCHAR(150) NOT NULL ,
  `usuario` VARCHAR(80) NOT NULL ,
  `clave` VARCHAR(100) NOT NULL ,
  `active` BIT NOT NULL DEFAULT 1 ,
  `usuarios_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_usuarios_roles1`
    FOREIGN KEY (`roles_id` )
    REFERENCES `solviv`.`roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_usuarios1`
    FOREIGN KEY (`usuarios_id` )
    REFERENCES `solviv`.`usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`acciones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`acciones` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`acciones` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(150) NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  `descripcion` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`historiales`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`historiales` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`historiales` (
  `id` INT NOT NULL ,
  `tablas_id` INT NOT NULL ,
  `usuarios_id` INT NOT NULL ,
  `campo` VARCHAR(100) NOT NULL ,
  `oldData` TEXT NULL ,
  `newData` TEXT NULL ,
  `fecha` DATETIME NOT NULL ,
  `acciones_id` INT NOT NULL ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_historiales_tablas1`
    FOREIGN KEY (`tablas_id` )
    REFERENCES `solviv`.`tablas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_historiales_usuarios1`
    FOREIGN KEY (`usuarios_id` )
    REFERENCES `solviv`.`usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_historiales_acciones1`
    FOREIGN KEY (`acciones_id` )
    REFERENCES `solviv`.`acciones` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`versiones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`versiones` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`versiones` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `tablas_id` INT NOT NULL ,
  `acciones_id` INT NOT NULL ,
  `oldData` TEXT NULL ,
  `newData` TEXT NULL ,
  `fecha` DATETIME NOT NULL DEFAULT timestamp ,
  `active` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_versiones_tablas1`
    FOREIGN KEY (`tablas_id` )
    REFERENCES `solviv`.`tablas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_versiones_acciones1`
    FOREIGN KEY (`acciones_id` )
    REFERENCES `solviv`.`acciones` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`auditoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`auditoria` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`auditoria` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versiones_id` INT NOT NULL ,
  `usuarios_id` INT NOT NULL ,
  `active` BIT NOT NULL DEFAULT 1 ,
  `descripcion` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_table1_versiones1`
    FOREIGN KEY (`versiones_id` )
    REFERENCES `solviv`.`versiones` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_usuarios1`
    FOREIGN KEY (`usuarios_id` )
    REFERENCES `solviv`.`usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `solviv`.`dbversion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `solviv`.`dbversion` ;

CREATE  TABLE IF NOT EXISTS `solviv`.`dbversion` (
  `id` INT NOT NULL ,
  `version` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
