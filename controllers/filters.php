<?php
/**
 * filters.php
 *
 * Handles all filter related stuff.
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

class FiltersController extends AuthenticatedController {

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

        $this->client = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    public function get_filternames_action($type)
    {
        switch ($type) {
            case 'persons':
                $class = 'LunaUserFilter';
                break;
            case 'companies':
                $class = 'LunaCompanyFilter';
                break;
        }
        $this->render_text(studip_json_encode($class::getFilterNames()));
    }

    public function get_filterdata_action($type)
    {
        switch ($type) {
            case 'persons':
                $class = 'LunaUserFilter';
                break;
            case 'companies':
                $class = 'LunaCompanyFilter';
                break;
        }
        $this->render_text(studip_json_encode($class::getFilterValues($this->client->id, Request::get('field'))));
    }

    public function set_entries_per_page_action()
    {
        $type = Request::option('type');
        $count = Request::int('count');
        $this->client->setListMaxEntries($type, $count);
        $this->render_text(studip_json_encode(array('OK')));
    }

    // customized #url_for for plugins
    public function url_for($to)
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
