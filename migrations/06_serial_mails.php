<?php

/**
 * Adds markers for serial mails.
 */

require_once(realpath(__DIR__.'/../models/LunaMarker.php'));

class SerialMails extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_markers` (
            `marker_id` CHAR(32) NOT NULL,
            `marker` VARCHAR(255) UNIQUE NOT NULL,
            `name` VARCHAR(255) UNIQUE NOT NULL,
            `priority` INT(2) UNIQUE NOT NULL,
            `type` ENUM ('text', 'database', 'database-relation', 'function') NOT NULL DEFAULT 'text',
            `description` TEXT NOT NULL,
            `replacement` TEXT NOT NULL,
            `replacement_male` TEXT NULL,
            `replacement_female` TEXT NULL,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`marker_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        // Fill with available entries.
        $markers = array(
            array(
                'marker' => 'SALUTATION',
                'name' => 'Anrede',
                'priority' => 1,
                'type' => 'text',
                'description' => 'Erzeugt eine vollständige Anrede: "Sehr geehrte Frau Michaela Musterfrau" bzw. "Sehr geehrter Herr Max Mustermann".',
                'replacement' => 'Sehr geehrte/r {FULLNAME}',
                'replacement_male' => 'Sehr geehrter Herr {FULLNAME}',
                'replacement_female' => 'Sehr geehrte Frau {FULLNAME}',
            ),
            array(
                'marker' => 'FULLNAME',
                'name' => 'Name ohne Titel',
                'priority' => 2,
                'type' => 'database',
                'description' => 'Setzt den vollen Namen der jeweiligen Person ein, z.B. "Max Mustermann".',
                'replacement' => '{FIRSTNAME} {LASTNAME}'
            ),
            array(
                'marker' => 'FULLNAME_WITH_TITLE',
                'name' => 'Name mit Titeln',
                'priority' => 3,
                'type' => 'database',
                'description' => 'Setzt den vollen Namen der jeweiligen Person mit Titeln ein, z.B. "Prof. Dr. Max Mustermann, PhD".',
                'replacement' => 'luna_users.title_front {FULLNAME} luna_users.title_rear'
            ),
            array(
                'marker' => 'FIRSTNAME',
                'name' => 'Vorname',
                'priority' => 5,
                'type' => 'database',
                'description' => 'Setzt den Vornamen der jeweiligen Person ein.',
                'replacement' => 'luna_users.firstname'
            ),
            array(
                'marker' => 'LASTNAME',
                'name' => 'Nachname',
                'priority' => 6,
                'type' => 'database',
                'description' => 'Setzt den Nachnamen der jeweiligen Person ein.',
                'replacement' => 'luna_users.lastname'
            ),
            array(
                'marker' => 'STREET',
                'name' => 'Straße',
                'priority' => 7,
                'type' => 'database',
                'description' => 'Setzt die Straße ein.',
                'replacement' => 'luna_users.street'
            ),
            array(
                'marker' => 'CITY',
                'name' => 'PLZ + Stadt',
                'priority' => 8,
                'type' => 'database',
                'description' => 'Setzt den Wohnort inkl. PLZ ein.',
                'replacement' => 'luna_users.zip luna_users.city'
            ),
            array(
                'marker' => 'ADDRESS',
                'name' => 'Anschrift',
                'priority' => 4,
                'type' => 'text',
                'description' => 'Setzt die vollständige Anschrift mit Zeilenumbrüchen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstraße 47
                    12345 Musterstadt',
                'replacement' => "{FULLNAME_WITH_TITLE}\r\n{STREET}\r\n{CITY}"
            ),
            array(
                'marker' => 'COMPANY',
                'name' => 'Unternehmen',
                'priority' => 9,
                'type' => 'database-relation',
                'description' => 'Setzt den Namen der Firma ein, der die Person zugeordnet ist.',
                'replacement' => 'luna_user_company->company_id->luna_companies->name'
            ),
        );

        foreach ($markers as $data) {
            LunaMarker::create($data);
        }

    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_markers`");
    }

}
