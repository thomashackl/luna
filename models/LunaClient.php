<?php
/**
 * LunaClient.php
 * model class for clients.
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
 * @property string client_id database column
 * @property string id alias column for client_id
 * @property string name database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LuneClientUser beneficiaries has_and_belongs_to_many LunaClientUser
 * @property LunaUser users has_many LunaUser
 * @property LunaCompany companies has_many LunaCompany
 * @property LunaSkill skills has_many LunaSkill
 */
class LunaClient extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_clients';
        $config['has_many']['beneficiaries'] = array(
            'class_name' => 'LunaClientUser',
            'assoc_key' => 'client_id',
            'assoc_foreign_key' => 'client_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_many']['users'] = array(
            'class_name' => 'LunaUser',
            'assoc_foreign_key' => 'client_id',
            'on_delete' => 'delete'
        );
        $config['has_many']['companies'] = array(
            'class_name' => 'LunaCompany',
            'assoc_foreign_key' => 'client_id',
            'on_delete' => 'delete'
        );
        $config['has_many']['skills'] = array(
            'class_name' => 'LunaSkill',
            'assoc_foreign_key' => 'client_id',
            'on_delete' => 'delete'
        );

        parent::configure($config);
    }

    public static function getCurrentClient()
    {
        return LunaClient::find(UserConfig::get($GLOBALS['user']->id)->LUNA_CURRENT_CLIENT);
    }

    public static function setCurrentClient($client_id)
    {
        UserConfig::get($GLOBALS['user']->id)->store('LUNA_CURRENT_CLIENT', $client_id);
    }

}
