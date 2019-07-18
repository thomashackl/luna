<?php

/**
 * Adds fields to Company
 */

class StateAndRegionEntries extends Migration {

    public function up() {
        DBManager::get()->execute("ALTER TABLE `luna_companies` 
            ADD `region` VARCHAR(255) NOT NULL DEFAULT '' AFTER `city`, 
            ADD `state` VARCHAR(255) NOT NULL DEFAULT '' AFTER `region`
        ");

        SimpleORMap::expireTableScheme();
    }

    public function down() {
        DBManager::get()->execute("ALTER TABLE `luna_companies`
            DROP `region`,
            DROP `state`   
        ");

        SimpleORMap::expireTableScheme();
    }

}