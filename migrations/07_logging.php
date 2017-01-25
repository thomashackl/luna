<?php

/**
 * Adds logging capabilities.
 */

class Logging extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_log` (
            `entry_id` CHAR(32) NOT NULL,
            `client_id` CHAR(32) NOT NULL,
            `action` ENUM('create', 'update', 'delete', 'mail') NOT NULL,
            `user_id` CHAR(32) NOT NULL REFERENCES `auth_user_md5`.`user_id`,
            `affected` TEXT NOT NULL,
            `affected_type` ENUM('user', 'email', 'phone', 'company', 'client') NOT NULL,
            `info` TEXT NOT NULL,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`entry_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        SimpleORMap::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_log`");

        SimpleORMap::expireTableScheme();
    }

}
