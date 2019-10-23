<?php
/**
 * LunaLastContact.php
 * model class for last contacts to a company or a person.
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
 * @property string luna_object_id database column
 * @property string type database column
 * @property int date database column
 * @property string id alias column for user_id,company_id,date
 * @property string contact database column
 * @property string notes database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class LunaLastContact extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_last_contact';
        $config['belongs_to']['user'] = [
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['belongs_to']['company'] = [
            'class_name' => 'LunaCompany',
            'foreign_key' => 'luna_object_id',
            'assoc_foreign_key' => 'company_id'
        ];
        $config['belongs_to']['person'] = [
            'class_name' => 'LunaUser',
            'foreign_key' => 'luna_object_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    public function getCompany()
    {
        if ($this->type === 'company') {
            return LunaCompany::find($this->luna_object_id);
        }

        return null;
    }

    public function getPerson()
    {
        if ($this->type === 'person') {
            return LunaUser::find($this->luna_object_id);
        }

        return null;
    }

}
