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
        $this->sidebar->setImage('sidebar/search-sidebar.png');

        $this->client = LunaClient::getCurrentClient();
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

        $this->presets = LunaUserFilter::getFilterPresets($this->client->id);
        $this->allfilters = LunaUserFilter::getFilterFields();
        ksort($this->presets);
    }

    public function delete_action($name)
    {
        $presets = LunaUserFilter::getFilterPresets($this->client->id);

        unset($presets[$name]);
        LunaUserFilter::saveFilterPresets($this->client->id, $presets);

        PageLayout::postSuccess(sprintf(dgettext('luna', 'Die Suchvorlage "%s" wurde gelöscht.'), $name));

        $this->relocate('search');
    }

    public function save_action()
    {
        CSRFProtection::verifyUnsafeRequest();
        if (LunaUserFilter::saveFilterPreset($this->client->id, Request::quoted('name'))) {
            PageLayout::postSuccess(sprintf(
                dgettext('luna', 'Die Suchvorlage %s wurde gespeichert.'),
                Request::quoted('name')));
        } else {
            PageLayout::postError(sprintf(
                dgettext('luna', 'Die Suchvorlage %s konnte nicht gespeichert werden.'),
                Request::quoted('name')));
        }
        $this->relocate('persons');
    }

    public function load_preset_action($name)
    {
        $presets = LunaUserFilter::getFilterPresets($this->client->id);
        LunaUserFilter::setFilters($this->client->id, $presets[$name]);
        $this->allfilters = LunaUserFilter::getFilterFields();
        $this->filters = $presets[$name];
    }

    public function get_filternames_action()
    {
        $this->render_text(studip_json_encode(LunaUserFilter::getFilterNames()));
    }

    public function get_filterdata_action()
    {
        $this->render_text(studip_json_encode(LunaUserFilter::getFilterValues($this->client->id, Request::get('field'))));
    }

    public function filter_preset_action()
    {
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Suchvorlage speichern'));
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
