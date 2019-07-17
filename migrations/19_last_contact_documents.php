<?php

/**
 * Adds field for last company contact and adds fields for last contact documents.
 */

class LastContactDocuments extends Migration {

    public function up() {
        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            DROP PRIMARY KEY");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            ADD `contact_id` INT NOT NULL AUTO_INCREMENT FIRST, 
            ADD PRIMARY KEY (`contact_id`)");

        DBManager::get()->execute("CREATE TABLE IF NOT EXISTS `luna_last_contact_documents`(
            `contact_id` INT NOT NULL REFERENCES `luna_company_last_contact`.`contact_id`,
            `file_ref_id` CHAR(32) NOT NULL REFERENCES `file_refs`.`id`,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`contact_id`, `file_ref_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        SimpleORMap::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_last_contact_documents`");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            DROP PRIMARY KEY,
            DROP COLUMN `contact_id`");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            ADD PRIMARY KEY (`user_id`, `company_id`, `date`");
    }
}
