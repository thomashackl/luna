<?php
/**
 * LunaSkill.php
 * model class for skills.
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
 * @property string skill_id database column
 * @property string id alias column for skill_id
 * @property string name database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaUser users has_and_belongs_to_many LunaUser
 */
class LunaSkill extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_skills';
        $config['has_and_belongs_to_many']['users'] = array(
            'class_name' => 'LunaUser',
            'thru_table' => 'luna_user_skills',
            'thru_key' => 'skill_id',
            'thru_assoc_key' => 'user_id',
            'on_store' => 'store'
        );

        parent::configure($config);
    }

    public static function getDistinctValues($client, $field, $type = 'user')
    {
        $filters = LunaUserFilter::getFilterFields();
        $column = $filters[$field]['ids'];
        $values = $filters[$field]['dbvalues'];
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT :ids AS id, :values AS value FROM `luna_skills`
                WHERE `client_id` = :client ORDER BY :values");
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':ids', $column, StudipPDO::PARAM_COLUMN);
        $stmt->bindParam(':values', $values, StudipPDO::PARAM_COLUMN);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDisplayValue($value, $field = 'name', $is_id = false)
    {
        if ($is_id) {
            $method = 'find';
        } else {
            $method = 'findOneBy' . $field;
        }
        return self::$method($value)->$field;
    }

}
