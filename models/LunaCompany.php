
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
 * @property string street database column
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
            'on_store' => 'store',
            'on_delete' => 'delete'
        );

        parent::configure($config);
    }

}
