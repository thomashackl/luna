<?php

/**
 * Define a sender address for each client.
 */

class ClientEmailSender extends Migration {

    public function up()
    {
        /*
         * Add new column to clients table for specifying a default sender address.
         */
        DBManager::get()->exec("ALTER TABLE `luna_clients` ADD `sender_address`
            VARCHAR(255) NOT NULL AFTER `name`;");
        LunaClient::expireTableScheme();
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `luna_clients` DROP `sender_address`");
        LunaClient::expireTableScheme();
    }

}
