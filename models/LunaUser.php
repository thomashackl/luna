<?php
/**
 * LunaUser.php
 * model class for users.
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
 * @property string  firstname database column
 * @property string  lastname database column
 * @property string  title_front database column
 * @property string  title_rear database column
 * @property int     gender database column
 * @property string  studip_user_id database column
 * @property string  street database column
 * @property string  zip database column
 * @property string  city database column
 * @property string  country database column
 * @property string  fax database column
 * @property string  homepage database column
 * @property string  status database column
 * @property string  graduation database column
 * @property string  notes database column
 * @property string  mkdate database column
 * @property string  chdate database column
 * @property LunaSkill skills has_and_belongs_to_many LunaSkill
 * @property LunaCompany companies has_and_belongs_to_many LunaCompany
 * @property LunaTag tags has_and_belongs_to_many LunaTag
 * @property LunaEMail emails has_many LunaEMail
 * @property LunaPhoneNumber phonenumbers has_many LunaPhoneNumber
 * @property StudipDocument documents has_many StudipDocument
 */
class LunaUser extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_users';
        $config['has_and_belongs_to_many']['skills'] = array(
            'class_name' => 'LunaSkill',
            'thru_table' => 'luna_user_skills',
            'thru_key' => 'user_id',
            'thru_assoc_key' => 'skill_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY `name`'
        );
        $config['has_and_belongs_to_many']['companies'] = array(
            'class_name' => 'LunaCompany',
            'thru_table' => 'luna_user_company',
            'thru_key' => 'user_id',
            'thru_assoc_key' => 'company_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY `name`'
        );
        $config['has_and_belongs_to_many']['tags'] = array(
            'class_name' => 'LunaTag',
            'thru_table' => 'luna_user_tag',
            'thru_key' => 'user_id',
            'thru_assoc_key' => 'tag_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY `name`'
        );
        $config['has_many']['emails'] = array(
            'class_name' => 'LunaEMail',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_many']['phonenumbers'] = array(
            'class_name' => 'LunaPhoneNumber',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_many']['documents'] = array(
            'class_name' => 'StudipDocument',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'seminar_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );

        parent::configure($config);
    }

    public function getFullname($format = 'full_rev')
    {
        switch ($format) {
            case 'full':
                $name = $this->firstname . ' ' . $this->lastname;
                if ($this->title_front) {
                    $name = $this->title_front . ' ' . $name;
                }
                if ($this->title_rear) {
                    $name .= ', ' . $this->title_rear;
                }
                break;
            case 'full_rev':
            default:
                $name = $this->lastname . ', ' . $this->firstname;
                if ($this->title_front) {
                    $name .= ', ' . $this->title_front;
                }
                if ($this->title_rear) {
                    $name .= ', ' . $this->title_rear;
                }
        }
        return $name;
    }

    public function getDefaultEmail()
    {
        return $this->emails->findOneBy('default', 1)->email;
    }

    public static function getDistinctValues($client, $field, $type = 'user')
    {
        $filters = LunaUserFilter::getFilterFields();
        $column = $filters[$field]['ids'];
        $values = $filters[$field]['dbvalues'];
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT :ids AS id, :values AS value FROM `luna_users`
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
