<?php
/**
 * LunaEMail.php
 * model class for e-mail addresses.
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
 * @property string user_id database column
 * @property string id alias column for user_id
 * @property string email database column
 * @property string type database column
 * @property string default database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaUser user belongs_to LunaUser
 */
class LunaEMail extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_email';
        $config['belongs_to']['user'] = array(
            'class_name' => 'LunaUser',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
            'on_store' => 'store'
        );
        $config['alias_fields']['name'] = 'email';

        parent::configure($config);
    }

}
