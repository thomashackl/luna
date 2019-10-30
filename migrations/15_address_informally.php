<?php

/**
 * Adds a person field for informal addressing.
 */

class AddressInformally extends Migration {

    public function up() {
        DBManager::get()->exec("ALTER TABLE `luna_users` ADD `informal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `country`");
        DBManager::get()->exec("ALTER TABLE `luna_markers` ADD `replacement_informal` VARCHAR(255) NOT NULL DEFAULT ''
            AFTER `replacement_unknown`");
        DBManager::get()->exec(
            "UPDATE `luna_markers` SET `replacement_informal` = 'PERSONAL_SALUTATION' WHERE `marker` = 'SALUTATION'");

        SimpleORMap::expireTableScheme();

        foreach (LunaTag::findByName('Duz-Freund') as $tag) {
            foreach ($tag->users as $user) {
                $user->informal = 1;
                $user->store();
            }
            $tag->delete();
        }
    }

    public function down() {
        DBManager::get()->exec("ALTER TABLE `luna_users` DROP `informal`");
        DBManager::get()->exec("ALTER TABLE `luna_markers` DROP `replacement_informal`");

        SimpleORMap::expireTableScheme();
    }

}
