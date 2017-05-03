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
            `description` = 'Setzt die Adresse (Stra�e) ein.'
        WHERE `marker` = 'STREET'");
        DBManager::get()->exec("UPDATE `luna_markers`
          SET `name` = 'Vollst�ndige Anschrift',
            `description` = 'Setzt die vollst�ndige Anschrift mit Zeilenumbr�chen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstra�e 47
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
            `name` = 'Stra�e',
            `description` = 'Setzt die Stra�e ein.'
        WHERE `marker` = 'STREET'");
        DBManager::get()->exec("UPDATE `luna_markers`
          SET `name` = 'Anschrift',
            `description` = 'Setzt die vollst�ndige Anschrift mit Zeilenumbr�chen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstra�e 47
                    12345 Musterstadt'
          WHERE `marker` = 'ADDRESS'");

        LunaUser::expireTableScheme();
        LunaCompany::expireTableScheme();
    }

}
