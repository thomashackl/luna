<?php
/**
 * LunaLogEntry.php
 * model class for logging activities.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Luna
 *
 * @property string entry_id database column
 * @property string id alias column for entry_id
 * @property string client_id database column
 * @property string action database column
 * @property string user_id database column
 * @property string affected database column
 * @property string affected_type database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaClient client belongs_to LunaClient
 * @property User user belongs_to User
 */
class LunaLogEntry extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_log';
        $config['belongs_to']['client'] = array(
            'class_name' => 'LunaClient',
            'foreign_key' => 'client_id',
            'assoc_foreign_key' => 'client_id'
        );
        $config['belongs_to']['user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        );
        $config['serialized_fields']['affected'] = "JSONArrayObject";

        parent::configure($config);
    }

}
