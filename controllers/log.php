<?php
/**
 * log.php
 *
 * Logging for actions.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Luna
 */

class LogController extends AuthenticatedController {

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/log-sidebar.png');

        $this->client = LunaClient::findCurrent();

        if (Studip\ENV == 'development') {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.min.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }
        PageLayout::addStylesheet($style);
        PageLayout::addScript($js);
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.insert-at-caret.min.js');
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');
    }

    /**
     * List all log entries.
     */
    public function index_action()
    {
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Aktionsprotokoll'));
        Navigation::activateItem('/tools/luna/log');

        $this->setFilterSelectors();
    }

    public function load_log_entries_action($start = 0)
    {
        $this->log = $this->client->getFilteredLogEntries($start);
        $this->logcount = $this->client->getFilteredLogEntriesCount();
        $this->entries_per_page = $this->client->getListMaxEntries('log');
        $this->pagecount = ceil($this->logcount / $this->entries_per_page);
        $this->activepage = $start + 1;
    }

    /**
     * Set selection filters
     */
    public function set_selection_action()
    {
        $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER);

        foreach (words('user_id action affected_type affected_id') as $type) {
            $value = Request::option($type);
            if (isset($value)) {
                if ($value == 'all') {
                    unset($filters[$this->client->id][$type]);
                } else {
                    $filters[$this->client->id][$type] = $value;
                }
                if ($type == 'affected_type') {
                    unset($filters[$this->client->id]['affected_id']);
                }
            }
        }

        $GLOBALS['user']->cfg->store('LUNA_LOG_FILTER', studip_json_encode($filters));

        $this->relocate('log');
    }

    /**
     * Adds filter selectors to the sidebar
     */
    private function setFilterSelectors()
    {
        if ($GLOBALS['user']->cfg->LUNA_LOG_FILTER) {
            $filters = studip_json_decode($GLOBALS['user']->cfg->LUNA_LOG_FILTER)[$this->client->id];
        } else {
            $filters = [];
        }

        $sidebar = Sidebar::get();

        $list = new SelectWidget(dgettext('luna', 'Wer hat die Aktion ausgeführt?'),
            $this->url_for('log/set_selection'), 'user_id');
        $list->addElement(new SelectElement('all', dgettext('luna', 'alle')), 'user_id-all');

        $users = User::findMany(
            DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` FROM `luna_log` WHERE `client_id` = ?",
                    [$this->client->id] ), "ORDER BY `nachname`, `vorname`");
        foreach ($users as $user) {
            $list->addElement(
                new SelectElement($user->id, $user->getFullname('full'), $filters['user_id'] == $user->id),
                'user_id-' . $user->id);
        }
        $sidebar->addWidget($list, 'filter_user_id');

        $list = new SelectWidget(dgettext('luna', 'Art der Aktion'),
            $this->url_for('log/set_selection'), 'action');
        $list->addElement(new SelectElement('all', dgettext('luna', 'alle')), 'action-all');
        $list->addElement(new SelectElement('create', dgettext('luna', 'Anlegen'), $filters['action'] == 'create'),
            'action-create');
        $list->addElement(new SelectElement('update', dgettext('luna', 'Daten ändern'), $filters['action'] == 'update'),
            'action-update');
        $list->addElement(new SelectElement('delete', dgettext('luna', 'Löschen'), $filters['action'] == 'delete'),
            'action-delete');
        $list->addElement(new SelectElement('mail', dgettext('luna', 'E-Mail schreiben'), $filters['action'] == 'mail'),
            'action-mail');
        $sidebar->addWidget($list, 'filter_action');

        $list = new SelectWidget(dgettext('luna', 'Worauf wurde die Aktion angewendet?'),
            $this->url_for('log/set_selection'), 'affected_type');
        $list->addElement(new SelectElement('all', dgettext('luna', 'alle')), 'affected_type-all');
        $list->addElement(new SelectElement('user', dgettext('luna', 'Person'), $filters['affected_type'] == 'user'),
            'affected_type-user');
        $list->addElement(new SelectElement('company', dgettext('luna', 'Unternehmen'), $filters['affected_type'] == 'company'),
            'affected_type-company');
        $list->addElement(new SelectElement('client', dgettext('luna', 'Mandant'), $filters['affected_type'] == 'client'),
            'affected_type-client');
        $sidebar->addWidget($list, 'filter_affected_type');

        if ($filters['affected_type']) {
            switch ($filters['affected_type']) {
                case 'user':
                    $class = 'LunaUser';
                    $title = dgettext('luna', 'Person');
                    $order = 'ORDER BY `lastname`, `firstname`';
                    $name = 'getFullname';
                    $param = 'full';
                    break;
                case 'email':
                case 'phone':
                    $class = 'LunaUser';
                    $title = dgettext('luna', 'Person');
                    $order = 'ORDER BY `lastname`, `firstname`';
                    $name = 'getFullname';
                    $param = 'full';
                    break;
                case 'company':
                    $class = 'LunaCompany';
                    $title = dgettext('luna', 'Unternehmen');
                    $order = 'ORDER BY `name`';
                    $name = 'name';
                    break;
                case 'client':
                    $class = 'LunaClient';
                    $title = dgettext('luna', 'Mandant');
                    $order = 'ORDER BY `name`';
                    $name = 'name';
                    break;
            }
            $affected = DBManager::get()->fetchFirst(
                "SELECT DISTINCT `affected` FROM `luna_log` WHERE `client_id` = ? AND `affected_type` = ?",
                [$this->client->id, $filters['affected_type']]);
            $ids = [];
            foreach ($affected as $a) {
                $ids = array_merge($ids, studip_json_decode($a));
            }
            $data = $class::findMany($ids, $order);

            $list = new SelectWidget(dgettext('luna', $title), $this->url_for('log/set_selection'), 'affected_id');
            $list->addElement(new SelectElement('all', dgettext('luna', 'alle')), 'affected_id-all');
            foreach ($data as $entry) {
                if (method_exists($entry, $name)) {
                    $text = $entry->$name($param);
                } else {
                    $text = $entry->$name;
                }
                $list->addElement(new SelectElement($entry->id, $text, $filters['affected_id'] == $entry->id),
                    'affected_id-' . $entry->id);
            }
            $sidebar->addWidget($list, 'filter_affected_id');
        }

    }

    /**
     * customized #url_for for plugins
     */
    public function url_for($to = '')
    {
        $args = func_get_args();

        // find params
        $params = [];
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        // urlencode all but the first argument
        $args = array_map("urlencode", $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->plugin, $params, join("/", $args));
    }

}
