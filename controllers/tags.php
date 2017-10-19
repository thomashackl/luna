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

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/literature-sidebar.png');

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
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    /**
     * List all available skills.
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/luna/tags');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Schlagwörter'));

        if ($this->hasWriteAccess) {
            $actions = new ActionsWidget();
            $actions->addLink(dgettext('luna', 'Schlagwort hinzufügen'),
                $this->url_for('tags/edit'),
                Icon::create('tag+add', 'clickable'))->asDialog('size=auto');
            $this->sidebar->addWidget($actions);
        }
    }

    public function load_tags_action($start = 0)
    {
        $this->entries_per_page = $this->client->getListMaxEntries('tags');
        if (count($this->client->tags) > 0) {
            $this->tags = $this->client->tags->limit($start, $this->entries_per_page);
        }
        $this->pagecount = ceil(count($this->client->tags) / $this->entries_per_page);
        $this->activepage = $start + 1;
    }

    /**
     * Create a new or edit an existing skill.
     *
     * @param string $id id of the tag to edit, empty if new tag
     */
    public function edit_action($id = '')
    {
        Navigation::activateItem('/tools/luna/tags');
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . $id ?
            dgettext('luna', 'Neues Schlagwort') :
            dgettext('Schlagwort bearbeiten'));

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

        $this->client = LunaClient::findCurrent();
        $access = $GLOBALS['perm']->have_perm('root') ? 'admin' :
            $this->client->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status;
        $this->hasWriteAccess = in_array($access, array('admin', 'write'));
    }

    public function save_action($id = '')
    {
        CSRFProtection::verifyUnsafeRequest();

        if ($id) {
            $tag = LunaTag::find($id);
        } else {
            $tag = new LunaTag();
        }

        // Check if a tag with the given name already exists.
        if (count($this->client->tags) > 0) {
            if ($samename = $this->client->tags->findOneBy('name', Request::get('name'))) {
                if (count($samename->users) > 0) {
                    $samename->users->merge($tag->users, 'ignore');
                } else {
                    $samename->users = $tag->users;
                }

                if (count($samename->companies) > 0) {
                    $samename->companies->merge($tag->companies, 'ignore');
                } else {
                    $samename->companies = $tag->companies;
                }

                $tag->delete();

                $tag = $samename;
            }
        }

        $tag->client_id = $this->client->client_id;
        $tag->name = Request::get('name');

        if ($tag->store()) {
            PageLayout::postSuccess(sprintf(
                dgettext('luna', 'Das Schlagwort %s wurde gespeichert.'),
                $tag->name));
        } else {
            PageLayout::postError(sprintf(
                dgettext('luna', 'Das Schlagwort %s konnte nicht gespeichert werden.'),
                $tag->name));
        }

        $persondata = Request::getArray('person');
        if ($persondata['return_to']) {
            foreach ($persondata as $key => $value) {
                if ($key != 'return_to') {
                    $this->flash[$key] = $value;
                }
            }
            $this->flash['tag'] = $tag->id;
            $this->redirect($persondata['return_to']);
        } else {
            $this->relocate('tags');
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
        $results = LunaTag::findBySQL("`client_id` = ?  AND `name` LIKE ? ORDER BY `name`",
            array($this->client->id, '%' . Request::quoted('term') . '%'));
        if (count($results) > 0) {
            $tags = array_map(function($t) { return $t->name; }, $results);
        } else {
            $tags = [];
        }
        $this->render_text(studip_json_encode($tags));
    }

    public function assigned_to_action($tag_id)
    {
        $this->tag = LunaTag::find($tag_id);

        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' .
            sprintf(dgettext('luna', 'Zugeordnet zu Schlagwort %s'), $this->skill->name));
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
