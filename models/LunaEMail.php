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

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_email';
        $config['belongs_to']['user'] = [
            'class_name' => 'LunaUser',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
            'on_store' => 'store'
        ];
        $config['alias_fields']['name'] = 'email';
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
            $log->affected = [$this->user->id];
            $log->affected_type = 'email';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = $this->email;
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
                $log->info = $this->email;
            }
            $log->store();
        }
    }

}
