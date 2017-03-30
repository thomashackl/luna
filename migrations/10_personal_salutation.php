<?php

/**
 * Adds markers for serial mails.
 */

require_once(realpath(__DIR__.'/../models/LunaMarker.php'));

class PersonalSalutation extends Migration {

    public function up() {
        DBManager::get()->exec("UPDATE `luna_markers` SET `priority` = `priority` + 1 WHERE `priority` >= 2");

        LunaMarker::create(
            array(
                'marker' => 'PERSONAL_SALUTATION',
                'name' => 'Persönliche Anrede mit Vorname',
                'priority' => 2,
                'type' => 'text',
                'description' => 'Erzeugt eine persönliche Anrede: "Liebe Michaela" bzw. "Lieber Max".',
                'replacement' => 'Liebe/r {FIRSTNAME}',
                'replacement_male' => 'Lieber {FIRSTNAME}',
                'replacement_female' => 'Liebe {FIRSTNAME}'
            )
        );

    }

    public function down() {
        DBManager::get()->exec("DELETE FROM  `luna_markers` WHERE `marker` = 'PERSONAL_SALUTATION'");
        DBManager::get()->exec("UPDATE `luna_markers` SET `priority` = `priority` - 1 WHERE `priority` > 2");
    }

}
