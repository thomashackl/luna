<?php
/**
 * LunaPhoneNumber.php
 * model class for phone numbers.
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
 * @property string number database column
 * @property string type database column
 * @property string default database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LunaUser user belongs_to LunaUser
 */
class LunaPhoneNumber extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_phone';
        $config['belongs_to']['user'] = array(
            'class_name' => 'LunaUser',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
            'on_store' => 'store'
        );
        $config['alias_fields']['name'] = 'number';

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        $this->registerCallback('after_create before_store before_delete', 'cbLog');

        parent::__construct($id);
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
            $log->affected = array($this->user->id);
            $log->affected_type = 'phone';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = $this->number;
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
                $log->info = $this->number;
            }
            $log->store();
        }
    }

}
