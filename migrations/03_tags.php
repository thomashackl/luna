<?php

/**
 * Adds tag management.
 */

require_once(realpath(__DIR__.'/../models/LunaClient.php'));
require_once(realpath(__DIR__.'/../models/LunaUser.php'));

class Tags extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_tags` (
            `tag_id` CHAR(32) NOT NULL,
            `client_id` CHAR(32) NOT NULL,
            `name` VARCHAR(255) NOT NULL UNIQUE,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`tag_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_user_tag` (
            `tag_id` CHAR(32) NOT NULL,
            `user_id` CHAR(32) NOT NULL,
            PRIMARY KEY (`tag_id`, `user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        LunaClient::expireTableScheme();
        LunaUser::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE `luna_tags`");
        DBManager::get()->exec("DROP TABLE `luna_user_tag`");
    }

}
