<?php

require_once(realpath(__DIR__ . '/../models/LunaMarker.php'));

/**
 * Reworks serial mail fields:
 * - "Salutation" should not contain the name anymore
 * - New field for written out title ("Professor" instead of "Prof.")
 * - Status available as serial mail field
 */

class ReworkSerialMailFields extends Migration {

    public function description()
    {
        return 'Reworks the "salutation" serial mail field and adds new ' .
            'fields for front title, front title in written out form and status.';
    }

    public function up()
    {
        DBManager::get()->execute("ALTER TABLE `luna_users` 
            ADD `title_front_full` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title_front`
        ");

        $salutation = LunaMarker::findOneByMarker('SALUTATION');
        $salutation->description = 'Erzeugt eine Anrede: "Sehr geehrte Frau" bzw. "Sehr geehrter Herr".';
        $salutation->replacement = 'Sehr geehrte/r';
        $salutation->replacement_male = 'Sehr geehrter Herr';
        $salutation->replacement_female = 'Sehr geehrte Frau';
        $salutation->replacement_unknown = null;
        $salutation->store();

        // Make space for new entries for title.
        DBManager::get()->execute("ALTER TABLE `luna_markers` DROP INDEX `priority`");
        DBManager::get()->execute("UPDATE `luna_markers` SET `priority` = `priority` + 2 WHERE `priority` >= 3");
        DBManager::get()->execute("ALTER TABLE `luna_markers` ADD UNIQUE `priority` (`priority`)");

        $title = new LunaMarker();
        $title->marker = 'TITLE_FRONT';
        $title->name = 'Vorangestellter Titel';
        $title->description = 'Setzt den vorangestellten Titel einer Person ein: "Prof." oder "Dr." o.ä.';
        $title->type = 'database';
        $title->replacement = 'luna_users.title_front';
        $title->priority = 3;
        $title->store();

        $titleFull = new LunaMarker();
        $titleFull->marker = 'TITLE_FRONT_FULL';
        $titleFull->name = 'Vorangestellter, ausgeschriebener Titel';
        $titleFull->description =
            'Setzt den vorangestellten, ausgeschriebenen Titel einer Person ein: "Professor" oder "Doktor" o.ä.';
        $titleFull->type = 'database';
        $titleFull->replacement = 'luna_users.title_front_full';
        $titleFull->priority = 4;
        $titleFull->store();

        // Make space for new entry for status.
        DBManager::get()->execute("ALTER TABLE `luna_markers` DROP INDEX `priority`");
        DBManager::get()->execute("UPDATE `luna_markers` SET `priority` = `priority` + 1 WHERE `priority` >= 7");
        DBManager::get()->execute("ALTER TABLE `luna_markers` ADD UNIQUE `priority` (`priority`)");

        $status = new LunaMarker();
        $status->marker = 'STATUS';
        $status->name = 'Status';
        $status->description =
            'Status einer Person: "Geschäftsführer" o.ä.';
        $status->type = 'database';
        $status->replacement = 'luna_users.status';
        $status->priority = 7;
        $status->store();

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        DBManager::get()->execute("ALTER TABLE `luna_users` DROP `title_front_full`");

        DBManager::get()->execute("DELETE FROM `luna_markers`
            WHERE `marker` IN ('TITLE_FRONT', 'TITLE_FRONT_FULL', 'STATUS')");
        DBManager::get()->execute("ALTER TABLE `luna_markers` DROP INDEX `priority`");
        DBManager::get()->execute("UPDATE `luna_markers` SET `priority` = `priority` - 1 WHERE `priority` >= 8");
        DBManager::get()->execute("UPDATE `luna_markers` SET `priority` = `priority` - 2 WHERE `priority` >= 5");
        DBManager::get()->execute("ALTER TABLE `luna_markers` ADD UNIQUE `priority` (`priority`)");

        SimpleORMap::expireTableScheme();
    }

}