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

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/person-sidebar.png');

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
        PageLayout::addScript($js);

        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');

        // select2
        PageLayout::addStylesheet($this->plugin->getPluginURL().'/assets/stylesheets/select2.min.css');
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/select2.min.js');
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

        $this->allfilters = LunaUserFilter::getFilterFields(true);
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
    }

    public function load_persons_action($start = 0, $searchtext = '')
    {
        LunaUserFilter::setFilters($this->client->id, Request::getArray('filters'));

        $this->allfilters = LunaUserFilter::getFilterFields(true);
        $this->filters = LunaUserFilter::getFilters($GLOBALS['user']->id, $this->client->id);
        $this->searchtext = $searchtext;

        $config = studip_json_decode($GLOBALS['user']->cfg->LUNA_PERSON_LIST_COLUMNS);
        $this->columns = $config[$this->client->id];
        if (!$this->columns) {
            $this->columns = array('address', 'companies', 'skills');
        }

        $this->persons = $this->client->getFilteredUsers($start, 0, $this->searchtext);
        $this->personcount = $this->client->getFilteredUsersCount($this->searchtext);
        $this->entries_per_page = $this->client->getListMaxEntries('persons');
        $this->pagecount = ceil($this->personcount / $this->entries_per_page);
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
                'address zip city country fax homepage status graduation notes') as $entry) {
            if (isset($this->flash[$entry])) {
                $this->person->$entry = $this->flash[$entry];
            }
        }

        $this->skills = $this->client->skills;
        $this->companies = $this->client->companies;
        $this->tags = $this->client->tags;

        $search = new PermissionSearch(
            'user',
            '',
            'user_id',
            array(
                'permission' => array('user', 'autor', 'tutor', 'dozent'),
                'exclude_user' => []
            )
        );
        $this->usersearch = QuickSearch::get('studip_user_id', $search);

        $title = $this->person->isNew() ?
            dgettext('luna', 'Neue Person anlegen') :
            sprintf(dgettext('luna', 'Daten von %s'), $this->person->getFullname('full'));

        $this->flash->keep();

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('persons'),
            Icon::create('group2', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Personendaten') :
            dgettext('luna', 'Neue Person anlegen'),
            $this->url_for('persons/edit', $id),
            Icon::create('roles2', 'clickable'))->setActive(true);
        if ($this->person->studip_user_id && $this->person->studip_user->course_memberships) {
            $views->addLink(dgettext('luna', 'Veranstaltungen'),
                $this->url_for('persons/courses', $id),
                Icon::create('course', 'clickable'))->setActive(false);
            $views->addLink(dgettext('luna', 'Stud.IP-Profil'),
                URLHelper::getURL('dispatch.php/profile', ['username' => $this->person->studip_user->username]),
                Icon::create('profile', 'clickable'))->setActive(false);
        }
        $this->sidebar->addWidget($views);
    }

    /**
     * Show info about a person.
     *
     * @param string $id id of the person to show
     */
    public function info_action($id)
    {
        Navigation::activateItem('/tools/luna/persons');

        $this->person = LunaUser::find($id);

        $title = sprintf(dgettext('luna', 'Daten von %s'), $this->person->getFullname('full'));

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('persons'),
            Icon::create('group2', 'clickable'))->setActive(false);
        $views->addLink(dgettext('luna', 'Personendaten'),
            $this->url_for('persons/info', $id),
            Icon::create('info', 'clickable'))->setActive(true);
        if ($this->person->studip_user_id && $this->person->studip_user->course_memberships) {
            $views->addLink(dgettext('luna', 'Veranstaltungen'),
                $this->url_for('persons/courses', $id),
                Icon::create('course', 'clickable'))->setActive(false);
            $views->addLink(dgettext('luna', 'Stud.IP-Profil'),
                URLHelper::getURL('dispatch.php/profile', ['username' => $this->person->studip_user->username]),
                Icon::create('profile', 'clickable'))->setActive(false);
        }
        $this->sidebar->addWidget($views);
    }

    /**
     * Lists the Stud.IP courses this user has held as lecturer.
     *
     * @param $user_id The user to show courses for.
     */
    public function courses_action($user_id) {
        Navigation::activateItem('/tools/luna/persons');

        $this->user = LunaUser::find($user_id);

        $this->courses = [];

        if (count($this->user->studip_user->course_memberships) > 0) {
            $lecturedcourses = $this->user->studip_user->course_memberships->findBy('status', 'dozent');

            if (count($lecturedcourses) > 0) {
                $courses = Course::findBySQL(
                    "`Seminar_id` IN (?) ORDER BY `start_time`, `VeranstaltungsNummer`, `Name`",
                    array($lecturedcourses->pluck('seminar_id')));

                foreach ($courses as $course) {
                    $this->courses[$course->start_semester->description][] = $course;
                }
            }
        }

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('persons'),
            Icon::create('group2', 'clickable'))->setActive(false);
        $views->addLink(dgettext('luna', 'Personendaten'),
            $this->url_for($this->hasWriteAccess ? 'persons/edit' : 'persons/info', $user_id),
            Icon::create('info', 'clickable'))->setActive(false);
        $views->addLink(dgettext('luna', 'Veranstaltungen'),
            $this->url_for('persons/courses', $user_id),
            Icon::create('course', 'clickable'))->setActive(true);
        $views->addLink(dgettext('luna', 'Stud.IP-Profil'),
            URLHelper::getURL('dispatch.php/profile', ['username' => $this->person->studip_user->username]),
            Icon::create('profile', 'clickable'))->setActive(false);
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
            }
            $user->client_id = $this->client->client_id;
            $user->firstname = Request::get('firstname');
            $user->lastname = Request::get('lastname');
            $user->title_front = Request::get('title_front');
            $user->title_rear = Request::get('title_rear');
            $user->gender = Request::int('gender');
            $user->address = Request::get('address');
            $user->zip = Request::get('zip');
            $user->city = Request::get('city');
            $user->country = Request::get('country', 'Deutschland');
            $user->fax = Request::get('fax');
            $user->homepage = Request::get('homepage');

            $user->studip_user_id = Request::option('studip_user_id', null);

            $skills = [];
            foreach (Request::getArray('skills') as $skill) {
                $data = $this->client->skills->findOneBy('name', trim($skill));
                if (!$data) {
                    $data = new LunaSkill();
                    $data->client_id = $this->client->id;
                    $data->name = trim($skill);
                }
                if (!$data->users) {
                    $data->users = array($user);
                } else if (!$data->users->findByUser_id($user->user_id)) {
                    $data->users->append($user);
                }
                $data->store();
                $skills[] = $data;
            }
            $user->skills = SimpleORMapCollection::createFromArray($skills);

            $user->companies = new SimpleORMapCollection();
            if (Request::option('company')) {
                $user->companies->append(LunaCompany::find(Request::option('company')));
            }

            $emails = [];
            $default = false;
            $i = 0;
            foreach (Request::getArray('email') as $index => $email) {
                if (trim($email['address'])) {
                    if ($user->id) {
                        if (!$entry = LunaEMail::find(array($user->id, $email['address']))) {
                            $entry = new LunaEMail();
                        }
                    } else {
                        $entry = new LunaEMail();
                    }
                    $entry->user_id = $user->id;
                    $entry->email = trim($email['address']);
                    $entry->type = $email['type'];
                    $entry->default = count(Request::getArray('email')) == 1 ? 1 : Request::int('email-default') == $index ? 1 : 0;
                    if ($entry->default) {
                        $default = true;
                    }
                    $i++;

                    // Set first email address as default if no default is given.
                    if ($i == sizeof(Request::getArray('email')) && !$default) {
                        $emails[0]->default = true;
                    }
                    $emails[] = $entry;
                }
            }
            $user->emails = SimpleORMapCollection::createFromArray($emails);

            $phonenumbers = [];
            $default = false;
            $i = 0;
            foreach (Request::getArray('phone') as $index => $phone) {
                if (trim($phone['number'])) {
                    if ($user->id) {
                        if (!$entry = LunaPhoneNumber::find(array($user->id, $email['number']))) {
                            $entry = new LunaPhoneNumber();
                        }
                    } else {
                        $entry = new LunaPhoneNumber();
                    }
                    $entry->user_id = $user->id;
                    $entry->number = trim($phone['number']);
                    $entry->type = $phone['type'];
                    $entry->default = count(Request::getArray('phone')) == 1 ? 1 : Request::int('phone-default') == $index ? 1 : 0;
                    if ($entry->default) {
                        $default = true;
                    }
                    $i++;

                    // Set first email address as default if no default is given.
                    if ($i == sizeof(Request::getArray('phone')) && !$default) {
                        $phonenumbers[0]->default = true;
                    }
                    $phonenumbers[] = $entry;
                }
            }
            $user->phonenumbers = SimpleORMapCollection::createFromArray($phonenumbers);

            $tags = [];
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

            if (Request::getArray('userdocs')) {
                $docs = StudipDocument::findMany(Request::getArray('userdocs'));
            } else {
                $docs = [];
            }

            foreach ($_FILES['docs']['name'] as $index => $filename) {
                if ($_FILES['docs']['error'][$index] === UPLOAD_ERR_OK && in_array($filename, Request::getArray('newdocs'))) {
                    $file = $filename;
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

            $user->status = Request::get('status');
            $user->graduation = Request::get('graduation');
            $user->notes = Request::get('notes');

            if ($user->store()) {
                PageLayout::postSuccess(sprintf(
                    dgettext('luna', 'Die Personendaten von %s wurden gespeichert.'),
                    $user->getFullname('full')));
            } else {
                PageLayout::postError(sprintf(
                    dgettext('luna', 'Die Personendaten von %s konnten nicht gespeichert werden.'),
                    $user->getFullname('full')));
            }

            $companydata = Request::getArray('company');
            if ($companydata['return_to']) {
                foreach ($companydata as $key => $value) {
                    if ($key != 'return_to') {
                        $this->flash[$key] = $value;
                    }
                }
                $this->flash['contact_person'] = $user->id;
                $this->redirect($companydata['return_to']);
            } else {
                $this->relocate('persons');
            }

        } else if (Request::submitted('newcompany')) {

            $this->flash['firstname'] = Request::get('firstname');
            $this->flash['lastname'] = Request::get('lastname');
            $this->flash['title_front'] = Request::get('title_front');
            $this->flash['title_rear'] = Request::get('title_rear');
            $this->flash['gender'] = Request::int('gender');
            $this->flash['address'] = Request::get('address');
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
            $this->flash['tags'] = Request::getArray('tags');
            if (Request::option('company')) {
                $this->flash['company'] = Request::option('company');
            }
            $this->flash['status'] = Request::get('status');
            $this->flash['graduation'] = Request::get('graduation');
            $this->flash['notes'] = Request::get('notes');
            $this->flash['return_to'] = $this->url_for('persons/edit', $id ?: null);

            $this->redirect($this->url_for('companies/edit'));

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

    public function bulk_action()
    {
        $this->flash['bulkusers'] = Request::optionArray('persons');
        switch (Request::option('bulkaction')) {
            case 'message':
                $this->relocate('message/write/users');
                break;
            case 'csv':
                $this->redirect($this->url_for('export/csv', 'persons'));
                break;
            case 'serialmail':
                $this->relocate('export/persons_serialmail');
                break;
            case 'vcard':
                $this->relocate('export/vcard', 'persons');
                break;
        }
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

    public function get_status_action()
    {
        $values = DBManager::get()->fetchFirst(
            "SELECT DISTINCT `status` FROM `luna_users` WHERE `client_id` = ? AND `status` LIKE ? ORDER BY `status`",
            array($this->client->id, '%' . Request::quoted('term') . '%'));
        $this->render_text(studip_json_encode($values));
    }

    public function configure_view_action()
    {
        if (Request::submitted('store')) {
            $config = studip_json_decode($GLOBALS['user']->cfg->LUNA_PERSON_LIST_COLUMNS);
            $config[$this->client->id] = Request::getArray('fields');

            if ($GLOBALS['user']->cfg->store('LUNA_PERSON_LIST_COLUMNS', studip_json_encode($config))) {
                PageLayout::postSuccess(dgettext('luna', 'Die anzuzeigenden Daten wurden gespeichert.'));
            } else {
                PageLayout::postError(dgettext('luna', 'Die anzuzeigenden Daten konnten nicht gespeichert werden.'));
            }

            $this->relocate('persons');
        } else {
            $this->fields = LunaUserFilter::getFilterFields(true);

            foreach (words('firstname lastname address zip city country') as $entry) {
                unset($this->fields[$entry]);
            }

            $address = array('address' => array('name' => dgettext('luna', 'Adresse')));
            $this->fields = $address + $this->fields;

            $config = studip_json_decode($GLOBALS['user']->cfg->LUNA_PERSON_LIST_COLUMNS);
            $this->selected = $config[$this->client->id];
            if (!$this->selected) {
                $this->selected = array('address', 'companies', 'skills');
            }
        }
    }

    public function find_person_action()
    {
        $values = DBManager::get()->fetchAll(
            "SELECT DISTINCT `user_id`, `firstname`, `lastname`, `title_front`, `title_rear`
                FROM `luna_users`
                WHERE `client_id` = :client
                    AND (`firstname` LIKE :term
                        OR `lastname` LIKE :term
                        OR CONCAT_WS(' ', `firstname`, `lastname`) LIKE :term
                        OR CONCAT_WS(' ', `lastname`, `firstname`) LIKE :term)
                ORDER BY `lastname`, `firstname`",
            array('client' => $this->client->id, 'term' => '%' . Request::quoted('term') . '%'));
        $this->render_text(studip_json_encode($values));
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

