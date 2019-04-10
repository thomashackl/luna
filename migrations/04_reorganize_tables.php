<?php

/**
 * Removes the luna_user_info table, integrating the data
 * into into luna_user_info.
 */

require_once(realpath(__DIR__.'/../models/LunaUser.php'));

class ReorganizeTables extends Migration {

    public function up() {
        DBManager::get()->exec("ALTER TABLE `luna_users`
            ADD `status` VARCHAR(255) NULL AFTER `homepage`,
            ADD `graduation` VARCHAR(255) NULL AFTER `status`,
            ADD `notes` TEXT NULL AFTER `graduation`");

        $stmt = DBManager::get()->prepare("UPDATE `luna_users`
            SET `status` = :status, `graduation` = :graduation, `notes` = :notes
            WHERE `user_id` = :uid");

        foreach (DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` FROM `luna_users` ORDER BY `user_id`") as $u) {
            $info = DBManager::get()->fetchOne("SELECT * FROM `luna_user_info` WHERE `user_id` = ?", [$u]);
            $stmt->execute([
                'uid' => $u,
                'status' => $info['status'],
                'graduation' => $info['graduation'],
                'notes' => $info['notes']
            ]);
        }

        DBManager::get()->exec("DROP TABLE IF EXISTS `luna_user_info`");

        LunaUser::expireTableScheme();
    }

    public function down() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `luna_user_info` (
            `user_id` CHAR(32) NOT NULL REFERENCES `luna_users`.`user_id`,
            `status` VARCHAR(255) NULL,
            `graduation` VARCHAR(255) NULL,
            `vita` TEXT NULL,
            `qualifications` TEXT NULL,
            `notes` TEXT NULL,
            `mkdate` INT NOT NULL,
            `chdate` INT NOT NULL,
            PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC");

        $stmt = DBManager::get()->prepare("INSERT INTO `luna_user_info` VALUES (
            :uid, :status, :graduation, :vita, :quali, :notes, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");

        $data = DBManager::get()->fetchAll("SELECT `user_id`, `status`, `graduation`,
            `vita`, `qualifications`, `notes` FROM `luna_users` ORDER BY `user_id`");
        foreach ($data as $d) {
            $stmt->execute([
                'uid' => $data['user_id'],
                'status' => $data['status'],
                'graduation' => $data['graduation'],
                'notes' => $data['notes']
            ]);
        }

        DBManager::get()->execute(
            "ALTER TABLE `luna_users` DROP `status`, `graduation`, `vita`, `qualifications`, `notes`");

        LunaUser::expireTableScheme();
    }

}
