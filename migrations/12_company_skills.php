<?php

/**
 * Adds tags for companies.
 */

require_once(realpath(__DIR__.'/../models/LunaSkill.php'));
require_once(realpath(__DIR__.'/../models/LunaCompany.php'));

class CompanySkills extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_company_skill` (
            `skill_id` CHAR(32) NOT NULL REFERENCES `luna_skills`.`skill_id`,
            `company_id` CHAR(32) NOT NULL REFERENCES `luna_companies`.`company_id`,
            PRIMARY KEY (`skill_id`, `company_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        LunaSkill::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_company_skill`");
        LunaSkill::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

}
