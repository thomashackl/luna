<?php

/**
 * Adds a column to luna_companies table for specifying the industrial sector.
 */

require_once(realpath(__DIR__.'/../models/LunaCompany.php'));

class CompanySectors extends Migration {

    public function up() {
        DBManager::get()->exec("ALTER TABLE `luna_companies`
            ADD `sector` VARCHAR(255) NOT NULL DEFAULT '' AFTER `homepage`");

        LunaCompany::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("ALTER TABLE `luna_users`
            DROP `sector`");

        LunaCompany::expireTableScheme();
    }

}
