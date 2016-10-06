<form class="default" action="<?= $controller->url_for('clients/save', $client->id ?: null) ?>" method="post">
    <header>
        <h1>
            <?= $client->isNew() ?
                dgettext('luna', 'Neuen Mandanten anlegen') :
                dgettext('luna', 'Mandant bearbeiten') ?>
        </h1>
    </header>
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
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('clients')) ?>
    </footer>
</form>
