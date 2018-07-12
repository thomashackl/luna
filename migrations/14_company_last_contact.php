<?php

/**
 * Adds fields for last company contact.
 */

class CompanyLastContact extends Migration {

    public function up() {
        DBManager::get()->execute("CREATE TABLE IF NOT EXISTS `luna_company_last_contact` (
            `user_id` CHAR(32) NOT NULL REFERENCES `auth_user_md5`.`user_id`,
            `company_id` CHAR(32) NOT NULL REFERENCES `luna_companies`.`company_id`,
            `date` INT NOT NULL,
            `contact` VARCHAR(255) NOT NULL DEFAULT '',
            `notes` TEXT NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`, `company_id`, `date`),
            INDEX `i_company_id` (`company_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_company_last_contact`");
    }

}
