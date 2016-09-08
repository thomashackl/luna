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
    }

    /**
     * List all available persons.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/persons');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Personenübersicht'));

        $this->persons = $this->client->users;
        if ($this->persons) {
            $this->persons->orderBy('lastname firstname');
        }

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Person hinzufügen'),
                $this->url_for('persons/edit'),
                Icon::create('person+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
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

            $user->skills = SimpleORMapCollection::createFromArray(LunaSkill::findMany(Request::getArray('skills')))->orderBy('name');

            $user->companies = new SimpleORMapCollection();
            if (Request::option('company')) {
                $user->companies->append(LunaCompany::find(Request::option('company')));
            }

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

