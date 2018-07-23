<?php

/**
 * Adds database tables for defining and settings custom configuration per client.
 */

require_once(realpath(__DIR__ . '/../models/LunaClient.php'));

class CustomClientConfiguration extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_client_config` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `key` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `type` ENUM ('bool', 'text', 'int') NOT NULL DEFAULT 'bool',
                `mkdate` INT NOT NULL,
                `chdate` INT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY (`key`)
            ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_client_config_client` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `client_id` CHAR(32) REFERENCES `luna_client`.`client_id`,
                `key` VARCHAR(255) NOT NULL REFERENCES `luna_client_config`.`key`,
                `value` VARCHAR(255),
                `mkdate` INT NOT NULL,
                `chdate` INT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY (`client_id`, `key`)
            ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        DBManager::get()->exec("INSERT INTO `luna_client_config`
            (`key`, `description`, `type`, `mkdate`, `chdate`)
            VALUES
            ('auto_create_tags', 'Sollen Schlagwörter automatisch erzeugt werden?', 'bool', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())"
        );

        DBManager::get()->exec("INSERT INTO `luna_client_config_client`
            (`client_id`, `key`, `value`, `mkdate`, `chdate`)
            (
                SELECT `client_id`, 'auto_create_tags', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
                FROM `luna_clients`
            )"
        );
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_client_config_client`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_client_config`");
    }

}
