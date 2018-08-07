<?php
/**
 * LunaClientUser.php
 * model class for users that have access to a LunaClient.
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
 * @property string client_id database column
 * @property string user_id database column
 * @property string id alias column for client_id, user_id
 * @property string status database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaClient client belongs_to LunaClient
 * @property User user belongs_to User
 */
class LunaClientUser extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_client_user';
        $config['belongs_to']['client'] = [
            'class_name' => 'LunaClient',
            'foreign_key' => 'client_id',
            'assoc_foreign_key' => 'client_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    public static function findByClientAndStatus($client_id, $status)
    {
        return self::findBySQL("`client_id` = ? AND `status` IN (?)",
            [$client_id, is_array($status) ? $status : words($status)]);
    }

    public static function findByUserAndStatus($user_id, $status)
    {
        return self::findBySQL("`user_id` = ? AND `status` IN (?)",
            [$user_id, is_array($status) ? $status : words($status)]);
    }

    public static function getPermissionLevels()
    {
        return [
            [
                'name' => dgettext('luna', 'Lesezugriff'),
                'value' => 'read'
            ],
            [
                'name' => dgettext('luna', 'Schreibzugriff'),
                'value' => 'write'
            ],
            [
                'name' => dgettext('luna', 'Administration'),
                'value' => 'admin'
            ]
        ];
    }

}
