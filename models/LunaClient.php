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

    public function __construct($id = null)
    {
        $this->registerCallback('after_create before_store before_delete', 'cbLog');

        parent::__construct($id);
    }

    public static function getCurrentClient()
    {
        return LunaClient::find($GLOBALS['user']->cfg->LUNA_CURRENT_CLIENT);
    }

    public static function setCurrentClient($client_id)
    {
        $GLOBALS['user']->cfg->store('LUNA_CURRENT_CLIENT', $client_id);
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
        $count_per_page = $this->getListMaxEntries('persons');
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

    public function getFilteredCompanies($start = 0)
    {
        $filters = LunaCompanyFilter::getFilters($GLOBALS['user']->id, $this->id);
        $all = LunaCompanyFilter::getFilterFields();
        $sql = "SELECT DISTINCT c.`company_id` FROM `luna_companies` c";
        if ($filters) {
            $tables = array();
            $where = array();
            $counter = 0;
            foreach ($filters as $filter) {
                if ($all[$filter['column']]['table'] != 'luna_companies') {
                    $counter++;
                    $alias = 't' . $counter;
                    $tables['t' . $counter] = $all[$filter['column']]['table'];
                } else {
                    $alias = 'c';
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
                $sql .= " JOIN `" . $table . "` " . $alias . " USING (`company_id`)";
            }
        }
        $sql .= " WHERE c.`client_id` = ?" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $sql .= " ORDER BY c.`name`";
        $sql .= " LIMIT ?, ?";
        $count_per_page = $this->getListMaxEntries('companies');
        $ids = DBManager::get()->fetchFirst($sql, array($this->id, $start * $count_per_page, $count_per_page));
        return SimpleORMapCollection::createFromArray(LunaCompany::findMany($ids))->orderBy('name');
    }

    public function getFilteredCompaniesCount()
    {
        $filters = LunaCompanyFilter::getFilters($GLOBALS['user']->id, $this->id);
        $all = LunaCompanyFilter::getFilterFields();
        $sql = "SELECT COUNT(DISTINCT c.`company_id`) FROM `luna_companies` c";
        if ($filters) {
            $tables = array();
            $where = array();
            $counter = 0;
            foreach ($filters as $filter) {
                if ($all[$filter['column']]['table'] != 'luna_companies') {
                    $counter++;
                    $alias = 't' . $counter;
                    $tables['t' . $counter] = $all[$filter['column']]['table'];
                } else {
                    $alias = 'c';
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
                $sql .= " JOIN `" . $table . "` " . $alias . " USING (`company_id`)";
            }
        }
        $sql .= " WHERE c.`client_id` = ?" .
            ($filters ? " AND ".implode(" AND ", $where) : "");
        $data = DBManager::get()->fetchFirst($sql, array($this->id));
        return $data[0];
    }

    public function getFilteredLogEntries($start = 0)
    {
        if ($GLOBALS['user']->cfg->LUNA_LOG_FILTER) {
            $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER)[$this->id];
        } else {
            $filters = array();
        }
        $sql = "SELECT DISTINCT `entry_id` FROM `luna_log`";
        if ($filters) {
            $where = array();
            $params = array();
            foreach ($filters as $filter => $value) {
                switch ($filter) {
                    case 'user_id':
                        $where[] = "`user_id` = :user_id";
                        $params['user_id'] = $value;
                        break;
                    case 'affected_id':
                        $where[] = "`affected_id` = :affected";
                        $params['affected'] = $value;
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
        return SimpleORMapCollection::createFromArray(LunaLogEntry::findMany($ids))->orderBy('mkdate desc');
    }

    public function getFilteredLogEntriesCount()
    {
        if ($GLOBALS['user']->cfg->LUNA_LOG_FILTER) {
            $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER)[$this->id];
        } else {
            $filters = array();
        }
        $sql = "SELECT COUNT(DISTINCT `entry_id`) FROM `luna_log`";
        if ($filters) {
            $where = array();
            $params = array();
            foreach ($filters as $filter => $value) {
                switch ($filter) {
                    case 'user_id':
                        $where[] = "`user_id` = :user_id";
                        $params['user_id'] = $value;
                        break;
                    case 'affected_id':
                        $where[] = "`affected_id` = :affected";
                        $params['affected'] = $value;
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
        $counts = $config->LUNA_ENTRIES_PER_PAGE ? studip_json_decode($config->LUNA_ENTRIES_PER_PAGE) : array();
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
