<?php
/**
 * search.php
 *
 * Shows all registered search presets.
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

class SearchController extends AuthenticatedController {

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/search-sidebar.png');

        $this->client = LunaClient::findCurrent();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    /**
     * List all available search presets.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/search');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Suchvorlagen'));

        $user_presets = LunaUserFilter::getFilterPresets($this->client->id);
        ksort($user_presets);
        $company_presets = LunaCompanyFilter::getFilterPresets($this->client->id);
        ksort($company_presets);

        $this->presets = array(
            'persons' => array(
                'name' => dgettext('luna', 'Personen'),
                'presets' => $user_presets,
                'allfilters' => LunaUserFilter::getFilterFields()
            ),
            'companies' => array(
                'name' => dgettext('luna', 'Unternehmen'),
                'presets' => $company_presets,
                'allfilters' => LunaCompanyFilter::getFilterFields()
            ),
        );
    }

    public function filter_preset_action($type)
    {
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Suchvorlage speichern'));

        $this->type = $type;
    }

    public function delete_preset_action($type, $name)
    {
        switch ($type) {
            case 'companies':
                $class = 'LunaCompanyFilter';
                break;
            case 'persons':
            default:
                $class = 'LunaUserFilter';
                break;
        }

        $presets = $class::getFilterPresets($this->client->id);

        unset($presets[urldecode($name)]);
        $class::saveFilterPresets($this->client->id, $presets);

        PageLayout::postSuccess(sprintf(dgettext('luna', 'Die Suchvorlage "%s" wurde gelöscht.'), urldecode($name)));

        $this->relocate('search');
    }

    public function save_preset_action($type)
    {
        CSRFProtection::verifyUnsafeRequest();

        switch ($type) {
            case 'companies':
                $class = 'LunaCompanyFilter';
                break;
            case 'persons':
            default:
                $class = 'LunaUserFilter';
                break;
        }

        if ($class::saveFilterPreset($this->client->id, Request::quoted('name'))) {
            PageLayout::postSuccess(sprintf(
                dgettext('luna', 'Die Suchvorlage %s wurde gespeichert.'),
                Request::quoted('name')));
        } else {
            PageLayout::postError(sprintf(
                dgettext('luna', 'Die Suchvorlage %s konnte nicht gespeichert werden.'),
                Request::quoted('name')));
        }
        $this->relocate($type);
    }

    public function load_preset_action($type, $name)
    {
        switch ($type) {
            case 'persons':
                $class = 'LunaUserFilter';
                break;
            case 'companies':
                $class = 'LunaCompanyFilter';
                break;
        }

        if (Request::isXhr()) {
            $name = $name;
        }

        $presets = $class::getFilterPresets($this->client->id);
        $class::setFilters($this->client->id, $presets[$name]);
        $this->allfilters = $class::getFilterFields();
        $this->filters = $presets[$name];
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
