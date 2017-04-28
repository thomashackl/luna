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
 * @property string  address database column
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
 * @property User studip_user has_one User
 */
class LunaUser extends SimpleORMap
{

    protected static function configure($config = [])
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
        $config['has_one']['studip_user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'studip_user_id',
            'assoc_foreign_key' => 'user_id'
        );

        $config['registered_callbacks']['after_create'][] = 'cbLog';
        $config['registered_callbacks']['before_store'][] = 'cbLog';
        $config['registered_callbacks']['before_delete'][] = 'cbLog';
        $config['registered_callbacks']['after_initialize'][] = 'cbHomepageAddress';
        $config['registered_callbacks']['before_store'][] = 'cbHomepageAddress';

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
            return LunaUser::find($value)->$field;
        } else {
            return LunaUser::findOneBySQL("`client_id` = :client AND `".$field."` = :value",
                array('client' => LunaClient::getCurrentClient()->id, 'value' => $value))->$field;
        }
    }

    /**
     * @param $type string type of callback
     */
    protected function cbLog($type)
    {
        if ($type == 'before_delete' || $type == 'after_create' || ($type == 'before_store' && !$this->isNew() && $this->isDirty())) {
            $log = new LunaLogEntry();
            $log->client_id = LunaClient::getCurrentClient()->id;
            $log->user_id = $GLOBALS['user']->id;
            $log->affected = array($this->id);
            $log->affected_type = 'user';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = '';
            } else if ($type == 'before_store' && !$this->isNew()) {
                $dirty = [];
                $old_entry = self::build($this->content_db);
                foreach (array_keys($this->db_fields) as $field) {
                    if ($this->isFieldDirty($field)) {
                        $dirty[] = $field . ': ' . $this->$field . ' -> ' . $old_entry->$field;
                    }
                }
                $log->action = 'update';
                $log->info = implode("\n", $dirty);
            } else if ($type == 'before_delete') {
                $log->action = 'delete';
                $log->info = $this->getFullname('full');
            }
            $log->store();
        }
    }

    /**
     * @param $type string type of callback
     */
    protected function cbHomepageAddress($type)
    {
        if ($this->homepage) {
            $this->homepage = preg_replace('/\s+/', '', trim($this->homepage));
            if (mb_substr($this->homepage, 0, 4) != 'http') {
                $this->homepage = 'http://' . $this->homepage;
            }
        }
    }

}
