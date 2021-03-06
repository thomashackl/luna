<form class="default" action="<?= $controller->url_for('persons/save', $pid ?: null) ?>"
      method="post" enctype="multipart/form-data"<?= $flash['return_to'] ? ' data-dialog' : '' ?>>
    <header>
        <h1>
            <?= $person->isNew() ?
                dgettext('luna', 'Neue Person anlegen') :
                sprintf(dgettext('luna', 'Personendaten von %s'), $this->person->getFullname('full')) ?>
        </h1>
    </header>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Persönliche Daten') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Status') ?>
                <input type="text" name="status" value="<?= htmlReady($person->status) ?>" size="75"
                       maxlength="255" data-available-status="<?= $controller->url_for('persons/get_status') ?>">
            </label>
        </section>
        <section class="col-3">
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Vorname') ?>
                </span>
                <input type="text" name="firstname" size="75" maxlength="255"
                       value="<?= htmlReady($person->firstname) ?>" required>
            </label>
        </section>
        <section class="col-3">
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Nachname') ?>
                </span>
                <input type="text" name="lastname" size="75" maxlength="255"
                       value="<?= htmlReady($person->lastname) ?>" required>
            </label>
        </section>
        <section>
            <label class="col-2">
                <?= dgettext('luna', 'Vorangestellter Titel') ?>
                <input type="text" name="title_front" size="50" maxlength="255"
                       value="<?= htmlReady($person->title_front) ?>"
                       placeholder="<?= dgettext('luna', 'z.B. Prof., Dr., etc.') ?>">
            </label>
            <label class="col-2">
                <?= dgettext('luna', 'Vorangestellter, ausgeschriebener Titel') ?>
                <input type="text" name="title_front_full" size="50" maxlength="255"
                       value="<?= htmlReady($person->title_front_full) ?>"
                       placeholder="<?= dgettext('luna', 'z.B. Professor, Doktor, etc.') ?>">
            </label>
            <label class="col-2">
                <?= dgettext('luna', 'Nachgestellter Titel') ?>
                <input type="text" name="title_rear" size="75" maxlength="255"
                       value="<?= htmlReady($person->title_rear) ?>"
                       placeholder="<?= dgettext('luna', 'z.B. LL.M., PhD, etc.') ?>">
            </label>
        </section>
        <section>
            <label for="gender-unknown">
                <?= dgettext('luna', 'Geschlecht') ?>
            </label>
            <label>
                <input type="radio" name="gender"
                       value="1"<?= $person->gender == 1 ? ' checked' : '' ?>>
                <?= dgettext('luna', 'männlich') ?>
            </label>
            <label>
                <input type="radio" name="gender"
                       value="2"<?= $person->gender == 2 ? ' checked' : '' ?>>
                <?= dgettext('luna', 'weiblich') ?>
            </label>
            <label>
                <input type="radio" name="gender" id="gender-unknown"
                       value="0"<?= ($person->gender == 0 || $person->isNew()) ? ' checked' : '' ?>>
                <?= dgettext('luna', 'unbekannt') ?>
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Anrede') ?>
        </legend>
        <section>
            <label>
                <input type="checkbox" name="informal" value="1"<?= $person->informal ? ' checked' : '' ?>>
                <?= dgettext('luna', 'Anrede per du') ?>
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Adresse') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Anschrift') ?>
                <textarea name="address" rows="3" cols="74"><?= htmlReady($person->address) ?></textarea>
            </label>
        </section>
        <section>
            <label class="col-1">
                <?= dgettext('luna', 'PLZ') ?>
                <input type="text" name="zip" size="20" class="size-s" maxlength="20" value="<?= htmlReady($person->zip) ?>">
            </label>
            <label class="col-5">
                <?= dgettext('luna', 'Ort') ?>
                <input type="text" name="city" size="75" maxlength="255"
                       value="<?= htmlReady($person->city) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Land') ?>
                <input type="text" name="country" size="75" maxlength="255"
                       value="<?= htmlReady($person->country) ?>">
            </label>
        </section>
    </fieldset>
    <fieldset id="luna-emails">
        <legend>
            <?= dgettext('luna', 'E-Mailadresse(n)') ?>
        </legend>
        <section id="luna-email-template" class="luna-email" data-number-of-emails="<?= count($person->emails) ?>">
            <label class="col-3">
                <?= dgettext('luna', 'Adresse') ?>
                <br>
                <input type="email" name="email-template-address" size="50" placeholder="<?= dgettext('luna', 'Geben Sie eine E-Mailadresse ein') ?>">
            </label>
            <label class="col-1">
                <?= dgettext('luna', 'Art') ?>
                <select name="email-template-type">
                    <option value="private"><?= dgettext('luna', 'Privat') ?></option>
                    <option value="office" selected><?= dgettext('luna', 'Geschäftlich') ?></option>
                </select>
            </label>
            <label class="col-2">
                <input type="radio" name="email-template-default">
                <?= dgettext('luna', 'Standardadresse') ?>
            </label>
        </section>
        <?php $i = 0; foreach ($person->emails as $m) : ?>
            <section class="luna-email">
                <label class="col-3">
                    <?= dgettext('luna', 'Adresse') ?>
                    <input type="email" name="email[<?= $i ?>][address]" size="50" value="<?= htmlReady($m->email) ?>">
                </label class="col-1">
                <label>
                    <?= dgettext('luna', 'Art') ?>
                    <select name="email[<?= $i ?>][type]">
                        <option value="private"<?= $m->type == 'private' ? ' selected' : '' ?>>
                            <?= dgettext('luna', 'Privat') ?>
                        </option>
                        <option value="office"<?= $m->type == 'office' ? ' selected' : '' ?>>
                            <?= dgettext('luna', 'Geschäftlich') ?>
                        </option>
                    </select>
                </label>
                <label class="col-2">
                    <input type="radio" name="email-default" value="<?= $i ?>"<?= $m->default ? ' checked' : '' ?>>
                    <?= dgettext('luna', 'Standardadresse') ?>
                </label>
            </section>
        <?php $i++; endforeach ?>
        <a class="luna-email-add" href="">
            <?= Icon::create('add', 'clickable', ['title' => dgettext('luna', 'E-Mailadresse hinzufügen')])->asImg(24) ?>
        </a>
    </fieldset>
    <fieldset id="luna-phone">
        <legend>
            <?= dgettext('luna', 'Telefonnummer(n)') ?>
        </legend>
        <section id="luna-phone-template" class="luna-phone" data-number-of-phonenumbers="<?= count($person->phonenumbers) ?>">
            <label class="col-3">
                <?= dgettext('luna', 'Nummer') ?>
                <br>
                <input type="tel" pattern="[\+]\d{2}[\(]0[\)]\d{1,10}[\/]\d{1,30}[\-]*\d{1,30}$" name="phone-template-number" size="50"
                       placeholder="<?= dgettext('luna', 'z.B. +49(0)123/4567-89') ?>">
            </label>
            <label class="col-1">
                <?= dgettext('luna', 'Art') ?>
                <select name="phone-template-type">
                    <option value="private">
                        <?= dgettext('luna', 'Privat') ?>
                    </option>
                    <option value="mobile">
                        <?= dgettext('luna', 'Mobil') ?>
                    </option>
                    <option value="office" selected>
                        <?= dgettext('luna', 'Geschäftlich') ?>
                    </option>
                </select>
            </label>
            <label class="col-2">
                <input type="radio" name="phone-template-default">
                <?= dgettext('luna', 'Standardnummer') ?>
            </label>
        </section>
        <?php $i = 0; foreach ($person->phonenumbers as $p) : ?>
            <section class="luna-phone">
                <label class="col-3">
                    <?= dgettext('luna', 'Nummer') ?>
                    <input type="tel" name="phone[<?= $i ?>][number]" size="50" value="<?= htmlReady($p->number) ?>">
                </label>
                <label class="col-1">
                    <?= dgettext('luna', 'Art') ?>
                    <select name="phone[<?= $i ?>][type]">
                        <option value="private"<?= $p->type == 'private' ? ' selected' : '' ?>>
                            <?= dgettext('luna', 'Privat') ?>
                        </option>
                        <option value="mobile"<?= $p->type == 'mobile' ? ' selected' : '' ?>>
                            <?= dgettext('luna', 'Mobil') ?>
                        </option>
                        <option value="office"<?= $p->type == 'office' ? ' selected' : '' ?>>
                            <?= dgettext('luna', 'Geschäftlich') ?>
                        </option>
                    </select>
                </label>
                <label class="col-2">
                    <input type="radio" name="phone-default" value="<?= $i ?>"<?= $p->default ? ' checked' : '' ?>>
                    <?= dgettext('luna', 'Standardnummer') ?>
                </label>
            </section>
        <?php $i++; endforeach ?>
        <a class="luna-phone-add" href="">
            <?= Icon::create('add', 'clickable', ['title' => dgettext('luna', 'Telefonnummer hinzufügen')])->asImg(24) ?>
        </a>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Weitere Kontaktdaten') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Fax') ?>
                <input type="text" name="fax" size="75" maxlength="255"
                       value="<?= htmlReady($person->fax) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Homepage') ?>
                <input type="url" name="homepage" size="75" maxlength="255"
                       value="<?= htmlReady($person->homepage) ?>" placeholder="http://">
            </label>
        </section>
    </fieldset>
    <?php if (count($companies) > 0) : ?>
        <fieldset>
            <legend>
                <?= dgettext('luna', 'Unternehmen') ?>
            </legend>
            <section>
                <label>
                    <?= dgettext('luna', 'Bestehendes Unternehmen auswählen') ?>
                    <select name="company" class="nested-select">
                        <option value="">-- <?= dgettext('luna', 'Kein Unternehmen auswählen') ?> --</option>
                        <?foreach ($companies as $company) : ?>
                            <option value="<?= $company->id ?>"<?= $person->companies->find($company->id) ? ' selected' : ''?>>
                                <?= htmlReady($company->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </label>
                <?= dgettext('luna', 'oder') ?>
                <?= Studip\Button::create(dgettext('luna', 'Neues Unternehmen hinzufügen'), 'newcompany',
                    ['data-dialog' => '']) ?>
            </section>
        </fieldset>
    <?php endif ?>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Kompetenzen') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Fügen Sie eine Kompetenz hinzu') ?>:
                <br>
                <input type="text" name="skill" size="40" class="luna-new-skill" data-available-skills="<?= $controller->url_for('skills/search') ?>">
                <a class="luna-skill-add" href="">
                    <?= Icon::create('add', 'clickable')->asImg(24) ?>
                </a>
            </label>
            <div id="luna-person-skills">
                <?php if (count($person->skills) > 0) : ?>
                    <?php foreach ($person->skills as $skill) : ?>
                        <div class="luna-skill" id="luna-skill-<?= htmlReady(str_replace(' ', '-', $skill->name)) ?>">
                            <?= htmlReady($skill->name) ?>
                            <input type="hidden" name="skills[]" value="<?= htmlReady($skill->name) ?>">
                            <a class="luna-skill-remove" href="">
                                <?= Icon::create('trash', 'clickable')->asImg() ?>
                            </a>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Schlagworte') ?>
        </legend>
        <section>
            <?php if ($client->getConfigValue('auto_create_tags')) : ?>
                <label>
                    <?= dgettext('luna', 'Fügen Sie ein Schlagwort hinzu') ?>:
                    <br>
                    <input type="text" name="tag" size="40" class="luna-new-tag" data-available-tags="<?= $controller->url_for('tags/search') ?>">
                    <a class="luna-tag-add" href="">
                        <?= Icon::create('add', 'clickable')->asImg(24) ?>
                    </a>
                </label>
                <div id="luna-person-tags">
                    <?php if (count($person->tags) > 0) : ?>
                        <?php foreach ($person->tags as $tag) : ?>
                            <div class="luna-tag" id="luna-tag-<?= htmlReady(str_replace(' ', '-', $tag->name)) ?>">
                                <?= htmlReady($tag->name) ?>
                                <input type="hidden" name="tags[]" value="<?= htmlReady($tag->name) ?>">
                                <a class="luna-tag-remove" href="">
                                    <?= Icon::create('trash', 'clickable')->asImg() ?>
                                </a>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            <?php else : ?>
                <label>
                    <?= dgettext('luna', 'Fügen Sie ein Schlagwort hinzu') ?>:
                    <br>
                    <select name="tags[]" size="40" class="nested-select" multiple>
                        <option value="">-- <?= dgettext('luna', 'bitte auswählen') ?> --</option>
                        <?php foreach ($client->tags as $tag) : ?>
                            <option value="<?= htmlReady($tag->name) ?>"
                                <?= $person->tags->findOneBy('name', $tag->name) ? ' selected' : '' ?>>
                                <?= htmlReady($tag->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </label>
            <?php endif ?>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Weitere Daten') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Hochschulabschluss') ?>
                <input type="text" name="graduation" value="<?= htmlReady($person->graduation) ?>" size="75" maxlength="255">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Notizen') ?>
                <textarea name="notes" cols="100" rows="10" class="<?= $wysiwyg ? 'wysiwyg' : 'add_toolbar' ?>"><?= htmlReady($person->notes) ?></textarea>
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Dateien') ?>
        </legend>
        <section>
            <label class="luna-cursor-pointer">
                <input type="file" name="docs[]" multiple>
                <?= Icon::create('upload', 'clickable', ['title' => _('Datei hochladen'), 'class' => 'text-bottom']) ?>
                <?= _('Datei hochladen') ?>
            </label>
            <ul class="luna-newdocs"></ul>
        </section>
        <?php if (count($documents) > 0) : ?>
            <section>
                <h3><?= dgettext('luna', 'Vorhandene Dateien') ?></h3>
                <ul id="luna-userdocs">
                    <?php foreach ($documents as $d) : ?>
                        <li>
                            <input type="hidden" name="userdocs[]" value="<?= $d->id ?>">
                            <a href="<?= $d->getDownloadURL() ?>" target="_blank">
                                <?= FileManager::getIconForMimeType($d->file->mime_type) ?>
                                <?= htmlReady($d->name) ?>
                            </a>
                            <a href="<?= $controller->url_for('persons/delete_doc', $person->id, $d->id) ?>">
                                <?= Icon::create('trash', 'clickable', ['class' => 'text-bottom']) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Verknüpfung mit Stud.IP') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Mit einer Stud.IP-Kennung verknüpfen') ?>
                <?= $usersearch->render() ?>
            </label>
            <?php if ($person->studip_user_id) : ?>
                <span class="luna-person-studip">
                    <input type="hidden" name="currentstudipuser" value="<?= $person->studip_user_id ?>">
                    <?= sprintf(dgettext('luna', 'Aktuell: %s'),
                        htmlReady($person->studip_user->getFullname('full') .
                            ' (' . $person->studip_user->username . ')')) ?>
                </span>
            <?php endif ?>
        </section>
    </fieldset>
    <?php if (!$person->isNew()) : ?>
        <table class="default">
            <caption>
                <?= dgettext('luna', 'Letzte Kontakte') ?>
                <span class="actions" id="luna-add-last-contact-container">
                    <a href="" id="luna-add-last-contact">
                        <?= Icon::create('add') ?>
                        <?= dgettext('luna', 'hinzufügen') ?>
                    </a>
                </span>
            </caption>
            <colgroup>
                <col width="150">
                <col width="250">
                <col>
                <col width="200">
                <col width="16">
            </colgroup>
            <thead id="luna-last-contacts-thead"<?= count($person->last_contacts) < 1 ? ' class="hidden-js"' : '' ?>>
                <tr>
                    <th id="luna-last-contact-date"><?= dgettext('luna', 'Wann?') ?></th>
                    <th id="luna-last-contact-who"><?= dgettext('luna', 'Wer?') ?></th>
                    <th id="luna-last-contact-notes"><?= dgettext('luna', 'Notizen') ?></th>
                    <th id="luna-last-contact-documents"><?= dgettext('luna', 'Dokumente') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <tr id="luna-new-last-contact" class="hidden-js">
                <td>
                    <input type="text" name="last_contact_date" value="<?= date('d.m.Y') ?>"
                           readonly data-date-picker
                           aria-labelledby="luna-last-contact-legend luna-last-contact-date">
                </td>
                <td>
                    <?= QuickSearch::get('last_contact_person', new StandardSearch('user_id'))
                        ->withButton()
                        ->setAttributes(['aria-labelledby' => 'luna-last-contact-legend luna-last-contact-who'])
                        ->render();
                    ?>
                    <input type="hidden" name="last_contact_contact" value="<?= htmlReady($person->getFullname()) ?>">
                </td>
                <td>
                        <textarea name="last_contact_notes" cols="50" rows="3"
                                  aria-labelledby="luna-last-contact-legend luna-last-contact-notes"></textarea>
                </td>
                <td colspan="2">
                    <section>
                        <label class="luna-cursor-pointer">
                            <input type="file" name="contactdocs[]" multiple>
                            <?= Icon::create('upload', 'clickable', ['title' => _('Datei hochladen'), 'class' => 'text-bottom']) ?>
                            <?= _('Datei hochladen') ?>
                        </label>
                        <ul class="luna-newdocs"></ul>
                    </section>
                </td>
            </tr>
            <?php foreach ($person->last_contacts as $lc) : ?>
                <?php $folder = LunaFolder::findTopFolder($lc->id) ?>
                <tr>
                    <td><?= date('d.m.Y', $lc->date) ?></td>
                    <td><?= htmlReady($lc->user->getFullName()) ?></td>
                    <td><?= $lc->notes ?></td>
                    <td>
                        <?php if ($folder != NULL) : ?>
                            <?php if (count($folder->getFiles()) > 0) : ?>
                                <section id="luna-last-contact-doc-list">
                                    <ul id="luna-last_contact_docs">
                                        <?php foreach ($folder->getFiles() as $d) : ?>
                                            <li>
                                                <input type="hidden" name="last_contact_docs[]" value="<?= $d->id ?>">
                                                <a href="<?= $d->getDownloadURL() ?>" target="_blank">
                                                    <?= FileManager::getIconForMimeType($d->file->mime_type) ?>
                                                    <?= htmlReady($d->name) ?>
                                                </a>
                                            </li>
                                        <?php endforeach ?>
                                    </ul>
                                </section>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <a href="<?= $controller->url_for('persons/delete_last_contact', $lc->contact_id) ?>"
                           data-confirm="<?= dgettext('luna', 'Soll der Eintrag wirklich gelöscht werden?') ?>">
                            <?= Icon::create('trash') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
    <footer data-dialog-button>
        <?php foreach ($flash->flash as $key => $value) : ?>
            <?= $this->render_partial('persons/_flashvalue', ['prefix' => 'company', 'key' => $key, 'value' => $value]) ?>
        <?php endforeach ?>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
