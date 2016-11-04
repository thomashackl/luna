<?php
/**
 * LunaPlugin.class.php
 *
 * Plugin for managing persons with their addresses and skills.
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

require 'bootstrap.php';

class LunaPlugin extends StudIPPlugin implements SystemPlugin {

    public function __construct()
    {
        parent::__construct();
        StudipAutoloader::addAutoloadPath(realpath(__DIR__.'/models'));

        // Localization
        bindtextdomain('luna', realpath(__DIR__.'/locale'));

        // Plugin only available for roots or role.
        if ($GLOBALS['perm']->have_perm('root') || $clients = LunaClientUser::findByUser_id($GLOBALS['user']->id)) {
            $currentClient = LunaClient::getCurrentClient();
            $target = 'persons';

            if (!$currentClient) {
                if (count($clients) > 1 || $GLOBALS['perm']->have_perm('root')) {
                    $target = 'clients';
                } else {
                    LunaClient::setCurrentClient($clients[0]->client_id);
                }
            }

            $navigation = new Navigation($this->getDisplayName(),
                PluginEngine::getURL($this, array(), $target));

            if ($currentClient) {
                $navigation->addSubNavigation('persons',
                    new Navigation(dgettext('luna', 'Personen'),
                        PluginEngine::getURL($this, array(), 'persons')));
                $navigation->addSubNavigation('companies',
                    new Navigation(dgettext('luna', 'Firmen'),
                        PluginEngine::getURL($this, array(), 'companies')));
                $navigation->addSubNavigation('skills',
                    new Navigation(dgettext('luna', 'Kompetenzen'),
                        PluginEngine::getURL($this, array(), 'skills')));
                $navigation->addSubNavigation('tags',
                    new Navigation(dgettext('luna', 'Schlagwörter'),
                        PluginEngine::getURL($this, array(), 'tags')));
            }
            // Roots or people with more than one assigned clients see client selection.
            if ($GLOBALS['perm']->have_perm('root') || count($clients) > 1 ||
                    LunaClientUser::findByUserAndStatus($GLOBALS['user']->id, 'admin')) {
                $navigation->addSubNavigation('clients',
                    new Navigation(dgettext('luna', 'Mandanten'),
                        PluginEngine::getURL($this, array(), 'clients')));
            }
            Navigation::addItem('/tools/luna', $navigation);
        }
    }

    /**
     * Plugin name to show in navigation.
     */
    public function getDisplayName()
    {
        return dgettext('luna', 'Luna');
    }

    public function getLongDisplayName()
    {
        return dgettext('luna', 'Lehrbeauftragten- und Adressverwaltung');
    }

    public function perform($unconsumed_path)
    {

        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'list'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

}
