-- MySQL Script generated by MySQL Workbench
-- Tue Jan 17 11:33:57 2023
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema my_database
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema my_database
-- -----------------------------------------------------
DROP DATABASE IF EXISTS my_database;
CREATE SCHEMA my_database DEFAULT CHARACTER SET utf8mb4;
USE my_database ;

-- -----------------------------------------------------
-- Table my_database.users
-- -----------------------------------------------------
CREATE TABLE users (
  userId INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(45) NOT NULL,
  pwd VARCHAR(255) NOT NULL,
  name VARCHAR(45) NOT NULL,
  PRIMARY KEY (userId)
  );


-- -----------------------------------------------------
-- Table my_database.tasks
-- -----------------------------------------------------
CREATE TABLE tasks (
  taskId INT NOT NULL AUTO_INCREMENT,
  userId INT NOT NULL,
  title VARCHAR(100) NULL,
  description LONGTEXT NULL,
  status ENUM("TO DO", "DOING", "DONE") NULL,
  startDate DATETIME NULL,
  modDate DATETIME NULL,
  endDate DATETIME NULL,
  PRIMARY KEY (taskId, userId)
  );

  # Create FKs
ALTER TABLE tasks
    ADD    FOREIGN KEY (userId)
    REFERENCES users(userId)
    ON DELETE CASCADE
;