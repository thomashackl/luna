<?php
/**
 * message.php
 *
 * Message sending related stuff.
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

class MessageController extends AuthenticatedController {

    /**
     * Actions and settings taking place before every page call.
     */
    public function before_filter(&$action, &$args)
    {
        $this->plugin = $this->dispatcher->plugin;
        $this->flash = Trails_Flash::instance();

        $this->set_layout(Request::isXhr() ? null : $GLOBALS['template_factory']->open('layouts/base'));

        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage('sidebar/mail-sidebar.png');

        $views = new ViewsWidget();
        $views->addLink(dgettext('luna', 'Übersicht'),
            $this->url_for('persons'),
            Icon::create('person2', 'clickable'))->setActive(false);
        $views->addLink(dgettext('luna', 'Serienmail schreiben'),
            $this->url_for('message/write'),
            Icon::create('mail', 'clickable'))->setActive(true);
        $this->sidebar->addWidget($views);

        $this->client = LunaClient::findCurrent();

        if (Studip\ENV == 'development') {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.js';
        } else {
            $style = $this->plugin->getPluginURL().'/assets/stylesheets/luna.min.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/luna.min.js';
        }

        $this->wysiwyg = Config::get()->WYSIWYG && $GLOBALS['user']->cfg->WYSIWYG_DISABLED != 1;

        PageLayout::addStylesheet($style);
        PageLayout::addScript($js);
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.insert-at-caret.min.js');
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/javascripts/jquery.typing-0.2.0.min.js');
    }

    /**
     * List all available search presets.
     *
     * @param string $type write a message to a single user or a whole company
     * @param string $id message recipient
     */
    public function write_action($type = '', $id = '')
    {
        PageLayout::setTitle($this->plugin->getDisplayName() . ' - ' . dgettext('luna', 'Serienmail schreiben'));
        Navigation::activateItem('/tools/luna/persons');

        if ($type == 'user' && $id) {
            $ids = [$id];
            $this->type = 'user';
            $this->target_id = $id;
        } else if ($type == 'company' && $id) {
            $company = LunaCompany::find($id);
            $ids = $company->members->pluck('user_id');
            if ($company->contact_person) {
                $ids[] = $company->contact_person;
            }
            $this->type = 'company';
            $this->target_id = $id;
        } else if ($type == 'companies') {
            if ($this->flash['bulkcompanies']) {
                $companies = $this->flash['bulkcompanies'];
                $ids = DBManager::get()->fetchAll(
                    "SELECT DISTINCT `user_id`  FROM `luna_user_company` WHERE `company_id` IN (?)",
                    [$companies]);
            } else {
                $this->persons = $this->client->getFilteredUsers()->pluck('id');
            }
        } else {
            $ids = $this->flash['bulkusers'] ?: $this->persons = $this->client->getFilteredUsers()->pluck('id');
        }
        $this->users = array_filter(LunaUser::findMany($ids, "ORDER BY `lastname`, `firstname`"), function ($u) {
            return ($u->getDefaultEmail() !== null && $u->getDefaultEmail() !== '');
        });
        $this->markers = LunaMarker::findBySQL("1 ORDER BY `priority`");
    }

    /**
     * Sends message to recipients.
     */
    public function send_action()
    {
        $users = LunaUser::findMany(Request::getArray('recipients'));

        if (LunaMarker::hasMarkers(Request::get('message'))) {
            foreach ($users as $u) {
                $message = LunaMarker::replaceMarkers(Request::get('message'), $u, true);

                $message = $this->wysiwyg ? $message : formatReady($message);

                $mail = new StudipMail();
                $mail->setSubject(Request::get('subject'))
                    ->setReplyToEmail(Request::get('sender'))
                    ->setBodyText('')
                    ->setSenderEmail(Request::get('sender'))
                    ->addRecipient($u->getDefaultEmail(), $u->getFullname('full'))
                    ->setBodyHtml($message);

                // Attachments
                foreach ($_FILES['docs']['name'] as $index => $filename) {
                    if ($_FILES['docs']['error'][$index] === UPLOAD_ERR_OK && in_array($filename, Request::getArray('newdocs'))) {
                        $file = $filename;
                        $mail->addFileAttachment(
                            $_FILES['docs']['tmp_name'][$index],
                            $file,
                            $_FILES['docs']['type'][$index]);
                    }
                }

                if ($mail->send()) {
                    PageLayout::postSuccess(dgettext('luna', 'Die Nachricht wurde verschickt.'));
                } else {
                    PageLayout::postError(dgettext('luna', 'Die Nachricht konnte nicht verschickt werden.'));
                }
            }

            // Send copy to self or other recipients if requested.
            if (Request::int('sendercopy') || Request::get('cc')) {
                $message = $this->wysiwyg ? Request::get('message') : formatReady(Request::get('message'));

                $mail = new StudipMail();
                $mail->setSubject(Request::get('subject'))
                    ->setReplyToEmail(Request::get('sender'))
                    ->setBodyText('')
                    ->setSenderEmail(Request::get('sender'))
                    ->setBodyHtml($message);
                if (Request::int('sendercopy')) {
                    $mail->addRecipient(Request::get('sender'), '', 'Bcc');
                }
                // Extra recipients added in CC
                if (Request::get('cc')) {
                    foreach (explode(',', Request::get('cc')) as $cc) {
                        if (!$mail->isRecipient(trim($cc))) {
                            $mail->addRecipient(trim($cc), 'Cc');
                        }
                    }
                }

                // Attachments
                foreach ($_FILES['docs']['name'] as $index => $filename) {
                    if ($_FILES['docs']['error'][$index] === UPLOAD_ERR_OK && in_array($filename, Request::getArray('newdocs'))) {
                        $file = $filename;
                        $mail->addFileAttachment(
                            $_FILES['docs']['tmp_name'][$index],
                            $file,
                            $_FILES['docs']['type'][$index]);
                    }
                }

                if ($mail->send()) {
                    PageLayout::postSuccess(dgettext('luna', 'Die Nachricht wurde verschickt.'));
                } else {
                    PageLayout::postError(dgettext('luna', 'Die Nachricht konnte nicht verschickt werden.'));
                }
            }

        } else {
            $mail = new StudipMail();
            $mail->setSubject(Request::get('subject'))
                ->setReplyToEmail(Request::get('sender'))
                ->setBodyText('')
                ->setSenderEmail(Request::get('sender'));

            foreach ($users as $u) {
                if (!$mail->isRecipient($u->getDefaultEmail())) {
                    $mail->addRecipient($u->getDefaultEmail(), $u->getFullname('full'), 'Bcc');
                }
            }

            // Send copy to self if requested.
            if (Request::int('sendercopy') && !$mail->isRecipient($u->getDefaultEmail())) {
                $mail->addRecipient(Request::get('sender'), '', 'Bcc');
            }

            // Extra recipients added in CC
            if (Request::get('cc')) {
                foreach (explode(',', Request::get('cc')) as $cc) {
                    if (!$mail->isRecipient(trim($cc))) {
                        $mail->addRecipient(trim($cc), 'Cc');
                    }
                }
            }

            // Attachments
            if (count($_FILES['docs']['name']) > 0) {
                foreach ($_FILES['docs']['name'] as $index => $filename) {
                    if ($_FILES['docs']['error'][$index] === UPLOAD_ERR_OK && in_array($filename, Request::getArray('newdocs'))) {
                        $file = $filename;
                        $mail->addFileAttachment(
                            $_FILES['docs']['tmp_name'][$index],
                            $file,
                            $_FILES['docs']['type'][$index]);
                    }
                }
            }

            $message = $this->wysiwyg ? wysiwygReady(Request::get('message')) : formatReady(Request::get('message'));

            $mail->setBodyHtml(formatReady($message));

            if ($mail->send()) {
                PageLayout::postSuccess(dgettext('luna', 'Die Nachricht wurde verschickt.'));
            } else {
                PageLayout::postError(dgettext('luna', 'Die Nachricht konnte nicht verschickt werden.'));
            }
        }

        // Write log entry with info about the sent mail.
        $log = new LunaLogEntry();
        $log->user_id = $GLOBALS['user']->id;
        $log->affected = Request::option('type') == 'company' ?
            Request::option('target_id') : Request::getArray('recipients');
        $log->affected_type = Request::option('type') == 'company' ? 'company' : 'user';
        $log->action = 'MAIL';
        $log->info = dgettext('luna', 'Betreff') . ': ' . Request::get('subject');
        $log->store();

        $this->relocate('persons');
    }

    /**
     * customized #url_for for plugins
     */
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
