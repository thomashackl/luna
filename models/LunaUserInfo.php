
<?php
/**
 * LunaUserInfo.php
 * model class for further user data.
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
 * @property string  user_id database column
 * @property string  id alias column for user_id
 * @property string  status database column
 * @property string  graduation database column
 * @property string  vita database column
 * @property string  qualifications database column
 * @property string  notes database column
 * @property string  mkdate database column
 * @property string  chdate database column
 * @property LunaUser user belongs_to LunaUser
 */
class LunaUserInfo extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_user_info';
        $config['belongs_to']['user'] = array(
            'class_name' => 'LunaUser',
            'foreign_key' => 'user_id'
        );

        parent::configure($config);
    }

    public static function getDistinctValues($client, $field)
    {
        $filters = LunaUserFilter::getFilterFields();
        $column = $filters[$field]['ids'];
        $values = $filters[$field]['dbvalues'];
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT :ids AS id, :values AS value FROM `luna_user_info`
                WHERE `client_id` = :client ORDER BY :values");
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':ids', $column, StudipPDO::PARAM_COLUMN);
        $stmt->bindParam(':values', $values, StudipPDO::PARAM_COLUMN);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDisplayValue($id, $field = 'name')
    {
        $method = 'findOneBy' . $field;
        return self::$method($id)->$field;
    }

}
