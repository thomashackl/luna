<?php

/**
 * Adds markers for serial mails.
 */

class SerialMails extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_markers` (
            `marker_id` CHAR(32) NOT NULL,
            `marker` VARCHAR(255) UNIQUE NOT NULL,
            `name` VARCHAR(255) UNIQUE NOT NULL,
            `priority` INT(2) UNIQUE NOT NULL,
            `type` ENUM ('text', 'database', 'function') NOT NULL DEFAULT 'text',
            `description` TEXT NOT NULL,
            `replacement` TEXT NOT NULL,
            `replacement_female` TEXT NULL,
            `replacement_unknown` TEXT NULL,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`marker_id`)
        )");

        // Fill with available entries.
        $markers = array(
            array(
                'marker' => 'SALUTATION',
                'name' => 'Anrede',
                'priority' => 1,
                'type' => 'text',
                'description' => 'Erzeugt eine vollständige Anrede: "Sehr geehrte Michaela Musterfrau" bzw. "Sehr geehrter Max Mustermann".',
                'replacement' => 'Sehr geehrter {FULLNAME}',
                'replacement_female' => 'Sehr geehrte {FULLNAME}',
                'replacement_unknown' => 'Sehr geehrte/r {FULLNAME}'
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
                'description' => 'Setzt die vollständige Adresse mit Zeilenumbrüchen ein, z.B.
                    Prof. Dr. Max Mustermann
                    Musterstraße 47
                    12345 Musterstadt',
                'replacement' => '{FULLNAME_WITH_TITLE}
{STREET}
{CITY}'
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
