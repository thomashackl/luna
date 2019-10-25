<?php
/**
 * LunaCompanyContactPerson.php
 * model class for company contact persons.
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
 * @property string contact_person_id database column
 * @property string id alias column for contact_person_id
 * @property string company_id database column
 * @property string user_id database column
 * @property string function database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaCompany company belongs_to LunaCompany
 * @property LunaUser user belongs_to LunaUser
 */
class LunaCompanyContactPerson extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_company_contact_person';
        $config['belongs_to']['company'] = [
            'class_name' => 'LunaCompany',
            'foreign_key' => 'company_id',
            'assoc_foreign_key' => 'company_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name' => 'LunaUser',
            'foreign_key' => 'person_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['registered_callbacks']['after_create'][] = 'cbLog';
        $config['registered_callbacks']['before_store'][] = 'cbLog';
        $config['registered_callbacks']['before_delete'][] = 'cbLog';

        parent::configure($config);
    }

    /**
     * @param $type string type of callback
     */
    protected function cbLog($type)
    {
        if ($type == 'before_delete' || $type == 'after_create' || ($type == 'before_store' && !$this->isNew() && $this->isDirty())) {
            $log = new LunaLogEntry();
            $log->client_id = LunaClient::findCurrent()->id;
            $log->user_id = $GLOBALS['user']->id;
            $log->affected = [$this->company_id];
            $log->affected_type = 'company';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = 'Neue Kontaktperson: ' . $this->user->getFullname();
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
                $log->info = 'Kontaktperson gelöscht: ' . $this->user->getFullname();;
            }
            $log->store();
        }
    }

}
