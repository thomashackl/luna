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
        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException(dgettext('luna',
                'Sie haben nicht die nötigen Rechte, um auf diese Funktion zuzugreifen!'));
        }

        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        // Check for AJAX.
        if (Request::isXhr()) {
            $this->set_layout(null);
            $this->set_content_type('text/html;charset=windows-1252');
            $request = Request::getInstance();
            foreach ($request as $key => $value) {
                $request[$key] = studip_utf8decode($value);
            }
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        }
        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/admin-sidebar.png');

        $this->currentClient = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->currentClient->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
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

        $this->clients = LunaClient::findBySQL("1 ORDER BY `name`");

        $actions = new ActionsWidget();
        $actions->addLink(dgettext('luna', 'Mandant hinzufügen'),
            $this->url_for('clients/edit'),
            Icon::create('category+add', 'clickable'))->asDialog('size=auto');
        $this->sidebar->addWidget($actions);
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
            $client->beneficiaries = new SimpleORMapCollection();
            $client->users = new SimpleORMapCollection();
            $client->companies = new SimpleORMapCollection();
            $client->skills = new SimpleORMapCollection();
        }
        $client->name = Request::get('name');

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
            array(
                'permission' => array('user', 'autor', 'tutor', 'dozent', 'admin'),
                'exclude_user' => array()
            )
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
            $l = LunaClientUser::find(array($client_id, $user_id));
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
    public function url_for($to)
    {
        $args = func_get_args();

        // find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        // urlencode all but the first argument
        $args = array_map("urlencode", $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->plugin, $params, join("/", $args));
    }

}
