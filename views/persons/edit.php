<form class="default" action="<?= $controller->url_for('persons/save', $pid ?: null) ?>" method="post" enctype="multipart/form-data">
    <header>
        <h1>
            <?= $person->isNew() ?
                dgettext('luna', 'Neue Person anlegen') :
                dgettext('luna', 'Personendaten bearbeiten') ?>
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
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Kontaktdaten') ?>
        </legend>
        <section>
            <label>
                <?= dgettext('luna', 'Geschäftliche E-Mailadresse') ?>
                <input type="email" name="email_office" size="75" maxlength="255"
                       value="<?= htmlReady($person->email_office) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Private E-Mailadresse') ?>
                <input type="email" name="email_private" size="75" maxlength="255"
                       value="<?= htmlReady($person->email_private) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Geschäftliche Telefonnummer') ?>
                <input type="text" name="phone_office" size="75" maxlength="255"
                       value="<?= htmlReady($person->phone_office) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Private Telefonnummer') ?>
                <input type="text" name="phone_private" size="75" maxlength="255"
                       value="<?= htmlReady($person->phone_private) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Mobiltelefon') ?>
                <input type="text" name="phone_mobile" size="75" maxlength="255"
                       value="<?= htmlReady($person->phone_mobile) ?>">
            </label>
        </section>
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
                       value="<?= htmlReady($person->homepage) ?>">
            </label>
        </section>
    </fieldset>
    <?php if (count($skills) > 0) : ?>
        <fieldset>
            <legend>
                <?= dgettext('luna', 'Kompetenzen') ?>
            </legend>
            <section>
                <?foreach ($skills as $skill) : ?>
                    <label>
                        <input type="checkbox" name="skills[]" value="<?= $skill->id ?>"<?= $person->skills->find($skill->id) ? ' checked' : ''?>>
                        <?= htmlReady($skill->name) ?>
                    </label>
                <?php endforeach ?>
                <?= Studip\Button::create(dgettext('luna', 'Neue Kompetenz hinzufügen'), 'newskill',
                    array('data-dialog' => '')) ?>
            </section>
        </fieldset>
    <?php endif ?>
    <?php if (count($companies) > 0) : ?>
        <fieldset>
            <legend>
                <?= dgettext('luna', 'Firma') ?>
            </legend>
            <section>
                <label>
                    <?= dgettext('luna', 'Bestehende Firma auswählen') ?>
                    <select name="company">
                        <option value="">-- <?= dgettext('luna', 'Keine Firma auswählen') ?> --</option>
                        <?foreach ($companies as $company) : ?>
                            <option value="<?= $company->id ?>"<?= $person->companies->find($company->id) ? ' selected' : ''?>>
                                <?= htmlReady($company->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </label>
                <?= dgettext('luna', 'oder') ?>
                <?= Studip\Button::create(dgettext('luna', 'Neue Firma hinzufügen'), 'newcompany',
                    array('data-dialog' => '')) ?>
            </section>
        </fieldset>
    <?php endif ?>
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
                <?php if ($person->tags) : ?>
                    <?php foreach ($person->tags as $tag) : ?>
                        <div class="luna-tag" id="luna-tag-<?= $tag->name ?>">
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
                <?= dgettext('luna', 'Kurzlebenslauf') ?>
                <textarea name="vita" cols="75" rows="4" class="add_toolbar"><?= htmlReady($person->vita) ?></textarea>
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Besondere Qualifikationen') ?>
                <textarea name="qualifications" cols="75" rows="4" class="add_toolbar"><?= htmlReady($person->qualifications) ?></textarea>
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Notizen') ?>
                <textarea name="notes" cols="75" rows="4" class="add_toolbar"><?= htmlReady($person->notes) ?></textarea>
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
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
