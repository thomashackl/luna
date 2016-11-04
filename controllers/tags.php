<?php
/**
 * tags.php
 *
 * Shows all registered tags.
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

class TagsController extends AuthenticatedController {

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
        $this->sidebar->setImage('sidebar/literature-sidebar.png');

        $this->client = LunaClient::getCurrentClient();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    /**
     * List all available skills.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/tags');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Schlagwörter'));

        $this->tags = $this->client->tags;
        if ($this->tags) {
            $this->tags->orderBy('name');
        }

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Schlagwort hinzufügen'),
                $this->url_for('tags/edit'),
                Icon::create('tag+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    /**
     * Create a new or edit an existing skill.
     *
     * @param string $id id of the tag to edit, empty if new tag
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/tags');

        if ($id) {
            $this->tag = LunaTag::find($id);
        } else {
            $this->tag = new LunaTag();
        }

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' .
        $id ?
            sprintf(dgettext('luna', 'Schlagwort %s bearbeiten'), $this->tag->name) :
            dgettext('luna', 'Neues Schlagwort anlegen'));

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('tags'),
            Icon::create('tag', 'clickable'))->setActive(false);
        $views->addLink($id ? dgettext('luna', 'Schlagwort bearbeiten') :
            dgettext('luna', 'Neues Schlagwort anlegen'),
            $this->url_for('tags/edit', $id),
            Icon::create('tag+add', 'clickable'))->setActive(true);
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
        $tag = LunaTag::find($id);
        $name = $tag->name;

        if ($tag->delete()) {
            PageLayout::postSuccess(sprintf(dgettext('luna', 'Das Schlagwort "%s" wurde gelöscht.'), $name));
        } else {
            PageLayout::postError(sprintf(dgettext('luna', 'Das Schlagwort "%s" konnte nicht gelöscht werden.'), $name));
        }

        $this->relocate('tags');
    }

    public function search_action()
    {
        $results = LunaTag::findBySQL("`name` LIKE ? ORDER BY `name`", array('%' . Request::quoted('term') . '%'));
        if (count($results) > 0) {
            $tags = array_map(function($t) { return $t->name; }, $results);
        } else {
            $tags = array();
        }
        $this->render_text(studip_json_encode($tags));
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
