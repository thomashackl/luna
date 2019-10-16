<?php

require_once(__DIR__ . '/../models/LunaCompanyLastContact.php');

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

        // Get all entries - they will need new IDs.
        $entries = DBManager::get()->fetchAll("SELECT * FROM `luna_company_last_contact`");

        SimpleORMap::expireTableScheme();

        DBManager::get()->execute("TRUNCATE TABLE `luna_company_last_contact`");

        // Generate new IDs for each entry.
        foreach ($entries as $entry) {
            $one = new LunaCompanyLastContact();
            $one->user_id = $entry['user_id'];
            $one->company_id = $entry['company_id'];
            $one->date = $entry['date'];
            $one->contact = $entry['contact'];
            $one->notes = $entry['notes'];
            $one->mkdate = $entry['mkdate'];
            $one->chdate = $entry['chdate'];
            $one->store();
        }
    }

    public function down() {
        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            DROP PRIMARY KEY,
            DROP COLUMN `contact_id`");

        DBManager::get()->execute("ALTER TABLE `luna_company_last_contact` 
            ADD PRIMARY KEY (`user_id`, `company_id`, `date`");
    }
}
