<?php
/**
 * companies.php
 *
 * Shows all registered companies with their data.
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

class CompaniesController extends AuthenticatedController {

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/institute-sidebar.png');

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

        $this->client = LunaClient::findCurrent();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, ['admin', 'write']);
    }

    /**
     * List all available persons.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/companies');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Unternehmensübersicht'));

        if (Request::submitted('apply')) {
            LunaCompanyFilter::addFilter($this->client->id, Request::get('field'),
                Request::get('compare'), Request::get('value'));
        }

        $this->allfilters = LunaCompanyFilter::getFilterFields(true);
        $this->filters = LunaCompanyFilter::getFilters($GLOBALS['user']->id, $this->client->id);

        $this->presets = LunaCompanyFilter::getFilterPresets($this->client->id);

        $this->companycount = $this->client->getFilteredCompaniesCount();
        $this->companies = $this->client->getFilteredCompanies();

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Unternehmen hinzufügen'),
                $this->url_for('companies/edit'),
                Icon::create('vcard+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    /**
     * AJAX endpoint for loading companies.
     *
     * @param int $start start from entry $start
     * @param string $searchtext filter by given search text
     */
    public function load_companies_action($start = 0, $searchtext = '')
    {
        $filterSettings = [
            'disjunction' => Request::int('disjunction', 0),
            'filters' => Request::getArray('filters')
        ];
        LunaCompanyFilter::setFilters($this->client->id, $filterSettings);

        $this->allfilters = LunaCompanyFilter::getFilterFields(true);
        $this->filters = $filterSettings;
        $this->searchtext = $searchtext;

        $this->companies = $this->client->getFilteredCompanies($start, 0, $this->searchtext);
        $this->companycount = $this->client->getFilteredCompaniesCount($this->searchtext);
        $this->entries_per_page = $this->client->getListMaxEntries('companies');
        $this->pagecount = ceil($this->companycount / $this->entries_per_page);
        $this->activepage = (int) $start + 1;
    }

    /**
     * Show info about a company.
     *
     * @param string $id id of the company to show
     */
    public function info_action($id)
    {
        Navigation::activateItem('/tools/luna/companies');

        $this->company = LunaCompany::find($id);

        $title = sprintf(dgettext('luna', 'Daten des Unternehmens %s'), $this->company->name);

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('companies'),
            Icon::create('vcard', 'clickable'))->setActive(false);
        $views->addLink(dgettext('luna', 'Unternehmensdaten'),
            $this->url_for('companies/info', $id),
            Icon::create('info', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);

        $f = new LunaFolder(Folder::findOneByRange_id($this->company->id));
        $this->documents = $f->getFiles();
    }

    /**
     * List company members.
     *
     * @param string $id id of the company
     */
    public function members_action($id)
    {
        $this->company = LunaCompany::find($id);
    }

    /**
     * Remove a member from a company.
     *
     * @param string $company id of the company
     * @param string $user id of the member
     */
    public function delete_member_action($company, $user)
    {
        DBManager::get()->execute("DELETE FROM `luna_user_company` WHERE `company_id` = ? AND `user_id` = ?",
            [$company, $user]);
        $this->render_nothing();
    }

    /**
     * Create a new or edit an existing person.
     *
     * @param string $id id of the company to edit, empty if new company
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/companies');

        if ($id) {
            $this->company = LunaCompany::find($id);
        } else {
            $this->company = new LunaCompany();
        }

        $metadata = $this->company->getTableMetadata();
        foreach ($metadata['fields'] as $name => $field) {
            if (isset($this->flash[$name])) {
                $this->company->$name = $this->flash[$name];
            }
        }
        foreach ($metadata['relations'] as $relation) {
            if (isset($this->flash[$relation])) {
                if ($this->company->$relation == null) {
                    $this->company->$relation = new SimpleCollection();
                }
                switch ($relation) {
                    case 'contact_persons':
                        foreach ($this->flash['contact_persons'] as $one) {
                            if ($one['id']) {
                                $contact = LunaCompanyContactPerson::find($one['id']);
                            } else {
                                $contact = new LunaCompanyContactPerson();
                                $contact->person_id = $one['person_id'];
                            }
                            $contact->function = $one['function'];
                            $this->company->contact_persons->append($contact);
                        }
                        break;
                    case 'skills':
                        foreach ($this->flash['skills'] as $one) {
                            $skill = new LunaSkill();
                            $skill->name = $one;
                            $this->company->skills->append($skill);
                        }
                        break;
                    case 'tags':
                        foreach ($this->flash['tags'] as $one) {
                            $tag = new LunaTag();
                            $tag->name = $one;
                            $this->company->tags->append($tag);
                        }
                        break;
                }
            }
        }

        $title = $this->company->isNew() ?
            dgettext('luna', 'Neues Unternehmen anlegen') :
            sprintf(dgettext('luna', 'Daten von "%s" bearbeiten'), $this->company->name);

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

        $f = new LunaFolder(Folder::findOneByRange_id($this->company->id));
        $this->documents = $f->getFiles();

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('companies'),
            Icon::create('vcard', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Unternehmensdaten bearbeiten') :
            dgettext('luna', 'Neues Unternehmen anlegen'),
            $this->url_for('companies/edit', $id),
            Icon::create('vcard+edit', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);

        $this->flash->keep();

        $this->clientUsers = SimpleCollection::createFromArray(array_map(function ($u) {
            return $u->user;
        }, LunaClientUser::findByClient_id($this->client->id)))->orderBy('nachname, vorname');

        $this->available_contact_persons = [];

        if (count($this->company->members) > 0) {
            $this->available_contact_persons = $this->company->members->filter(function ($person) {
                return $this->company->contact_persons->findOneBy('person_id', $person->id) == null;
            })->pluck('id');
        }

        $this->usersearch = QuickSearch::get('contact', new LunaContactPersonSearch($id))
            ->fireJSFunctionOnSelect('STUDIP.Luna.addContactPerson')
            ->setInputStyle('width: 240px');
    }

    /**
     * Save company data.
     *
     * @param string $id the company to save.
     */
    public function save_action($id = '')
    {

        if (Request::submitted('store')) {
            CSRFProtection::verifyUnsafeRequest();

            if ($id) {
                $company = LunaCompany::find($id);
            } else {
                $company = new LunaCompany($id);
            }
            $company->client_id = $this->client->client_id;
            $company->name = Request::get('name');
            $company->address = Request::get('address');
            $company->zip = Request::get('zip');
            $company->city = Request::get('city');
            $company->region = Request::get('region');
            $company->state = Request::get('state');
            $company->country = Request::get('country', 'Deutschland');
            $company->email = Request::get('email');
            $company->phone = Request::get('phone');
            $company->fax = Request::get('fax');
            $company->homepage = Request::get('homepage');
            $company->sector = Request::get('sector');
            $company->subsector = Request::get('subsector');

            $skills = [];
            foreach (Request::getArray('skills') as $skill) {
                $data = $this->client->skills->findOneBy('name', trim($skill));
                if (!$data) {
                    $data = new LunaSkill();
                    $data->client_id = $this->client->id;
                    $data->name = trim($skill);
                }
                if (count($data->companies) == 0) {
                    $data->companies = [$company];
                } else if (!$data->companies->findBy("company_id", $company->company_id)) {
                    $data->companies->append($company);
                }
                $data->store();
                $skills[] = $data;
            }
            $company->skills = SimpleORMapCollection::createFromArray($skills);

            $tags = [];
            foreach (Request::getArray('tags') as $tag) {
                $data = $this->client->tags->findOneBy('name', trim($tag));
                if (!$data) {
                    $data = new LunaTag();
                    $data->client_id = $this->client->id;
                    $data->name = trim($tag);
                }
                if (count($data->companies) == 0) {
                    $data->companies = [$company];
                } else if (!$data->companies->findBy("company_id", $company->company_id)) {
                    $data->companies->append($company);
                }
                $data->store();
                $tags[] = $data;
            }
            $company->tags = SimpleORMapCollection::createFromArray($tags);

            $contact_persons = new SimpleCollection();
            foreach (Request::getArray('contact_persons') as $one) {
                if ($one['id']) {
                    $entry = LunaCompanyContactPerson::find($one['id']);
                } else {
                    $entry = new LunaCompanyContactPerson();
                    $entry->person_id = $one['person_id'];

                    if ($company->members == null) {
                        $company->members = new SimpleCollection();
                    }
                }
                $entry->function = $one['function'];
                $contact_persons->append($entry);

                if (!$company->members->findOneBy('user_id', $entry->person_id)) {
                    $company->members->append(LunaUser::find($entry->person_id));
                }
            }
            $company->contact_persons = $contact_persons;

            $success = $company->store();

            if ($success !== false) {

                // Save last contact if given.
                if (Request::get('last_contact_date') && Request::option('last_contact_person') &&
                    Request::get('last_contact_contact')) {

                    if (count($company->last_contacts) < 1) {
                        $company->last_contacts = new SimpleCollection();
                    }

                    $lastContact = new LunaLastContact();
                    $lastContact->user_id = Request::option('last_contact_person');
                    $lastContact->luna_object_id = $company->id;
                    $lastContact->type = 'company';
                    $lastContact->date = strtotime(Request::get('last_contact_date'));
                    $lastContact->contact = Request::get('last_contact_contact');
                    $lastContact->notes = Request::get('last_contact_notes');

                    $success = $lastContact->store();

                    if ($success !== false) {

                        $company->last_contacts->append($lastContact);

                        if (is_array($_FILES['docs']) && $_FILES['docs']['error'][0] != 4) {
                            $folder = Folder::findOneByRange_id($lastContact->id);

                            if (!$folder) {
                                $folder = Folder::createTopFolder(
                                    $lastContact->id,
                                    'luna',
                                    'LunaFolder'
                                );
                                $folder->store();
                            }
                            $folder = $folder->getTypedFolder();

                            $uploaded = FileManager::handleFileUpload($_FILES['docs'], $folder, $GLOBALS['company']->id);

                            if ($uploaded['error']) {
                                $success = false;
                                PageLayout::postError(
                                    dgettext('luna', 'Es ist ein Fehler beim Dateiupload aufgetreten.'),
                                    $uploaded['error']
                                );
                            }
                        }

                    } else {

                        PageLayout::postError(
                            dgettext('luna', 'Es ist ein Fehler beim Speichern der letzten Kontakte aufgetreten.'));

                    }
                }

                if ($success) {
                    PageLayout::postSuccess(sprintf(
                        dgettext('luna', 'Die Unternehmensdaten von "%s" wurden gespeichert.'),
                        $company->name));
                }

            } else {
                PageLayout::postError(sprintf(
                    dgettext('luna', 'Die Unternehmensdaten von "%s" konnten nicht gespeichert werden.'),
                    $company->name));
            }

            $persondata = Request::getArray('person');
            if ($persondata['return_to']) {
                foreach ($persondata as $key => $value) {
                    if ($key != 'return_to') {
                        $this->flash[$key] = $value;
                    }
                }
                $this->flash['company'] = $company->id;
                $this->redirect($persondata['return_to']);
            } else {
                $this->relocate('companies');
            }
        } else if (Request::submitted('newperson')) {
            $request = Request::getInstance()->getIterator()->getArrayCopy();
            $filtered = array_filter($request, function($value, $key) {
                return ($value != '' && $value != null &&
                    !in_array($key, ['security_token', 'newperson', 'contact', 'contact_paramter']));
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($filtered as $key => $value) {
                $this->flash[$key] = $value;
            }
            $this->flash['return_to'] = $this->url_for('companies/edit', $id ?: null);

            $this->redirect($this->url_for('persons/edit'));

        }
    }

    public function delete_last_contact_action($contact_id)
    {
        $contact = LunaLastContact::find([$contact_id]);
        $company_id = $contact->company_id;
        if ($contact->delete()) {
            PageLayout::postSuccess(dgettext('luna', 'Der Eintrag wurde gelöscht.'));
        } else {
            PageLayout::postError(dgettext('luna', 'Der Eintrag konnte nicht gelöscht werden.'));
        }
        $this->relocate('companies/edit', $company_id);
    }

    /**
     * Delete a company.
     *
     * @param $id the company to delete
     */
    public function delete_action($id)
    {
        $company = LunaCompany::find($id);
        $name = $company->name;

        if ($company->delete()) {
            PageLayout::postSuccess(sprintf(dgettext('luna', 'Das Unternehmen "%s" wurde gelöscht.'), $name));
        } else {
            PageLayout::postError(sprintf(dgettext('luna', 'Das Unternehmen "%s" konnte nicht gelöscht werden.'), $name));
        }

        $this->relocate('companies');
    }

    /**
     * Do some action on several companies at once.
     */
    public function bulk_action()
    {
        $this->flash['bulkcompanies'] = Request::optionArray('companies');
        switch (Request::option('bulkaction')) {
            case 'message':
                $this->relocate('message/write/companies');
                break;
            case 'export':
                $this->redirect($this->url_for('export/csv', 'companies'));
                break;
	        case 'vcard':
		        $this->relocate('export/vcard', 'companies');
		        break;
        }
    }

    public function get_sectors_action()
    {
        $values = DBManager::get()->fetchFirst(
            "SELECT DISTINCT `sector` FROM `luna_companies` WHERE `client_id` = ? AND `sector` LIKE ? ORDER BY `sector`",
            [$this->client->id, '%' . Request::quoted('term') . '%']);
        $this->render_text(studip_json_encode($values));
    }

    public function get_subsectors_action()
    {
        $values = DBManager::get()->fetchFirst(
            "SELECT DISTINCT `subsector` FROM `luna_companies` WHERE `client_id` = ? AND `subsector` LIKE ? ORDER BY `subsector`",
            [$this->client->id, '%' . Request::quoted('term') . '%']);
        $this->render_text(studip_json_encode($values));
    }

    public function contact_person_template_action()
    {
        $this->person = new LunaCompanyContactPerson();
        $this->person->person_id = Request::option('contact_person');
        $this->person->user = LunaUser::find(Request::option('contact_person'));
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

