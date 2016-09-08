<?php
/**
 * skill.php
 *
 * Shows all registered skills.
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

class SkillsController extends AuthenticatedController {

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
        $this->sidebar->setImage('sidebar/roles-sidebar.png');
    }

    /**
     * List all available skills.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/skills');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Kompetenzen'));

        $this->skills = $this->client->skills;
        if ($this->skills) {
            $this->skills->orderBy('name');
        }

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Kompetenz hinzuf�gen'),
                $this->url_for('skills/edit'),
                Icon::create('roles+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    /**
     * Create a new or edit an existing skill.
     *
     * @param string $id id of the skill to edit, empty if new skill
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/skills');

        if ($id) {
            $this->skill = LunaSkill::find($id);
        } else {
            $this->skill = new LunaSkill();
        }

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' .
            $id ?
            sprintf(dgettext('luna', 'Kompetenz %s bearbeiten'), $this->skill->name) :
            dgettext('luna', 'Neue Kompetenz anlegen'));

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', '�bersicht'),
            $this->url_for('skills'),
            Icon::create('roles', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Kompetenz bearbeiten') :
            dgettext('luna', 'Neue Kompetenz anlegen'),
            $this->url_for('skills/edit', $id),
            Icon::create('roles+add', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);

        $this->flash->keep();

        $this->client = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    public function save_action($id = '')
    {
        CSRFProtection::verifyUnsafeRequest();

        if ($id) {
            $skill = LunaSkill::find($id);
        } else {
            $skill = new LunaSkill($id);
        }
        $skill->client_id = $this->client->client_id;
        $skill->name = Request::get('name');

        if ($skill->store()) {
            PageLayout::postSuccess(sprintf(
                dgettext('luna', 'Die Kompetenz %s wurde gespeichert.'),
                $skill->name));
        } else {
            PageLayout::postError(sprintf(
                dgettext('luna', 'Die Kompetenz %s konnte nicht gespeichert werden.'),
                $skill->name));
        }

        $persondata = Request::getArray('person');
        if ($persondata['return_to']) {
            foreach ($persondata as $key => $value) {
                if ($key != 'return_to') {
                    $this->flash[$key] = $value;
                }
            }
            $this->flash['skill'] = $skill->id;
            $this->redirect($persondata['return_to']);
        } else {
            $this->relocate('skills');
        }
    }

    public function delete_action($id)
    {
        $skill = LunaSkill::find($id);
        $name = $skill->name;

        if ($skill->delete()) {
            PageLayout::postSuccess(sprintf(dgettext('luna', 'Die Kompetenz "%s" wurde gel�scht.'), $name));
        } else {
            PageLayout::postError(sprintf(dgettext('luna', 'Die Kompetenz "%s" konnte nicht gel�scht werden.'), $name));
        }

        $this->relocate('skills');
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
