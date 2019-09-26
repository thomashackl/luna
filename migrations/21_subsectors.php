<?php

/**
 * Adds an additional field to company entries which specifies some
 * subsector of business
 */

class Subsectors extends Migration {

    public function up() {
        DBManager::get()->execute("ALTER TABLE `luna_companies` 
            ADD `subsector` VARCHAR(255) NOT NULL DEFAULT '' AFTER `sector` 
        ");

        SimpleORMap::expireTableScheme();
    }

    public function down() {
        DBManager::get()->execute("ALTER TABLE `luna_companies`
            DROP `subsector`
        ");

        SimpleORMap::expireTableScheme();
    }

}