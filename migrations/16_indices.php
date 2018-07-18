<?php

/**
 * Adds some indices for better performance.
 */

class Indices extends Migration {

    public function up() {
        DBManager::get()->exec("CREATE INDEX `client` ON `luna_companies` (`client_id`)");
        DBManager::get()->exec("CREATE INDEX `client` ON `luna_log` (`client_id`)");
        DBManager::get()->exec("CREATE INDEX `client` ON `luna_skills` (`client_id`)");
        DBManager::get()->exec("CREATE INDEX `client` ON `luna_tags` (`client_id`)");
        DBManager::get()->exec("CREATE INDEX `client` ON `luna_users` (`client_id`)");
        DBManager::get()->exec("CREATE INDEX `user` ON `luna_log` (`user_id`)");
    }

    public function down() {
        DBManager::get()->exec("DROP INDEX `client` ON `luna_companies`");
        DBManager::get()->exec("DROP INDEX `client` ON `luna_log`");
        DBManager::get()->exec("DROP INDEX `client` ON `luna_skills`");
        DBManager::get()->exec("DROP INDEX `client` ON `luna_tags`");
        DBManager::get()->exec("DROP INDEX `client` ON `luna_users`");
        DBManager::get()->exec("DROP INDEX `user` ON `luna_log`");
    }

}
