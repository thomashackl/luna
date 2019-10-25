<?php

/**
 * A company should have several contact persons in different functions
 */

require_once(realpath(__DIR__ . '/../models/LunaCompanyContacatPerson.php'));

class CompanyContactPersons extends Migration {

    public function description()
    {
        return 'Allows several contact persons for companies';
    }

    public function up()
    {
        // Create new table for contact persons.
        DBManager::get()->execute("CREATE TABLE IF NOT EXISTS `luna_company_contact_person` (
            `contact_person_id` INT NOT NULL AUTO_INCREMENT,
            `company_id` VARCHAR(32) NOT NULL REFERENCES `luna_companies`.`company_id`,
            `person_id` VARCHAR(32) NOT NULL REFERENCES  `luna_users`.`user_id`,
            `function` VARCHAR(255) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`contact_person_id`),
            INDEX (`company_id`),
            INDEX (`person_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        // Get all contact persons and insert them into new table.
        $entries = DBManager::get()->fetchAll("SELECT `company_id`, `contact_person`
            FROM `luna_companies`
            WHERE `contact_person` IS NOT NULL AND `contact_person` != ''");

        foreach ($entries as $one) {
            if (LunaUser::find($one['contact_person'])) {
                $p = new LunaCompanyContactPerson();
                $p->company_id = $one['company_id'];
                $p->person_id = $one['contact_person'];
                $p->function = '';
                $p->store();
            }
        }

        // Now cleanup luna_companies table
        DBManager::get()->execute("ALTER TABLE `luna_companies` DROP `contact_person`");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        // Now cleanup luna_companies table
        DBManager::get()->execute("ALTER TABLE `luna_companies` ADD
            `contact_person` VARCHAR(255) NULL DEFAULT NULL AFTER `subsector`");

        // Get all contact persons, grab one and insert it into luna_companies table.
        $entries = DBManager::get()->fetchAll("SELECT `company_id`, `person_id`
            FROM `luna_company_contact_person` GROUP BY `company_id` ORDER BY `function`");

        $stmt = DBManager::get()->prepare("UPDATE `luna_companies` SET `contact_person` = :contact
            WHERE `company_id` = :id");

        foreach ($entries as $one) {
            $stmt->execute(['contact' => $one['person_id'], 'id' => $one['company_id']]);
        }

        SimpleORMap::expireTableScheme();
    }

}
