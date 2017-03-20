<?php

/**
 * Provides the possibility to have as many e-mail addresses
 * and phone numbers as necessary per person
 */

require_once(realpath(__DIR__.'/../models/LunaUser.php'));

class MailAndPhoneEntries extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_email` (
            `user_id` CHAR(32) NOT NULL,
            `email` CHAR(255) NOT NULL,
            `type` ENUM ('private', 'office') NOT NULL DEFAULT 'private',
            `default` TINYINT(1) NOT NULL DEFAULT 0,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`, `email`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_phone` (
            `user_id` CHAR(32) NOT NULL,
            `number` CHAR(255) NOT NULL,
            `type` ENUM ('private', 'office', 'mobile') NOT NULL DEFAULT 'private',
            `default` TINYINT(1) NOT NULL DEFAULT 0,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`, `number`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        $estmt = DBManager::get()->prepare("INSERT INTO `luna_email`
            VALUES (:uid, :mail, :type, :default, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
        $pstmt = DBManager::get()->prepare("INSERT INTO `luna_phone`
            VALUES (:uid, :number, :type, :default, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");

        // Transfer existing data to new tables.
        $data = DBManager::get()->fetchAll(
            "SELECT `user_id`, `email_private`, `email_office`, `phone_private`, `phone_office`, `phone_mobile`
            FROM `luna_users` ORDER BY `user_id`");
        foreach ($data as $u) {
            if ($u['email_private']) {
                $estmt->execute(array(
                    'uid' => $u['user_id'],
                    'mail' => $u['email_private'],
                    'type' => 'private',
                    'default' => 1
                ));
            }
            if ($u['email_office']) {
                $estmt->execute(array(
                    'uid' => $u['user_id'],
                    'mail' => $u['email_office'],
                    'type' => 'office',
                    'default' => $u['email_private'] ? 0 : 1
                ));
            }
            if ($u['phone_private']) {
                $pstmt->execute(array(
                    'uid' => $u['user_id'],
                    'number' => $u['phone_private'],
                    'type' => 'private',
                    'default' => 1
                ));
            }
            if ($u['phone_mobile']) {
                $pstmt->execute(array(
                    'uid' => $u['user_id'],
                    'number' => $u['phone_mobile'],
                    'type' => 'mobile',
                    'default' => $u['phone_private'] ? 0 : 1
                ));
            }
            if ($u['phone_office']) {
                $pstmt->execute(array(
                    'uid' => $u['user_id'],
                    'number' => $u['phone_office'],
                    'type' => 'office',
                    'default' => $u['phone_private'] || $u['phone_mobile'] ? 0 : 1
                ));
            }
        }

        DBManager::get()->execute("ALTER TABLE `luna_users`
            DROP `email_private`, DROP `email_office`, DROP `phone_private`, DROP `phone_mobile`, DROP `phone_office`");

        LunaUser::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_email`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_phone`");

        DBManager::get()->exec("ALTER TABLE `luna_users`
            ADD `email_office` VARCHAR(255) NOT NULL AFTER `country`,
            ADD `email_private` VARCHAR(255) NOT NULL AFTER `email_office`,
            ADD `phone_office` VARCHAR(255) NOT NULL AFTER `email_private`,
            ADD `phone_private` VARCHAR(255) NOT NULL AFTER `phone_office`,
            ADD `phone_mobile` VARCHAR(255) NOT NULL AFTER `phone_private`");

        LunaUser::expireTableScheme();
    }

}
