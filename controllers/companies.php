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
        $this->sidebar->setImage('sidebar/institute-sidebar.png');

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
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');

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

        if ($this->companies) {
            $this->companies->orderBy('name');
        }

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Unternehmen hinzufügen'),
                $this->url_for('companies/edit'),
                Icon::create('vcard+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    public function load_companies_action($start = 0, $searchtext = '')
    {
        LunaCompanyFilter::setFilters($this->client->id, Request::getArray('filters'));

        $this->allfilters = LunaCompanyFilter::getFilterFields(true);
        $this->filters = LunaCompanyFilter::getFilters($GLOBALS['user']->id, $this->client->id);
        $this->searchtext = studip_utf8decode($searchtext);

        $this->companies = $this->client->getFilteredCompanies($start, 0, $this->searchtext);
        $this->companycount = $this->client->getFilteredCompaniesCount($this->searchtext);
        $this->entries_per_page = $this->client->getListMaxEntries('companies');
        $this->pagecount = ceil($this->personcount / $this->entries_per_page);
        $this->activepage = $start + 1;
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
            array($company, $user));
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

        foreach (words('name contact_person street zip city country email phone fax homepage') as $entry) {
            if (isset($this->flash[$entry])) {
                $this->company->$entry = $this->flash[$entry];
            }
        }

        $title = $this->company->isNew() ?
            dgettext('luna', 'Neues Unternehmen anlegen') :
            sprintf(dgettext('luna', 'Daten von "%s" bearbeiten'), $this->company->name);

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $title);

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

        $this->usersearch = QuickSearch::get('contact', new LunaSearch('user_id'))
            ->setInputStyle('width: 240px');
    }

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
            $company->contact_person = Request::get('contact') ?: Request::get('currentcontact') ?: null;
            $company->street = Request::get('street');
            $company->zip = Request::get('zip');
            $company->city = Request::get('city');
            $company->country = Request::get('country', 'Deutschland');
            $company->email = Request::get('email');
            $company->phone = Request::get('phone');
            $company->fax = Request::get('fax');
            $company->homepage = Request::get('homepage');

            $tags = array();
            foreach (Request::getArray('tags') as $tag) {
                $data = $this->client->tags->findOneBy('name', trim($tag));
                if (!$data) {
                    $data = new LunaTag();
                    $data->client_id = $this->client->id;
                    $data->name = trim($tag);
                }
                if (!$data->companies) {
                    $data->companies = array($company);
                } else if (!$data->companies->findByCompany_id($company->company_id)) {
                    $data->companies->append($company);
                }
                $data->store();
                $tags[] = $data;
            }
            $company->tags = SimpleORMapCollection::createFromArray($tags);

            if ($company->store()) {
                PageLayout::postSuccess(sprintf(
                    dgettext('luna', 'Die Unternehmensdaten von "%s" wurden gespeichert.'),
                    $company->name));
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
            $this->flash['name'] = Request::get('name');
            if (Request::option('currentcontact')) {
                $this->flash['contact_person'] = Request::option('currentcontact');
            }
            $this->flash['street'] = Request::get('street');
            $this->flash['zip'] = Request::get('zip');
            $this->flash['city'] = Request::get('city');
            $this->flash['country'] = Request::get('country');
            $this->flash['email'] = Request::get('email');
            $this->flash['phone'] = Request::get('phone');
            $this->flash['fax'] = Request::get('fax');
            $this->flash['homepage'] = Request::get('homepage');
            $this->flash['tags'] = Request::getArray('tags');
            $this->flash['return_to'] = $this->url_for('companies/edit', $id ?: null);

            $this->redirect($this->url_for('persons/edit'));

        }
    }

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

    public function bulk_action()
    {
        $this->flash['bulkcompanies'] = Request::optionArray('companies');
        switch (Request::option('bulkaction')) {
            case 'message':
                $this->relocate('message/write/companies');
                break;
            case 'export':
                $this->redirect($this->url_for('companies/export_companies'));
                break;
        }
    }

    public function export_companies_action()
    {
        $this->fields = LunaCompanyFilter::getFilterFields(true);

        $this->selected = array_keys($this->fields);

        if (Request::submitted('do_export')) {
            $companies = Request::optionArray('companies') ?
                LunaCompany::findMany(Request::optionArray('companies')) :
                $this->client->getFilteredCompanies(0, -1);
            $csv = array();
            $csv[] = array_map(function($entry) {
                return $entry['name'];
            }, $this->fields);
            foreach ($companies as $company) {
                $entry = array();
                foreach ($this->selected as $field) {
                    if ($company->$field instanceof SimpleORMapCollection) {
                        $entry[] = implode("\n", $company->$field->pluck('name'));
                    } else {
                        $entry[] = $company->$field;
                    }
                }
                $csv[] = $entry;
            }
            $this->response->add_header('Content-Type', 'text/csv');
            $this->response->add_header('Content-Disposition', 'attachment; filename=' .
                Request::get('filename') . '.csv');
            $this->render_text(array_to_csv($csv));
        } else {
            PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Dateinamen wählen'));
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

