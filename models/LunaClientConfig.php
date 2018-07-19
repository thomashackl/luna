<?php
/**
 * LunaClientConfig.php
 * model class for custom client configurations.
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
 * @property string id database column
 * @property string key database column
 * @property string name database column
 * @property string type database column
 * @property string default database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaClient client belongs_to LunaClient
 */
class LunaClientUser extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_client_config';
        parent::configure($config);
    }

}
