<form class="default" action="<?= $controller->url_for('clients/save', $client->id ?: null) ?>" method="post">
    <header>
        <h1>
            <?= $client->isNew() ?
                dgettext('luna', 'Neuen Mandanten anlegen') :
                dgettext('luna', 'Mandant bearbeiten') ?>
        </h1>
    </header>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Allgemeine Einstellungen') ?>
        </legend>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Name') ?>
                </span>
                <input type="text" name="name" size="75" maxlength="255"
                       value="<?= htmlReady($client->name) ?>" required>
            </label>
        </section>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('luna', 'Absender für E-Mails') ?>
                </span>
                <input type="text" name="sender_address" size="75" maxlength="255"
                       value="<?= htmlReady($client->sender_address) ?>" required>
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('luna', 'Konfiguration') ?>
        </legend>
        <?php foreach ($client->config_entries as $entry) : ?>
            <section>
                <label>
                    <?php if ($entry->config->type === 'bool') : ?>
                        <input type="checkbox" name="configuration[<?= htmlReady($entry->key) ?>]"
                               value="1"<?= $entry->value == 1 ? ' checked' : ''?>>
                    <?php endif ?>
                    <?= htmlReady($entry->config->description) ?>
                </label>
                <input type="hidden" name="configuration[<?= htmlReady($entry->key) ?>]"
                       value="0">
            </section>
        <?php endforeach ?>
    </fieldset>
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('clients')) ?>
    </footer>
</form>
