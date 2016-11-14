<?php
/**
 * persons.php
 *
 * Shows all registered persons with their data.
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

class PersonsController extends AuthenticatedController {

    protected $utf8decode_xhr = true;

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
        $this->sidebar->setImage('sidebar/person-sidebar.png');

        if (Studip\ENV == 'development') {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.css';
        } else {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.min.css';
        }
        PageLayout::addStylesheet($style);

        $this->client = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));

        if (Studip\ENV == 'development') {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }
        PageLayout::addScript($js);
    }

    /**
     * List all available persons.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/persons');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Personenübersicht'));

        if (Request::submitted('apply')) {
            LunaUserFilter::addFilter($this->client->id, Request::get('field'),
                Request::get('compare'), Request::get('value'));
        }

        $this->allfilters = LunaUserFilter::getFilterFields();
        $this->filters = LunaUserFilter::getFilters($GLOBALS['user']->id, $this->client->id);

        $this->presets = LunaUserFilter::getFilterPresets($this->client->id);

        $this->personcount = $this->client->getFilteredUsersCount();
        $this->persons = $this->client->getFilteredUsers();

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Person hinzufügen'),
                $this->url_for('persons/edit'),
                Icon::create('person+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }

        $export = new ExportWidget();
        $export->addLink(dgettext('luna', 'Excel-Export'),
            $this->url_for('persons/export_persons'),
            Icon::create('file-excel', 'clickable')
        )->asDialog('size=auto');
        $this->sidebar->addWidget($export);
    }

    public function load_data_action($start = 0)
    {
        LunaUserFilter::setFilters($this->client->id, Request::getArray('filters'));

        $this->allfilters = LunaUserFilter::getFilterFields();
        $this->filters = LunaUserFilter::getFilters($GLOBALS['user']->id, $this->client->id);

        $this->persons = $this->client->getFilteredUsers($start);
        $this->personcount = $this->client->getFilteredUsersCount();
        $this->pagecount = ceil($this->personcount / $this->client->getListMaxEntries());
        $this->activepage = $start + 1;
    }

    /**
     * Create a new or edit an existing person.
     *
     * @param string $id id of the person to edit, empty if new person
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/persons');

        if ($id) {
            $this->person = LunaUser::find($id);
            $this->pid = $id;
        } else {
            $this->person = new LunaUser();
            $this->person->info = new LunaUserInfo();
        }

        if ($this->flash['skills']) {
            $this->person->skills = SimpleORMapCollection::createFromArray(LunaSkill::findMany($this->flash['skills']));
        }

        if ($this->flash['skill']) {
            $this->person->skills->append(LunaSkill::find($this->flash['skill']));
        }

        if ($this->flash['company']) {
            $this->person->companies = new SimpleORMapCollection();
            $this->person->companies->append(LunaCompany::find($this->flash['company']));
        }

        foreach (words('firstname lastname title_front title_rear gender '.
                'street zip city country email_office email_private phone_office '.
                'phone_private phone_mobile fax homepage') as $entry) {
            if (isset($this->flash[$entry])) {
                $this->person->$entry = $this->flash[$entry];
            }
        }
        foreach (words('status graduation vita qualifications notes') as $entry) {
            if (isset($this->flash[$entry])) {
                $this->person->info->$entry = $this->flash[$entry];
            }
        }

        $this->skills = $this->client->skills;
        if ($this->skills) {
            $this->skills->orderBy('name');
        }
        $this->companies = $this->client->companies;
        if ($this->companies) {
            $this->companies->orderBy('name');
        }
        $this->tags = $this->client->tags;
        if ($this->tags) {
            $this->tags->orderBy('name');
        }

        $title = $this->person->isNew() ?
            dgettext('luna', 'Neue Person anlegen') :
            sprintf(dgettext('luna', 'Daten von %s bearbeiten'), $this->person->getFullname('full'));

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('persons'),
            Icon::create('group2', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Personendaten bearbeiten') :
            dgettext('luna', 'Neue Person anlegen'),
            $this->url_for('persons/edit', $id),
            Icon::create('roles2', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);
    }

    public function save_action($id = '')
    {
        if (Request::submitted('store')) {
            CSRFProtection::verifyUnsafeRequest();

            if ($id) {
                $user = LunaUser::find($id);
            } else {
                $user = new LunaUser();
                $user->info = new LunaUserInfo();
            }
            $user->client_id = $this->client->client_id;
            $user->firstname = Request::get('firstname');
            $user->lastname = Request::get('lastname');
            $user->title_front = Request::get('title_front');
            $user->title_rear = Request::get('title_rear');
            $user->gender = Request::int('gender');
            $user->street = Request::get('street');
            $user->zip = Request::get('zip');
            $user->city = Request::get('city');
            $user->country = Request::get('country', 'Deutschland');
            $user->email_office = Request::get('email_office');
            $user->email_private = Request::get('email_private');
            $user->phone_office = Request::get('phone_office');
            $user->phone_private = Request::get('phone_private');
            $user->phone_mobile = Request::get('phone_mobile');
            $user->fax = Request::get('fax');
            $user->homepage = Request::get('homepage');

            $user->skills = SimpleORMapCollection::createFromArray(LunaSkill::findMany(Request::getArray('skills')));

            $user->companies = new SimpleORMapCollection();
            if (Request::option('company')) {
                $user->companies->append(LunaCompany::find(Request::option('company')));
            }

            $tags = array();
            foreach (Request::getArray('tags') as $tag) {
                $data = $this->client->tags->findOneBy('name', trim($tag));
                if (!$data) {
                    $data = new LunaTag();
                    $data->client_id = $this->client->id;
                    $data->name = trim($tag);
                }
                if (!$data->users) {
                    $data->users = array($user);
                } else if (!$data->users->findByUser_id($user->user_id)) {
                    $data->users->append($user);
                }
                $data->store();
                $tags[] = $data;
            }
            $user->tags = SimpleORMapCollection::createFromArray($tags);

            $docs = array();
            foreach ($_FILES['docs']['name'] as $index => $filename) {
                if ($_FILES['docs']['error'][$index] === UPLOAD_ERR_OK && in_array($filename, Request::getArray('newdocs'))) {
                    $file = studip_utf8decode($filename);
                    $doc = StudipDocument::createWithFile($_FILES['docs']['tmp_name'][$index], array(
                        'range_id' => $this->client->id,
                        'user_id' => $GLOBALS['user']->id,
                        'name' => $file,
                        'filename' => $file,
                        'filesize' => $_FILES['docs']['size'][$index]
                    ));
                    if ($doc) {
                        $docs[] = $doc;
                    }
                }
            }
            $user->documents = SimpleORMapCollection::createFromArray($docs);

            $user->info->status = Request::get('status');
            $user->info->graduation = Request::get('graduation');
            $user->info->vita = Request::get('vita');
            $user->info->qualifications = Request::get('qualifications');
            $user->info->notes = Request::get('notes');

            if ($user->store()) {
                PageLayout::postSuccess(sprintf(
                    dgettext('luna', 'Die Personendaten von %s wurden gespeichert.'),
                    $user->getFullname('full')));
            } else {
                PageLayout::postError(sprintf(
                    dgettext('luna', 'Die Personendaten von %s konnten nicht gespeichert werden.'),
                    $user->getFullname('full')));
            }

            $this->relocate('persons');

        } else if (Request::submittedSome('newcompany', 'newskill')) {

            $this->flash['firstname'] = Request::get('firstname');
            $this->flash['lastname'] = Request::get('lastname');
            $this->flash['title_front'] = Request::get('title_front');
            $this->flash['title_rear'] = Request::get('title_rear');
            $this->flash['gender'] = Request::int('gender');
            $this->flash['street'] = Request::get('street');
            $this->flash['zip'] = Request::get('zip');
            $this->flash['city'] = Request::get('city');
            $this->flash['country'] = Request::get('country', 'Deutschland');
            $this->flash['email_office'] = Request::get('email_office');
            $this->flash['email_private'] = Request::get('email_private');
            $this->flash['phone_office'] = Request::get('phone_office');
            $this->flash['phone_private'] = Request::get('phone_private');
            $this->flash['phone_mobile'] = Request::get('phone_mobile');
            $this->flash['fax'] = Request::get('fax');
            $this->flash['homepage'] = Request::get('homepage');
            $this->flash['skills'] = Request::getArray('skills');
            if (Request::option('company')) {
                $this->flash['company'] = Request::option('company');
            }
            $this->flash['status'] = Request::get('status');
            $this->flash['graduation'] = Request::get('graduation');
            $this->flash['vita'] = Request::get('vita');
            $this->flash['qualifications'] = Request::get('qualifications');
            $this->flash['notes'] = Request::get('notes');
            $this->flash['return_to'] = $this->url_for('persons/edit', $id ?: null);

            if (Request::submitted('newcompany')) {
                $this->redirect($this->url_for('companies/edit'));
            } else if (Request::submitted('newskill')) {
                $this->redirect($this->url_for('skills/edit'));
            }

        }
    }

    public function delete_action($id)
    {
        $user = LunaUser::find($id);
        $name = $user->getFullname('full');

        if ($user->delete()) {
            PageLayout::postSuccess(sprintf(dgettext('luna', '%s wurde gelöscht.'), $name));
        } else {
            PageLayout::postError(sprintf(dgettext('luna', '%s konnte nicht gelöscht werden.'), $name));
        }

        $this->relocate('persons');
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

    public function save_filter_preset_action()
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

    /**
     * Deletes the given document which is assigned to the given person.
     * @param $person_id
     * @param $doc_id
     */
    public function delete_doc_action($person_id, $doc_id)
    {
        if ($this->hasWriteAccess) {
            $doc = StudipDocument::find($doc_id);
            $docname = $doc->name;
            if ($doc->delete()) {
                @unlink(get_upload_file_path($doc_id));
                PageLayout::postSuccess(sprintf(dgettext('luna', 'Die Datei %s wurde gelöscht.'), $docname));
            }
            $this->relocate('persons/edit', $person_id);
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * As the normal sendfile.php has several permission checks which cannot
     * be satisfied here, an extra download action is provided.
     *
     * @param $doc_id the file to download
     */
    public function download_action($doc_id)
    {
        $path = get_upload_file_path($doc_id);
        $doc = StudipDocument::find($doc_id);
        //replace bad charakters to avoid problems when saving the file
        $file_name = prepareFilename(basename($doc->filename));
        $content_type = get_mime_type($file_name);

        if (Request::int('force_download') || $content_type == "application/octet-stream") {
            $content_disposition = "attachment";
        } else {
            $content_disposition = "inline";
        }

        $filesize = @filesize($path);
        $start = $end = null;

        if ($filesize) {
            header('Accept-Ranges: bytes');
            $start = 0;
            $end = $filesize - 1;
            $length = $filesize;
            if (isset($_SERVER['HTTP_RANGE'])) {
                $c_start = $start;
                $c_end   = $end;
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                if (strpos($range, ',') !== false) {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header('Content-Range: bytes $start-$end/$filesize');
                    exit;
                }
                if ($range == '-') {
                    $c_start = $filesize - substr($range, 1);
                } else {
                    $range  = explode('-', $range);
                    $c_start = $range[0];
                    $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $filesize;
                }
                $c_end = ($c_end > $end) ? $end : $c_end;
                if ($c_start > $c_end || $c_start > $filesize - 1 || $c_end >= $filesize) {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header('Content-Range: bytes $start-$end/$filesize');
                    exit;
                }
                $start  = $c_start;
                $end    = $c_end;
                $length = $end - $start + 1;
                header('HTTP/1.1 206 Partial Content');
            }
            header('Content-Range: bytes ' . ($start-$end/$filesize));
            header('Content-Length: ' . $length);
        }

        header('Expires: Mon, 12 Dec 2001 08:00:00 GMT');
        header('Last-Modified: ' . gmdate ('D, d M Y H:i:s') . ' GMT');
        if ($_SERVER['HTTPS'] == 'on'){
            header('Pragma: public');
            header('Cache-Control: private');
        } else {
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP/1.1
        }
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Content-Type: $content_type');
        header('Content-Disposition: $content_disposition; filename="' . $file_name . '"');


        @readfile_chunked($path, $start, $end);

        $this->render_nothing();
    }

    public function export_persons_action()
    {
        $this->fields = LunaUserFilter::getFilterFields(true);

        if (UserConfig::get($GLOBALS['user']->id)->LUNA_EXPORT_FIELDS) {
            $selected = studip_json_decode(UserConfig::get($GLOBALS['user']->id)->LUNA_EXPORT_FIELDS);
            $this->selected = $selected[$this->client->id];
        } else {
            $this->selected = array_keys($this->fields);
        }

        if (Request::submitted('do_export')) {
            $persons = $this->client->getFilteredUsers();
            $csv = array();
            $csv[] = array_map(function($entry) {
                return $entry['name'];
            }, array_intersect_key($this->fields, array_flip($this->selected)));
            foreach ($persons as $person) {
                $entry = array();
                foreach (Request::getArray('fields') as $field) {
                    $entry[] = $person->$field;
                }
                $csv[] = $entry;
            }
            $this->response->add_header('Content-Type', 'text/csv');
            $this->response->add_header('Content-Disposition', 'attachment; filename=' .
                Request::get('filename') . '.csv');
            $this->render_text(array_to_csv($csv));
        } else if (Request::submitted('default')) {
            $stored = UserConfig::get($GLOBALS['user']->id)->LUNA_EXPORT_FIELDS;
            $fields = studip_json_decode($stored ? studip_json_decode($stored) : array());

            $fields[$this->client->id] = Request::getArray('fields');

            UserConfig::get($GLOBALS['user']->id)->store('LUNA_EXPORT_FIELDS', studip_json_encode($fields));

            PageLayout::postSuccess(dgettext('luna', 'Die Voreinstellung für den Datenexport wurde gespeichert.'));

            $this->relocate('persons');
        } else {
            PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Datenfelder wählen'));
        }
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

