<?php
/**
 * LunaCompanyLastContact.php
 * model class for last contacts to a company.
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
 * @property string company_id database column
 * @property int date database column
 * @property string id alias column for user_id,company_id,date
 * @property string contact database column
 * @property string notes database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class LunaCompanyLastContact extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_company_last_contact';
        $config['belongs_to']['user'] = [
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['belongs_to']['company'] = [
            'class_name' => 'LunaCompany',
            'foreign_key' => 'company_id',
            'assoc_foreign_key' => 'company_id'
        ];

        parent::configure($config);
    }

}
