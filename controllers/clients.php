<?php
/**
 * clients.php
 *
 * Shows all registered clients.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class ClientsController extends AuthenticatedController {

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));
        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/admin-sidebar.png');

        $this->isRoot = $GLOBALS['perm']->have_perm('root');

        $this->currentClient = LunaClient::findCurrent();
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');
    }

    /**
     * List all available clients.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/clients');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Mandanten'));
        if (Studip\ENV == 'development') {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }
        PageLayout::addScript($js);

        if ($this->isRoot) {
            $this->clients = LunaClient::findBySQL("1 ORDER BY `name`");
        } else {
            $accessible = SimpleCollection::createFromArray(
                LunaClientUser::findByUser_id($GLOBALS['user']->id));
            if (count($accessible) > 0) {
                $this->clients = LunaClient::findMany($accessible->pluck('client_id'));
            }
        }

        if ($this->isRoot) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Mandant hinzufügen'),
                $this->url_for('clients/edit'),
                Icon::create('category+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    /**
     * Create a new or edit an existing client.
     *
     * @param string $id id of the client to edit, empty if new client
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/clients');

        if ($id) {
            $this->client = LunaClient::find($id);
        } else {
            $this->client = new LunaClient();
            $this->client->beneficiaries = new SimpleORMapCollection();
            $this->client->users = new SimpleORMapCollection();
            $this->client->companies = new SimpleORMapCollection();
            $this->client->skills = new SimpleORMapCollection();
            $this->client->config_entries = new SimpleORMapCollection();
            foreach (LunaClientConfig::findBySQL("1 ORDER BY `key`") as $one) {
                $entry = LunaClientConfigEntry::build(['key' => $one['key']]);
                $this->client->config_entries->append($entry);
            }
        }

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' .
        $id ?
            sprintf(dgettext('luna', 'Mandant %s bearbeiten'), $this->skill->name) :
            dgettext('luna', 'Neuen Mandanten anlegen'));

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('clients'),
            Icon::create('category', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Mandant bearbeiten') :
            dgettext('luna', 'Neuen Mandanten anlegen'),
            $this->url_for('admin/edit', $id),
            Icon::create('category+add', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);

        $this->flash->keep();
    }

    public function save_action($id = '')
    {
        CSRFProtection::verifyUnsafeRequest();

        if ($id) {
            $client = LunaClient::find($id);
        } else {
            $client = new LunaClient();
            $client->beneficiaries = new SimpleCollection();
            $client->users = new SimpleCollection();
            $client->companies = new SimpleCollection();
            $client->skills = new SimpleCollection();
            $client->config_entries = new SimpleCollection();
        }
        $client->name = Request::get('name');
        $client->sender_address = Request::get('sender_address');

        $entries = new SimpleCollection();
        foreach (Request::getArray('configuration') as $entry => $value) {
            $one = $client->config_entries->findOneBy('key', $entry);
            if ($one) {
                $one->value = $value;
                $entries->append($one);
            } else {
                $one = LunaClientConfigEntry::build([
                    'key' => $entry,
                    'value' => $value
                ]);
                $entries->append($one);
            }
            $client->config_entries = $entries;
        }

        if ($client->store()) {
            PageLayout::postSuccess(sprintf(
                dgettext('luna', 'Der Mandant "%s" wurde gespeichert.'),
                $client->name));
        } else {
            PageLayout::postError(sprintf(
                dgettext('luna', 'Der Mandant "%s" konnte nicht gespeichert werden.'),
                $client->name));
        }

        $this->relocate('clients');
    }

    public function delete_action($id)
    {
        $client = LunaClient::find($id);
        $name = $client->name;

        if ($client->delete()) {
            PageLayout::postSuccess(sprintf(dgettext('luna', 'Der Mandant "%s" wurde gelöscht.'), $name));
        } else {
            PageLayout::postError(sprintf(dgettext('luna', 'Der Mandant "%s" konnte nicht gelöscht werden.'), $name));
        }

        $this->relocate('clients');
    }

    public function permissions_action($id)
    {
        Navigation::activateItem('/tools/luna/clients');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Zugriffsberechtigungen'));
        if (Studip\ENV == 'development') {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }
        PageLayout::addScript($js);

        $this->client = LunaClient::find($id);

        $this->levels = LunaClientUser::getPermissionLevels();

        // Init person search
        $search = new PermissionSearch(
            'user',
            '',
            'user_id',
            [
                'permission' => ['user', 'autor', 'tutor', 'dozent', 'admin'],
                'exclude_user' => []
            ]
        );
        $this->search = QuickSearch::get('user_id', $search)
            ->fireJSFunctionOnSelect('STUDIP.Luna.addBeneficiary')
            ->render();

    }

    public function save_permissions_action($client_id)
    {
        CSRFProtection::verifyUnsafeRequest();

        $client = LunaClient::find($client_id);
        $beneficiaries = new SimpleORMapCollection();

        foreach (Request::getArray('users') as $user_id => $status) {
            $l = LunaClientUser::find([$client_id, $user_id]);
            if (!$l) {
                $l = new LunaClientUser();
                $l->client_id = $client_id;
                $l->user_id = $user_id;
            }
            $l->status = $status;
            $beneficiaries->append($l);
        }

        $client->beneficiaries = $beneficiaries;
        $client->store();

        $this->relocate('clients');
    }

    public function select_action($client_id)
    {
        LunaClient::setCurrentClient($client_id);
        $this->relocate('persons');
    }

    // customized #url_for for plugins
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
