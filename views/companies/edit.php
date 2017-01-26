<form class="default" action="<?= $controller->url_for('companies/save', $company->id ?: null) ?>" method="post" data-dialog="reload-on-close">
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
                <?= dgettext('luna', 'Stra�e') ?>
                <input type="text" name="street" size="75" maxlength="255" value="<?= htmlReady($company->street) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'PLZ') ?>
                <input type="text" name="zip" size="20" class="size-s" maxlength="20" value="<?= htmlReady($company->zip) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Ort') ?>
                <input type="text" name="city" size="75" maxlength="255"
                       value="<?= htmlReady($company->city) ?>">
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
            <?= Studip\Button::create(dgettext('luna', 'Neue Person hinzuf�gen'), 'newperson',
                array('data-dialog' => '')) ?>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'E-Mailadresse') ?>
                <input type="email" name="email" size="75" maxlength="255"
                       value="<?= htmlReady($company->email) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Telefonnummer') ?>
                <input type="text" name="phone" size="75" maxlength="255"
                       value="<?= htmlReady($company->phone) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Fax') ?>
                <input type="text" name="fax" size="75" maxlength="255"
                       value="<?= htmlReady($company->fax) ?>">
            </label>
        </section>
        <section>
            <label>
                <?= dgettext('luna', 'Homepage') ?>
                <input type="url" name="homepage" size="75" maxlength="255"
                       value="<?= htmlReady($company->homepage) ?>">
            </label>
        </section>
    </fieldset>
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
