<?php

/**
 * Removes the luna_user_info table, integrating the data
 * into into luna_user_info.
 */

require_once(realpath(__DIR__.'/../models/LunaUser.php'));

class LargerAddressField extends Migration {

    public function up() {
        DBManager::get()->exec("ALTER TABLE `luna_users`
            CHANGE `street` `address` TEXT NOT NULL DEFAULT ''");
        DBManager::get()->exec("ALTER TABLE `luna_companies`
            CHANGE `street` `address` TEXT NOT NULL DEFAULT ''");

        DBManager::get()->exec("UPDATE `luna_markers`
          SET `replacement` = 'luna_users.address',
            `name` = 'Adresse',
            `description` = 'Setzt die Adresse (Straße) ein.'
        WHERE `marker` = 'STREET'");
        DBManager::get()->exec("UPDATE `luna_markers`
          SET `name` = 'Vollständige Anschrift',
            `description` = 'Setzt die vollständige Anschrift mit Zeilenumbrüchen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstraße 47
                    12345 Musterstadt'
          WHERE `marker` = 'ADDRESS'");

        LunaUser::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("ALTER TABLE `luna_users`
            CHANGE `address` `street` VARCHAR(255) NOT NULL DEFAULT ''");
        DBManager::get()->exec("ALTER TABLE `luna_companies`
            CHANGE `address` `street` VARCHAR(255) NOT NULL DEFAULT ''");

        DBManager::get()->exec("UPDATE `luna_markers` SET `replacement` = 'luna_users.street' WHERE `marker` = 'STREET'");
        DBManager::get()->exec("UPDATE `luna_markers`
          SET `replacement` = 'luna_users.street',
            `name` = 'Straße',
            `description` = 'Setzt die Straße ein.'
        WHERE `marker` = 'STREET'");
        DBManager::get()->exec("UPDATE `luna_markers`
          SET `name` = 'Anschrift',
            `description` = 'Setzt die vollständige Anschrift mit Zeilenumbrüchen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstraße 47
                    12345 Musterstadt'
          WHERE `marker` = 'ADDRESS'");

        LunaUser::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

}
