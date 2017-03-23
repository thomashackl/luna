<?php
/**
 * LunaCompany.php
 * model class for companies.
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
 * @property string company_id database column
 * @property string id alias column for user_id
 * @property string name database column
 * @property string address database column
 * @property string zip database column
 * @property string city database column
 * @property string country database column
 * @property string email database column
 * @property string phone database column
 * @property string fax database column
 * @property string homepage database column
 * @property string contact_person database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaUser members has_many LunaUser
 * @property LunaUser contact has_one LunaUser
 * @property LunaTag tags has_and_belongs_to_many LunaTag
 */
class LunaCompany extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_companies';
        $config['has_and_belongs_to_many']['members'] = array(
            'class_name' => 'LunaUser',
            'thru_table' => 'luna_user_company',
            'thru_key' => 'company_id',
            'thru_assoc_key' => 'user_id',
            'order_by' => 'ORDER BY `lastname`, `firstname`',
            'on_store' => 'store',
            'on_delete' => 'delete'
        );
        $config['has_one']['contact'] = array(
            'class_name' => 'LunaUser',
            'foreign_key' => 'contact_person',
            'assoc_foreign_key' => 'user_id',
            'on_store' => 'store',
            'on_delete' => 'delete'
        );
        $config['has_and_belongs_to_many']['tags'] = array(
            'class_name' => 'LunaTag',
            'thru_table' => 'luna_company_tag',
            'thru_key' => 'company_id',
            'thru_assoc_key' => 'tag_id',
            'order_by' => 'ORDER BY `name`',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        $this->registerCallback('after_create before_store before_delete', 'cbLog');

        parent::__construct($id);
    }

    public static function getDistinctValues($client, $field, $type = 'company')
    {
        if ($type == 'user') {
            $filters = LunaUserFilter::getFilterFields();
        } else {
            $filters = LunaCompanyFilter::getFilterFields();
        }
        $column = $filters[$field]['ids'];
        $values = $filters[$field]['dbvalues'];
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT :ids AS id, :values AS value FROM `luna_companies`
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
            return self::find($value)->$field;
        } else {
            $company = self::findOneBySQL("`client_id` = :client AND `".$field."` = :value",
                array('client' => LunaClient::getCurrentClient()->id, 'value' => $value));
            return $company->$field;
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
            $log->affected_type = 'company';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = '';
            } else if ($type == 'before_store' && !$this->isNew()) {
                $dirty = array();
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
                $log->info = $this->name;
            }
            $log->store();
        }
    }

}
