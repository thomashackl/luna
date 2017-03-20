<form class="default" action="<?= $controller->url_for('persons/save', $pid ?: null) ?>" method="post" enctype="multipart/form-data" data-dialog="reload-on-close">
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
                <input type="text" name="status" value="<?= htmlReady($person->status) ?>" size="75" maxlength="255" data-available-status="<?= $controller->url_for('persons/get_status') ?>">
            </label>
        </section>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Vorname') ?>
                </span>
                <input type="text" name="firstname" size="75" maxlength="255"
                       value="<?= htmlReady($person->firstname) ?>" required>
            </label>
        </section>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Nachname') ?>
                </span>
                <input type="text" name="lastname" size="75" maxlength="255"
                       value="<?= htmlReady($person->lastname) ?>" required>
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Vorgestellter Titel') ?>
                <input type="text" name="title_front" size="75" maxlength="255" value="<?= htmlReady($person->title_front) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Nachgestellter Titel') ?>
                <input type="text" name="title_rear" size="75" maxlength="255" value="<?= htmlReady($person->title_rear) ?>">
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
            <?= dgettext('luna', 'Adresse') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Straße') ?>
                <input type="text" name="street" size="75" maxlength="255" value="<?= htmlReady($person->street) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'PLZ') ?>
                <input type="text" name="zip" size="20" class="size-s" maxlength="20" value="<?= htmlReady($person->zip) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Ort') ?>
                <input type="text" name="city" size="75" maxlength="255"
                       value="<?= htmlReady($person->city) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Land') ?>
                <input type="text" name="country" size="75" maxlength="255"
                       value="<?= htmlReady($person->country) ?: dgettext('luna', 'Deutschland') ?>">
            </label>
        </section>
    </fieldset>
    <fieldset id="luna-emails">
        <legend>
            <?= dgettext('luna', 'E-Mailadresse(n)') ?>
        </legend>
        <section id="luna-email-template" class="luna-email" data-number-of-emails="<?= count($person->emails) ?>">
            <label>
                <?= dgettext('luna', 'Adresse') ?>
                <input type="email" name="email-template-address" size="50" placeholder="<?= dgettext('luna', 'Geben Sie eine E-Mailadresse ein') ?>">
            </label>
            <label>
                <?= dgettext('luna', 'Art') ?>
                <select name="email-template-type">
                    <option value="private"><?= dgettext('luna', 'Privat') ?></option>
                    <option value="office"><?= dgettext('luna', 'Geschäftlich') ?></option>
                </select>
            </label>
            <label>
                <input type="radio" name="email-template-default">
                <?= dgettext('luna', 'Standardadresse') ?>
            </label>
        </section>
        <?php $i = 0; foreach ($person->emails as $m) : ?>
            <section class="luna-email">
                <label>
                    <?= dgettext('luna', 'Adresse') ?>
                    <input type="email" name="email[<?= $i ?>][address]" size="50" value="<?= htmlReady($m->email) ?>">
                </label>
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
                <label>
                    <input type="radio" name="email-default" value="<?= $i ?>"<?= $m->default ? ' checked' : '' ?>>
                    <?= dgettext('luna', 'Standardadresse') ?>
                </label>
            </section>
        <?php $i++; endforeach ?>
        <a class="luna-email-add" href="">
            <?= Icon::create('add', 'clickable', array('title' => dgettext('luna', 'E-Mailadresse hinzufügen')))->asImg(24) ?>
        </a>
    </fieldset>
    <fieldset id="luna-phone">
        <legend>
            <?= dgettext('luna', 'Telefonnummer(n)') ?>
        </legend>
        <section id="luna-phone-template" class="luna-phone" data-number-of-phonenumbers="<?= count($person->phonenumbers) ?>">
            <label>
                <?= dgettext('luna', 'Nummer') ?>
                <input type="tel" name="phone-template-number" size="50" placeholder="<?= dgettext('luna', 'Geben Sie eine Telefonnummer ein') ?>">
            </label>
            <label>
                <?= dgettext('luna', 'Art') ?>
                <select name="phone-template-type">
                    <option value="private">
                        <?= dgettext('luna', 'Privat') ?>
                    </option>
                    <option value="mobile">
                        <?= dgettext('luna', 'Mobil') ?>
                    </option>
                    <option value="office">
                        <?= dgettext('luna', 'Geschäftlich') ?>
                    </option>
                </select>
            </label>
            <label>
                <input type="radio" name="phone-template-default">
                <?= dgettext('luna', 'Standardnummer') ?>
            </label>
        </section>
        <?php $i = 0; foreach ($person->phonenumbers as $p) : ?>
            <section class="luna-phone">
                <label>
                    <?= dgettext('luna', 'Nummer') ?>
                    <input type="tel" name="phone[<?= $i ?>][number]" size="50" value="<?= htmlReady($p->number) ?>">
                </label>
                <label>
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
                <label>
                    <input type="radio" name="phone-default" value="<?= $i ?>"<?= $p->default ? ' checked' : '' ?>>
                    <?= dgettext('luna', 'Standardnummer') ?>
                </label>
            </section>
        <?php $i++; endforeach ?>
        <a class="luna-phone-add" href="">
            <?= Icon::create('add', 'clickable', array('title' => dgettext('luna', 'Telefonnummer hinzufügen')))->asImg(24) ?>
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
                    <select name="company">
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
                    array('data-dialog' => '')) ?>
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
                <?php if ($person->skills) : ?>
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
                <textarea name="notes" cols="75" rows="10" class="add_toolbar"><?= htmlReady($person->notes) ?></textarea>
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
                <?= Icon::create('upload', 'clickable', array('title' => _('Datei hochladen'), 'class' => 'text-bottom')) ?>
                <?= _('Datei hochladen') ?>
            </label>
            <ul id="luna-newdocs"></ul>
        </section>
        <?php if (count($person->documents) > 0) : ?>
            <section>
                <h3><?= dgettext('luna', 'Vorhandene Dateien') ?></h3>
                <ul id="luna-userdocs">
                    <?php foreach ($person->documents as $d) : ?>
                        <li>
                            <input type="hidden" name="userdocs[]" value="<?= $d->id ?>">
                            <a href="<?= $controller->url_for('persons/download', $d->id) ?>">
                                <?= GetFileIcon(getFileExtension($d->filename))->asImg(['class' => "text-bottom"]) ?>
                                <?= htmlReady($d->name) ?>
                            </a>
                            <a href="<?= $controller->url_for('persons/delete_doc', $person->id, $d->id) ?>">
                                <?= Icon::create('trash', 'clickable', array('class' => 'text-bottom')) ?>
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
    <footer data-dialog-button>
        <?php foreach ($flash->flash as $key => $value) : ?>
            <?php if (is_array($value)) : ?>
                <?php foreach ($value as $entry) : ?>
                    <input type="hidden" name="company[<?= $key ?>][]" value="<?= $entry ?>">
                <?php endforeach ?>
            <?php else : ?>
                <input type="hidden" name="company[<?= $key ?>]" value="<?= $value ?>">
            <?php endif ?>
        <?php endforeach ?>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
