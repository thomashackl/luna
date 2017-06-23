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

    protected static function configure($config = [])
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
            'order_by' => 'ORDER BY `lastname`, `firstname`',
            'on_delete' => 'delete'
        );
        $config['has_many']['companies'] = array(
            'class_name' => 'LunaCompany',
            'assoc_foreign_key' => 'client_id',
            'order_by' => 'ORDER BY `name`',
            'on_delete' => 'delete'
        );
        $config['has_many']['skills'] = array(
            'class_name' => 'LunaSkill',
            'assoc_foreign_key' => 'client_id',
            'order_by' => 'ORDER BY `name`',
            'on_delete' => 'delete'
        );
        $config['has_many']['tags'] = array(
            'class_name' => 'LunaTag',
            'assoc_foreign_key' => 'client_id',
            'order_by' => 'ORDER BY `name`',
            'on_delete' => 'delete'
        );

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        $this->registerCallback('after_create before_store before_delete', 'cbLog');

        parent::__construct($id);
    }

    public static function getCurrentClient()
    {
        $client = LunaClient::find($GLOBALS['user']->cfg->LUNA_CURRENT_CLIENT);

        if (!$client) {
            $clients = LunaClientUser::findByUser_id($GLOBALS['user']->id);
            if (count($clients) == 1) {
                LunaClient::setCurrentClient($clients[0]->client_id);
                $client = LunaClient::find($clients[0]->client_id);
            }
        }

        return $client;
    }

    public static function setCurrentClient($client_id)
    {
        $GLOBALS['user']->cfg->store('LUNA_CURRENT_CLIENT', $client_id);
    }

    public function getFilteredUsers($start = 0, $limit = 0, $searchtext = '')
    {
        return SimpleORMapCollection::createFromArray(
            LunaUser::findMany(
                $this->getFilteredEntries('persons', $start, $limit, $searchtext)))
            ->orderBy('lastname firstname');
    }

    public function getFilteredUsersCount($searchtext = '')
    {
        return $this->getFilteredEntries('persons', 0, 0, $searchtext, true);
    }

    public function getFilteredCompanies($start = 0, $limit = 0, $searchtext = '')
    {
        return SimpleORMapCollection::createFromArray(
            LunaCompany::findMany(
                $this->getFilteredEntries('companies', $start, $limit, $searchtext)))
            ->orderBy('name');
    }

    public function getFilteredCompaniesCount($searchtext = '')
    {
        return $this->getFilteredEntries('companies', 0, 0, $searchtext, true);
    }

    public function getFilteredLogEntries($start = 0)
    {
        if ($GLOBALS['user']->cfg->LUNA_LOG_FILTER) {
            $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER)[$this->id];
        } else {
            $filters = [];
        }
        $sql = "SELECT DISTINCT `entry_id` FROM `luna_log`";
        if ($filters) {
            $where = [];
            $params = [];
            foreach ($filters as $filter => $value) {
                switch ($filter) {
                    case 'user_id':
                        $where[] = "`user_id` = :user_id";
                        $params['user_id'] = $value;
                        break;
                    case 'affected_id':
                        $where[] = "`affected` LIKE :affected";
                        $params['affected'] = '%' . $value . '%';
                        break;
                    case 'affected_type':
                        $where[] = "`affected_type` = :atype";
                        $params['atype'] = $value;
                        break;
                    case 'action':
                        $where[] = "`action` = :action";
                        $params['action'] = $value;
                        break;
                }
            }
        }
        $sql .= " WHERE `client_id` = :client" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $params['client'] = $this->id;
        $sql .= " ORDER BY `mkdate` DESC";
        $sql .= " LIMIT :count OFFSET :start";
        $count_per_page = $this->getListMaxEntries('log');
        $params['start'] = $start * $count_per_page;
        $params['count'] = $count_per_page;
        $ids = DBManager::get()->fetchFirst($sql, $params);
        return SimpleCollection::createFromArray(LunaLogEntry::findMany($ids));
    }

    public function getFilteredLogEntriesCount()
    {
        if ($GLOBALS['user']->cfg->LUNA_LOG_FILTER) {
            $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER)[$this->id];
        } else {
            $filters = [];
        }
        $sql = "SELECT COUNT(DISTINCT `entry_id`) FROM `luna_log`";
        if ($filters) {
            $where = [];
            $params = [];
            foreach ($filters as $filter => $value) {
                switch ($filter) {
                    case 'user_id':
                        $where[] = "`user_id` = :user_id";
                        $params['user_id'] = $value;
                        break;
                    case 'affected_id':
                        $where[] = "`affected` LIKE :affected";
                        $params['affected'] = '%' . $value . '%';
                        break;
                    case 'affected_type':
                        $where[] = "`affected_type` = :atype";
                        $params['atype'] = $value;
                        break;
                    case 'action':
                        $where[] = "`action` = :action";
                        $params['action'] = $value;
                        break;
                }
            }
        }
        $sql .= " WHERE `client_id` = :client" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $params['client'] = $this->id;
        $data = DBManager::get()->fetchFirst($sql, $params);
        return $data[0];
    }

    public function getListMaxEntries($type)
    {
        $counts = studip_json_decode($GLOBALS['user']->cfg->LUNA_ENTRIES_PER_PAGE);
        return $counts[$this->id][$type] ?: 25;
    }

    public function setListMaxEntries($type, $count)
    {
        $config = $GLOBALS['user']->cfg;
        $counts = $config->LUNA_ENTRIES_PER_PAGE ? studip_json_decode($config->LUNA_ENTRIES_PER_PAGE) : [];
        $counts[$this->id][$type] = $count;
        return $config->store('LUNA_ENTRIES_PER_PAGE', studip_json_encode($counts));
    }

    public function hasReadAccess($user_id) {
        $access = false;
        if ($GLOBALS['perm']->have_perm('root')) {
            $access = true;
        } else if (count($this->beneficiaries) > 0) {
            if ($entry = $this->beneficiaries->findOneBy('user_id', $user_id)) {
                $access = true;
            }
        }
        return $access;
    }

    public function hasWriteAccess($user_id) {
        $access = false;
        if ($GLOBALS['perm']->have_perm('root')) {
            $access = true;
        } else if (count($this->beneficiaries) > 0) {
            $entry = $this->beneficiaries->findOneBy('user_id', $user_id);
            if ($entry && in_array($entry->status, words('write admin'))) {
                $access = true;
            }
        }
        return $access;
    }

    public function isAdmin($user_id) {
        $access = false;
        if ($GLOBALS['perm']->have_perm('root')) {
            $access = true;
        } else if (count($this->beneficiaries) > 0) {
            $entry = $this->beneficiaries->findOneBy('user_id', $user_id);
            if ($entry && $entry->status == 'admin') {
                $access = true;
            }
        }
        return $access;
    }

    /**
     * @param $type string type of callback
     */
    protected function cbLog($type)
    {
        if ($type == 'before_delete' || $type == 'after_create' || ($type == 'before_store' && !$this->isNew() && $this->isDirty())) {
            $log = new LunaLogEntry();
            $log->client_id = '';
            $log->user_id = $GLOBALS['user']->id;
            $log->affected = array($this->id);
            $log->affected_type = 'client';
            if ($type == 'after_create') {
                $log->action = 'create';
                $log->info = $this->name;
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
                $log->info = $this->name;
            }
            $log->store();
        }
    }

    protected function getFilteredEntries($type, $start, $limit, $searchtext = '', $justcount = false)
    {
        switch ($type) {
            case 'companies':
                $tablename = 'luna_companies';
                $joinfield = 'company_id';
                $order = 't.`name`';
                $filterClass = 'LunaCompanyFilter';
                break;
            case 'persons':
            default:
                $tablename = 'luna_users';
                $joinfield = 'user_id';
                $order = 't.`lastname`, t.`firstname`';
                $filterClass = 'LunaUserFilter';
                break;
        }

        $filters = $filterClass::getFilters($GLOBALS['user']->id, $this->id);
        $all = $filterClass::getFilterFields();
        if ($justcount) {
            $sql = "SELECT COUNT(DISTINCT t.`" . $joinfield . "`) FROM `" . $tablename . "` t";
        } else {
            $sql = "SELECT DISTINCT t.`" . $joinfield . "` FROM `" . $tablename . "` t";
        }
        $where = [];
        $tables = [];
        $counter = 0;

        if ($filters) {
            foreach ($filters as $filter) {
                if ($all[$filter['column']]['table'] != $tablename) {
                    $counter++;
                    $alias = 't' . $counter;
                    $tables['t' . $counter] = $all[$filter['column']]['table'];
                } else {
                    $alias = 't';
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
                $sql .= " JOIN `" . $table . "` " . $alias . " USING (`" . $joinfield . "`)";
            }
        }

        $tables2 = [];
        $where2 = [];
        $used = [];
        if ($searchtext) {
            foreach (str_getcsv($searchtext, ' ') as $word) {
                $subwhere = [];
                foreach ($all as $key => $filter) {
                    if ($filter['table'] != $tablename) {
                        if (!$used[$filter['table']]) {
                            $counter++;
                            $alias = 't' . $counter;
                            $tables['t' . $counter] = $filter['table'];
                            $used[$filter['table']] = $alias;
                        } else {
                            $alias = $used[$filter['table']];
                        }
                    } else {
                        $alias = 't';
                    }

                    if ($filter['linked']) {
                        if (!$used[$filter['linked']]) {
                            $counter++;
                            $alias = 't' . $counter;
                            $tables2['t' . $counter] = array('table' => $filter['linked'], 'join' => $filter['ids']);
                            $used[$filter['linked']] = $alias;
                        } else {
                            $alias = $used[$filter['linked']];
                        }
                        $subwhere[] = $alias . ".`" . $filter['dbvalues'] . "` LIKE '%" . $word . "%'";
                    } else {
                        $subwhere[] = $alias . ".`" . $filter['dbvalues'] . "` LIKE '%" . $word . "%'";
                    }
                }
                $where2[] = $subwhere;
            }

            foreach ($tables as $alias => $table) {
                $sql .= " LEFT JOIN `" . $table . "` " . $alias . " USING (`user_id`)";
            }
            foreach ($tables2 as $alias => $table) {
                $sql .= " LEFT JOIN `" . $table['table'] . "` " . $alias . " USING (`" . $table['join'] . "`)";
            }
        }

        $sql .= " WHERE t.`client_id` = ?" .
            ($where ? " AND (".implode(" AND ", $where) . ")" : "");

        if ($where2) {
            foreach ($where2 as $sub) {
                $sql .= " AND (" . implode(" OR ", $sub) . ")";
            }
        }

        $sql .= " ORDER BY " . $order;

        if ($justcount) {
            $data = DBManager::get()->fetchFirst($sql, array($this->id));
            return $data[0];
        } else {
            if ($start == 0 && $limit == -1) {
                $count_per_page = $limit;
                $data = DBManager::get()->fetchFirst($sql, array($this->id));
            } else {
                $sql .= " LIMIT ?, ?";
                $count_per_page = $this->getListMaxEntries($type);
                $data = DBManager::get()->fetchFirst($sql, array($this->id, $start * $count_per_page, $count_per_page));
            }
            return $data;
        }
    }

}
