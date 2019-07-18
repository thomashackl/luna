<?php

/**
 * Adds a field and adjusts primary key for last contacts.
 */

class LastContactDocuments extends Migration {

    public function up() {
        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            DROP PRIMARY KEY");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            ADD `contact_id` VARCHAR(32) NOT NULL FIRST");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact`
            ADD PRIMARY KEY (`contact_id`)");

        SimpleORMap::expireTableScheme();
    }

    public function down() {
        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            DROP PRIMARY KEY,
            DROP COLUMN `contact_id`");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            ADD PRIMARY KEY (`user_id`, `company_id`, `date`");
    }
}
