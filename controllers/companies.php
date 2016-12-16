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
        PageLayout::addScript($js);

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

    public function load_companies_action($start = 0)
    {
        LunaCompanyFilter::setFilters($this->client->id, Request::getArray('filters'));

        $this->allfilters = LunaCompanyFilter::getFilterFields(true);
        $this->filters = LunaCompanyFilter::getFilters($GLOBALS['user']->id, $this->client->id);

        $this->companies = $this->client->getFilteredCompanies($start);
        $this->companycount = $this->client->getFilteredCompaniesCount();
        $this->entries_per_page = $this->client->getListMaxEntries('companies');
        $this->pagecount = ceil($this->companycount / $this->entries_per_page);
        $this->activepage = $start + 1;
    }

    /**
     * Create a new or edit an existing person.
     *
     * @param string $id id of the person to edit, empty if new person
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/companies');

        if ($id) {
            $this->company = LunaCompany::find($id);
        } else {
            $this->company = new LunaCompany();
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

