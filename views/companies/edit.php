<?php LunaClient::expireTableScheme() ?>
<form class="default" action="<?= $controller->url_for('companies/save', $company->id ?: null) ?>" method="post" data-dialog="reload-on-close" enctype="multipart/form-data">
    <header>
        <h1>
            <?= $company->isNew() ?
                dgettext('luna', 'Neues Unternehmen anlegen') :
                dgettext('luna', 'Unternehmensdaten bearbeiten') ?>
        </h1>
    </header>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Grunddaten') ?>
        </legend>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Name des Unternehmens') ?>
                </span>
                <input type="text" name="name" size="75" maxlength="255"
                       value="<?= htmlReady($company->name) ?>" required>
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Branche') ?>
                <input type="text" name="sector" value="<?= htmlReady($company->sector) ?>"
                       size="75" maxlength="255"
                       data-available-sectors="<?= $controller->url_for('companies/get_sectors') ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Unterbranche') ?>
                <input type="text" name="subsector" value="<?= htmlReady($company->subsector) ?>"
                       size="75" maxlength="255"
                       data-available-subsectors="<?= $controller->url_for('companies/get_subsectors') ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Anschrift') ?>
                <textarea name="address" rows="3" rows="3"><?= htmlReady($company->address) ?></textarea>
            </label>
        </section>
        <section>
            <label class="col-1">
                <?= dgettext('luna', 'PLZ') ?>
                <input type="text" name="zip" size="20" class="size-s" maxlength="20"
                       value="<?= htmlReady($company->zip) ?>">
            </label>
            <label class="col-5">
                <?= dgettext('luna', 'Ort') ?>
                <input type="text" name="city" size="75" maxlength="255"
                       value="<?= htmlReady($company->city) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Region') ?>
                <input type="text" name="region" size="75" maxlength="255"
                       value="<?= htmlReady($company->region) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Bundesland') ?>
                <input type="text" name="state" size="75" maxlength="255"
                       value="<?= htmlReady($company->state) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Land') ?>
                <input type="text" name="country" size="75" maxlength="255"
                       value="<?= htmlReady($company->country) ?: dgettext('luna', 'Deutschland') ?>">
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Kontaktdaten') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Kontaktperson') ?>
                <?= $usersearch->render() ?>
            </label>
            <?php if ($company->contact) : ?>
                <span class="luna-company-contact">
                    <input type="hidden" name="currentcontact" value="<?= $company->contact_person ?>">
                    <?= sprintf(dgettext('luna', 'Aktuell: %s'), htmlReady($company->contact->getFullname('full'))) ?>
                </span>
                <br>
            <?php endif ?>
            <?= dgettext('luna', 'oder') ?>
            <?= Studip\Button::create(dgettext('luna', 'Neue Person hinzufügen'), 'newperson',
                ['data-dialog' => '']) ?>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'E-Mailadresse') ?>
                <input type="email" name="email" size="75" maxlength="255"
                       value="<?= htmlReady($company->email) ?>">
            </label>
        </section>
        <section class="luna-phone companies-phone">
            <label>
                <?= dgettext('luna', 'Telefonnummer') ?>
                <input type="tel" pattern="[\+]\d{2}[\(]0[\)]\d{1,10}[\/]\d{1,30}[\-]*\d{1,30}$" name="phone" size="75" maxlength="255"
                       placeholder="<?= dgettext('luna', 'z.B. +49(0)123/4567-89') ?>" value="<?= htmlReady($company->phone) ?>">
            </label>
        </section>
        <section class="luna-phone companies-phone">
            <label>
                <?= dgettext('luna', 'Fax') ?>
                <input type="tel" pattern="[\+]\d{2}[\(]0[\)]\d{1,10}[\/]\d{1,30}[\-]*\d{1,30}$" name="fax" size="75" maxlength="255"
                       placeholder="<?= dgettext('luna', 'z.B. +49(0)123/4567-89') ?>" value="<?= htmlReady($company->fax) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Homepage') ?>
                <input type="url" name="homepage" size="75" maxlength="255"
                       value="<?= htmlReady($company->homepage) ?>" placeholder="http://">
            </label>
        </section>
    </fieldset>
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
            <div id="luna-company-skills">
                <?php if (count($company->skills) > 0) : ?>
                    <?php foreach ($company->skills as $skill) : ?>
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
                <div id="luna-company-tags">
                    <?php if (count($company->tags) > 0) : ?>
                        <?php foreach ($company->tags as $tag) : ?>
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
                                    <?= $company->tags->findOneBy('name', $tag->name) ? ' selected' : '' ?>>
                                <?= htmlReady($tag->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </label>
            <?php endif ?>
        </section>
    </fieldset>
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
            <col width="200">
            <col width="200">
            <col>
            <col width="200">
            <col width="16">
        </colgroup>
        <thead id="luna-last-contacts-thead"<?= count($company->last_contacts) < 1 ? ' class="hidden-js"' : '' ?>>
            <tr>
                <th id="luna-last-contact-date"><?= dgettext('luna', 'Wann?') ?></th>
                <th id="luna-last-contact-who"><?= dgettext('luna', 'Wer?') ?></th>
                <th id="luna-last-contact-contact"><?= dgettext('luna', 'Mit wem?') ?></th>
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
                    <select name="last_contact_person" size="1"
                            aria-labelledby="luna-last-contact-legend luna-last-contact-who">
                        <option value="">
                            -- <?= dgettext('luna', 'bitte auswählen') ?> --
                        </option>
                        <?php foreach ($clientUsers as $u) : ?>
                            <option value="<?= $u->user_id ?>">
                                <?= htmlReady($u->getFullname('full')) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <?= QuickSearch::get('last_contact_contact', new StandardSearch('user_id'))
                        ->withButton()
                        ->render();
                    ?>

                </td>
                <td>
                    <textarea name="last_contact_notes" cols="50" rows="3"
                              aria-labelledby="luna-last-contact-legend luna-last-contact-notes"></textarea>
                </td>
                <td>
                    <section>
                        <label class="luna-cursor-pointer">
                            <input type="file" name="docs[]" multiple>
                            <?= Icon::create('upload', 'clickable', ['title' => _('Datei hochladen'), 'class' => 'text-bottom']) ?>
                            <?= _('Datei hochladen') ?>
                        </label>
                        <ul id="luna-newdocs"></ul>
                    </section>
                </td>
            </tr>
            <?php foreach ($company->last_contacts as $lc) : ?>
                <?php $folder = LunaFolder::findTopFolder($lc->id)?>
                <?php if($folder != NULL) : ?>
                    <tr>
                        <td><?= date('d.m.Y', $lc->date) ?></td>
                        <td><?= htmlReady($lc->user->getFullName()) ?></td>
                        <td><?= htmlReady(User::find($lc->contact)->getFullname()) ?></td>
                        <td><?= htmlReady($lc->notes) ?></td>
                        <td>
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
                        </td>
                        <td>
                            <a href="<?= $controller->url_for('companies/delete_last_contact', $lc->contact_id) ?>"
                               data-confirm="<?= dgettext('luna', 'Soll der Eintrag wirklich gelöscht werden?') ?>">
                                <?= Icon::create('trash') ?>
                            </a>
                        </td>
                    </tr>
                <?php endif ?>
            <?php endforeach ?>
        </tbody>
    </table>
    <footer data-dialog-button>
        <?php foreach ($flash->flash as $key => $value) : ?>
            <?php if (is_array($value)) : ?>
                <?php foreach ($value as $entry) : ?>
                    <input type="hidden" name="person[<?= $key ?>][]" value="<?= $entry ?>">
                <?php endforeach ?>
            <?php else : ?>
                <input type="hidden" name="person[<?= $key ?>]" value="<?= $value ?>">
            <?php endif ?>
        <?php endforeach ?>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('companies')) ?>
    </footer>
</form>
