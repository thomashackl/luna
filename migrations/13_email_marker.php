<?php

/**
 * Adds markers for serial mails.
 */

require_once(realpath(__DIR__.'/../models/LunaMarker.php'));

class EmailMarker extends Migration {

    public function up() {
        $max = DBManager::get()->fetchOne("SELECT MAX(`priority`) AS maxprio FROM `luna_markers`");
        $max = $max['maxprio'];

        LunaMarker::create(
            [
                'marker' => 'EMAIL',
                'name' => 'E-Mail',
                'priority' => $max + 1,
                'type' => 'database-relation',
                'description' => 'Setzt die Standard-E-Mailadresse einer Person ein.',
                'replacement' => 'luna_users->user_id->luna_email->email->`default`=1'
            ]
        );

    }

    public function down() {
        DBManager::get()->exec("DELETE FROM  `luna_markers` WHERE `marker` = 'EMAIL'");
    }

}
