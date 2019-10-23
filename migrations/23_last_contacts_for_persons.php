<?php

/**
 * Persons can also have last contacts now, so we must rework the database table:
 * - Rename "luna_company_contacts" to "luna_last_contacts"
 * - Add a column for entry type ("person" or "company")
 * - Adjust primary key and index
 */

class LastContactsForPersons extends Migration {

    public function description()
    {
        return 'Adds last contact administration for persons.';
    }

    public function up()
    {
        DBManager::get()->execute("RENAME TABLE `luna_company_last_contact` TO `luna_last_contact`");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` ADD `type` ENUM('person', 'company') NOT NULL
            DEFAULT 'company' AFTER `company_id`;");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` DROP INDEX `i_company_id`");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` CHANGE `company_id` `luna_object_id`
            VARCHAR(32) NOT NULL;");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` ADD INDEX(`luna_object_id`, `type`)");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {

        DBManager::get()->execute("ALTER TABLE `luna_last_contact` DROP INDEX `luna_object_id`");
       DBManager::get()->execute("ALTER TABLE `luna_last_contact` CHANGE `luna_object_id` `company_id`
            CHAR(32) NOT NULL;");
        DBManager::get()->execute("DELETE FROM `luna_last_contact` WHERE `type` = 'person'");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` DROP `type`");
        DBManager::get()->execute("ALTER TABLE `luna_last_contact` ADD INDEX `i_company_id` ( `company_id`)");
        DBManager::get()->execute("RENAME TABLE `luna_last_contact` TO `luna_company_last_contact`");

        SimpleORMap::expireTableScheme();
    }

}
