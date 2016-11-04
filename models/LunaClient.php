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
 * @property string sender_address database column
 * @property string mkdate database column
 * @property string chdate database column
 * @property LuneClientUser beneficiaries has_and_belongs_to_many LunaClientUser
 * @property LunaUser users has_many LunaUser
 * @property LunaCompany companies has_many LunaCompany
 * @property LunaSkill skills has_many LunaSkill
 * @property LunaTags tags has_many LunaTag
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
        $config['has_many']['tags'] = array(
            'class_name' => 'LunaTag',
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

    public function getFilteredUsers($start = 0)
    {
        $filters = LunaUserFilter::getFilters($GLOBALS['user']->id, $this->id);
        $all = LunaUserFilter::getFilterFields();
        $sql = "SELECT DISTINCT u.`user_id` FROM `luna_users` u";
        if ($filters) {
            $tables = array();
            $where = array();
            $counter = 0;
            foreach ($filters as $filter) {
                if ($all[$filter['column']]['table'] != 'luna_users') {
                    $counter++;
                    $alias = 't' . $counter;
                    $tables['t' . $counter] = $all[$filter['column']]['table'];
                } else {
                    $alias = 'u';
                }
                if (in_array($filter['compare'], words('LIKE', 'NOT LIKE'))) {
                    $filter['value'] = '%' . $filter['value'] . '%';
                }
                $where[] = $alias .
                    ".`" . $all[$filter['column']]['ids'] . "`" .
                    $filter['compare'] .
                    "'" . $filter['value'] . "'";
            }
            foreach ($tables as $alias => $table) {
                $sql .= " JOIN `" . $table . "` " . $alias . " USING (`user_id`)";
            }
        }
        $sql .= " WHERE u.`client_id` = ?" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $sql .= " ORDER BY u.`lastname`, u.`firstname`";
        $sql .= " LIMIT ?, ?";
        $count_per_page = $this->getListMaxEntries();
        $ids = DBManager::get()->fetchFirst($sql, array($this->id, $start * $count_per_page, $count_per_page));
        return SimpleORMapCollection::createFromArray(LunaUser::findMany($ids))->orderBy('lastname firstname');
    }

    public function getFilteredUsersCount()
    {
        $filters = LunaUserFilter::getFilters($GLOBALS['user']->id, $this->id);
        $all = LunaUserFilter::getFilterFields();
        $sql = "SELECT COUNT(DISTINCT u.`user_id`) FROM `luna_users` u";
        if ($filters) {
            $tables = array();
            $where = array();
            $counter = 0;
            foreach ($filters as $filter) {
                if ($all[$filter['column']]['table'] != 'luna_users') {
                    $counter++;
                    $alias = 't' . $counter;
                    $tables['t' . $counter] = $all[$filter['column']]['table'];
                } else {
                    $alias = 'u';
                }
                if (in_array($filter['compare'], words('LIKE', 'NOT LIKE'))) {
                    $filter['value'] = '%' . $filter['value'] . '%';
                }
                $where[] = $alias .
                    ".`" . $all[$filter['column']]['ids'] . "`" .
                    $filter['compare'] .
                    "'" . $filter['value'] . "'";
            }
            foreach ($tables as $alias => $table) {
                $sql .= " JOIN `" . $table . "` " . $alias . " USING (`user_id`)";
            }
        }
        $sql .= " WHERE u.`client_id` = ?" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $data = DBManager::get()->fetchFirst($sql, array($this->id));
        return $data[0];
    }

    public function getListMaxEntries()
    {
        $counts = json_decode(UserConfig::get($GLOBALS['user']->id)->LUNA_SHOW_USER_COUNT);
        return $counts[$this->id] ?: 50;
    }

    public function setListMaxEntries($count)
    {
        $config = UserConfig::get($GLOBALS['user']->id);
        $counts = $config->LUNA_SHOW_USER_COUNT ? studip_json_decode($config->LUNA_SHOW_USER_COUNT) : array();
        $counts[$this->id] = $count;
        return $config->store('LUNA_SHOW_USER_COUNT', studip_json_encode($counts));
    }

}
