<?php

/**
 * Creates all necessary tables and access role.
 */

class Setup extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_clients` (
            `client_id` CHAR(32) NOT NULL,
            `name` VARCHAR(255) NULL,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`client_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_client_user` (
            `client_id` CHAR(32) NOT NULL REFERENCES `luna_clients`.`client_id`,
            `user_id` CHAR(32) NOT NULL REFERENCES `auth_user_md5`.`user_id`,
            `status` ENUM ('read', 'write', 'admin') NOT NULL DEFAULT 'read',
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`client_id`, `user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_users` (
            `user_id` CHAR(32) NOT NULL,
            `client_id` CHAR(32) NOT NULL REFERENCES `luna_clients`.`client_id`,
            `firstname` VARCHAR(255) NOT NULL DEFAULT '',
            `lastname` VARCHAR(255) NOT NULL DEFAULT '',
            `title_front` VARCHAR(255) NOT NULL DEFAULT '',
            `title_rear` VARCHAR(255) NOT NULL DEFAULT '',
            `gender` TINYINT(4) NOT NULL DEFAULT 0,
            `studip_user_id` CHAR(32) NULL DEFAULT NULL REFERENCES `auth_user_md5`.`user_id`,
            `street` VARCHAR(255) NOT NULL DEFAULT '',
            `zip` VARCHAR(20) NOT NULL DEFAULT '',
            `city` VARCHAR(255) NOT NULL DEFAULT '',
            `country` VARCHAR(255) NOT NULL DEFAULT 'Deutschland',
            `email_office` VARCHAR(255) NOT NULL DEFAULT '',
            `email_private` VARCHAR(255) NULL DEFAULT NULL,
            `phone_office` VARCHAR(255) NOT NULL DEFAULT '',
            `phone_private` VARCHAR(255) NOT NULL DEFAULT '',
            `phone_mobile` VARCHAR(255) NOT NULL DEFAULT '',
            `fax` VARCHAR(255) NOT NULL DEFAULT '',
            `homepage` VARCHAR(255) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_user_info` (
            `user_id` CHAR(32) NOT NULL REFERENCES `luna_users`.`user_id`,
            `status` VARCHAR(255) NULL,
            `graduation` VARCHAR(255) NULL,
            `vita` TEXT NULL,
            `qualifications` TEXT NULL,
            `notes` TEXT NULL,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_companies` (
            `company_id` CHAR(32) NOT NULL,
            `client_id` CHAR(32) NOT NULL REFERENCES `luna_clients`.`client_id`,
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            `street` VARCHAR(255) NULL DEFAULT NULL,
            `zip` VARCHAR(255) NOT NULL DEFAULT '',
            `city` VARCHAR(255) NOT NULL DEFAULT '',
            `country` VARCHAR(255) NOT NULL DEFAULT '',
            `email` VARCHAR(255) NOT NULL DEFAULT '',
            `phone` VARCHAR(255) NOT NULL DEFAULT '',
            `fax` VARCHAR(255) NOT NULL DEFAULT '',
            `homepage` VARCHAR(255) NOT NULL DEFAULT '',
            `contact_person` VARCHAR(255) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`company_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_user_company` (
            `user_id` CHAR(32) NOT NULL REFERENCES `luna_users`.`user_id`,
            `company_id` CHAR(32) NOT NULL REFERENCES `luna_companies`.`company_id`,
            PRIMARY KEY (`user_id`, `company_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_skills` (
            `skill_id` CHAR(32) NOT NULL,
            `client_id` CHAR(32) NOT NULL REFERENCES `luna_clients`.`client_id`,
            `name` VARCHAR(255) NOT NULL DEFAULT '' UNIQUE,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`skill_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_user_skills` (
            `skill_id` CHAR(32) NOT NULL REFERENCES `luna_skills`.`skill_id`,
            `user_id` CHAR(32) NOT NULL REFERENCES `luna_users`.`user_id`,
            PRIMARY KEY (`skill_id`, `user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_user_skills`");
        DBManager::get()->exec("DROP TABLE `luna_skills`");
        DBManager::get()->exec("DROP TABLE `luna_user_company`");
        DBManager::get()->exec("DROP TABLE `luna_companies`");
        DBManager::get()->exec("DROP TABLE `luna_user_info`");
        DBManager::get()->exec("DROP TABLE `luna_users`");
        DBManager::get()->exec("DROP TABLE `luna_client_user`");
        DBManager::get()->exec("DROP TABLE `luna_clients`");
    }

}
