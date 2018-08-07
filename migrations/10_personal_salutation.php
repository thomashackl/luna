<?php

/**
 * Adds markers for serial mails.
 */

require_once(realpath(__DIR__.'/../models/LunaMarker.php'));

class PersonalSalutation extends Migration {

    public function up() {
        $stmt = DBManager::get()->prepare("UPDATE `luna_markers` SET `priority` = `priority` + 1 WHERE `marker` = ?");
        foreach (DBManager::get()->fetchAll("SELECT `marker` FROM `luna_markers` WHERE `priority` >= 2 ORDER BY `priority` DESC") as $entry) {
            $stmt->execute(array($entry['marker']));
        }

        LunaMarker::create(
            [
                'marker' => 'PERSONAL_SALUTATION',
                'name' => 'Persönliche Anrede mit Vorname',
                'priority' => 2,
                'type' => 'text',
                'description' => 'Erzeugt eine persönliche Anrede: "Liebe Michaela" bzw. "Lieber Max".',
                'replacement' => 'Liebe/r {FIRSTNAME}',
                'replacement_male' => 'Lieber {FIRSTNAME}',
                'replacement_female' => 'Liebe {FIRSTNAME}'
            ]
        );

    }

    public function down() {
        DBManager::get()->exec("DELETE FROM  `luna_markers` WHERE `marker` = 'PERSONAL_SALUTATION'");
        $stmt = DBManager::get()->prepare("UPDATE `luna_markers` SET `priority` = `priority` - 1 WHERE `marker` = ?");
        foreach (DBManager::get()->fetchAll("SELECT `marker` FROM `luna_markers` WHERE `priority` > 2 ORDER BY `priority` ASC") as $entry) {
            $stmt->execute(array($entry['marker']));
        }
    }

}
