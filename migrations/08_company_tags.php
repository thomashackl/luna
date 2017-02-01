<?php

/**
 * Adds tags for companies.
 */

require_once(realpath(__DIR__.'/../models/LunaTag.php'));
require_once(realpath(__DIR__.'/../models/LunaCompany.php'));

class CompanyTags extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_company_tag` (
            `tag_id` CHAR(32) NOT NULL,
            `company_id` CHAR(32) NOT NULL,
            PRIMARY KEY (`tag_id`, `company_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        LunaTag::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_company_tag`");
        LunaTag::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

}
