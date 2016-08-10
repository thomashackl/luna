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
        // Localization
        bindtextdomain('luna', realpath(__DIR__.'/locale'));

        // Plugin only available for roots or role.
        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = new Navigation($this->getDisplayName(),
                PluginEngine::getURL($this, array(), 'persons'));
            $navigation->addSubNavigation('persons',
                new Navigation(dgettext('luna', 'Personen'),
                    PluginEngine::getURL($this, array(), 'persons')));
            $navigation->addSubNavigation('companies',
                new Navigation(dgettext('luna', 'Firmen'),
                    PluginEngine::getURL($this, array(), 'companies')));
            $navigation->addSubNavigation('skills',
                new Navigation(dgettext('luna', 'Kompetenzen'),
                    PluginEngine::getURL($this, array(), 'skills')));
            Navigation::addItem('/admin/luna', $navigation);
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
        StudipAutoloader::addAutoloadPath(realpath(__DIR__.'/models'));

        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'list'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

}
