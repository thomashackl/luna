<?php
/**
 * export.php
 *
 * All export-related actions.
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

class ExportController extends AuthenticatedController {

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
        $this->sidebar->setImage('sidebar/export-sidebar.png');

        $this->client = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));

        if (Studip\ENV == 'development') {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.min.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }
        PageLayout::addStylesheet($style);
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');
        PageLayout::addScript($js);
    }

    /**
     * Export data in CSV format.
     * @param string $type either 'persons' or 'companies'
     */
    public function csv_action($type)
    {

        switch ($type) {
            case 'persons':
                $class = 'LunaUser';
                $filterClass = 'LunaUserFilter';
                $savedDefault = 'LUNA_USER_EXPORT_FIELDS';
                $entriesName = 'users';
                $getEntriesFunction = 'getFilteredUsers';
                $redirect = 'persons';
                break;
            case 'companies':
                $class = 'LunaCompany';
                $filterClass = 'LunaCompanyFilter';
                $savedDefault = 'LUNA_COMPANY_EXPORT_FIELDS';
                $entriesName = 'companies';
                $getEntriesFunction = 'getFilteredCompanies';
                $redirect = 'companies';
                break;
        }

        $this->type = $type;

        $this->fields = $filterClass::getFilterFields(true);

        if ($GLOBALS['user']->cfg->$savedDefault) {
            $selected = studip_json_decode($GLOBALS['user']->cfg->$savedDefault);
            $this->selected = $selected[$this->client->id];
        } else {
            $this->selected = array_keys($this->fields);
        }

        if (Request::submitted('do_export')) {
            $entries = Request::optionArray($entriesName) ?
                $class::findMany(Request::optionArray($entriesName)) :
                $this->client->$getEntriesFunction(0, -1);
            $csv = [];
            $csv[] = array_map(function($entry) {
                return studip_utf8encode($entry['name']);
            }, array_intersect_key($this->fields, array_flip(Request::getArray('fields'))));
            foreach ($entries as $one) {
                $entry = [];
                foreach (Request::getArray('fields') as $field) {
                    if ($one->$field instanceof SimpleORMapCollection) {
                        $entry[] = implode("\n", studip_utf8encode($one->$field->pluck('name')));
                    } else {
                        $entry[] = studip_utf8encode($one->$field);
                    }
                }
                $csv[] = $entry;
            }
            $this->set_content_type('text/csv;charset=utf-8');

            $this->response->add_header('Content-Disposition', 'attachment; filename=' .
                Request::get('filename') . '.csv');
            $this->render_text(array_to_csv($csv));
        } else if (Request::submitted('default')) {
            $stored = $GLOBALS['user']->cfg->$savedDefault;
            $fields = $stored ? studip_json_decode($stored) : [];

            $fields[$this->client->id] = Request::getArray('fields');

            $GLOBALS['user']->cfg->store($savedDefault, studip_json_encode($fields));

            PageLayout::postSuccess(dgettext('luna', 'Die Voreinstellung für den Datenexport wurde gespeichert.'));

            $this->relocate($redirect);
        } else {
            PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Datenfelder wählen'));
        }
    }

    /**
     * Export one or more vcards for persons or companies.
     * @param string $type on of 'persons' or 'companies'
     * @param string|null $entry ID of a single entry to export
     */
    public function vcard_action($type, $entry = null)
    {
        switch ($type) {
            case 'persons':
                $class = 'LunaUser';
                $entriesName = 'bulkusers';
                $getEntriesFunction = 'getFilteredUsers';
                break;
            case 'companies':
                $class = 'LunaCompany';
                $entriesName = 'bulkcompanies';
                $getEntriesFunction = 'getFilteredCompanies';
                break;
        }

        $this->type = $type;

        if ($entry) {
            $one = $class::find($entry);
        }

        $this->entries = ($entry ?
            [$one] :
            ($this->flash[$entriesName] ?
                $class::findMany($this->flash[$entriesName]) :
                $this->client->$getEntriesFunction(0, -1)));

        $this->set_content_type('text/vcf;charset=utf-8');
        $this->set_layout(null);

        if ($entry) {
            if ($type == 'persons') {
                $filename = str_replace([' ', ',', '.'], ['-', '', ''], $one->getFullname('full_rev'));
            } else if ($type == 'companies') {
                $filename = str_replace(' ', '-', $one->name);
            }
        } else {
            if ($type == 'persons') {
                $filename = 'contacts-' . date('Y-m-d-H-i');
            } else if ($type == 'companies') {
                $filename = 'companies-' . date('Y-m-d-H-i');
            }
        }

        $this->trans = Transliterator::create('Latin-ASCII');

        $this->response->add_header('Content-Disposition', 'attachment; filename=' . $filename . '.vcf');
    }

    /**
     * Export data for usage in Word
     */
    public function persons_serialmail_action()
    {
        $this->fields = LunaUserFilter::getFilterFields(true);

        if ($GLOBALS['user']->cfg->LUNA_EXPORT_FIELDS) {
            $selected = studip_json_decode($GLOBALS['user']->cfg->LUNA_EXPORT_FIELDS);
            $this->selected = $selected[$this->client->id];
        } else {
            $this->selected = array_keys($this->fields);
        }

        $markers = LunaMarker::findBySQL("1 ORDER BY `priority`");

        $persons = $this->flash['bulkusers'] ?
            LunaUser::findMany($this->flash['bulkusers']) :
            $this->client->getFilteredUsers(0, -1);

        $csv = [];
        $csv[] = array_map(function($m) {
            return $m->name;
        }, $markers);

        foreach ($persons as $person) {
            $entry = [];
            foreach ($markers as $marker) {
                $entry[] = $marker->getMarkerReplacement($person);
            }
            $csv[] = $entry;
        }
        $this->set_content_type('text/csv;charset=windows-1252');
        $this->response->add_header('Content-Disposition', 'attachment;filename=luna-serienmail-' . date('Y-m-d-H-i') . '.csv');
        $this->render_text(array_to_csv($csv));

        /*$excel = new PHPExcel();
        $excel->getProperties()->setCreator(studip_utf8encode($GLOBALS['user']->getFullname('full')))
            ->setLastModifiedBy(studip_utf8encode($GLOBALS['user']->getFullname('full')))
            ->setCompany(studip_utf8encode($GLOBALS['UNI_NAME_CLEAN']))
            ->setTitle(dgettext('luna', 'Serienbrief'))
            ->setSubject(dgettext('luna', 'Serienbrief'));

        $column = 'A';
        $row = 1;

        // Write headers.
        foreach ($markers as $marker) {
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($column . $row, studip_utf8encode($marker->name));
            $column++;
        }

        $column = 'A';
        $row = 2;

        // Write person data.
        foreach ($persons as $person) {
            foreach ($markers as $marker) {
                $excel->setActiveSheetIndex(0)
                    ->setCellValueExplicit($column . $row,
                        studip_utf8encode($marker->getMarkerReplacement($person)));
                $column++;
            }
            $column = 'A';
            $row++;
        }

        // HTTP headers for file download.
        $this->set_layout(null);
        $this->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->response->add_header('Content-Disposition', 'attachment;filename="luna-serienmail-' . date('Y-m-d-H-i') . '.xlsx"');
        $this->response->add_header('Cache-Control', 'max-age=0');

        // If you're serving to IE over SSL, then the following may be needed
        $this->response->add_header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        $this->response->add_header('Last-Modified', gmdate('D, d M Y H:i:s').' GMT'); // always modified
        $this->response->add_header('Cache-Control', 'cache, must-revalidate'); // HTTP/1.1
        $this->response->add_header('Pragma', 'public'); // HTTP/1.0

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        $this->render_nothing();*/
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

