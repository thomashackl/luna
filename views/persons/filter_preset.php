<form class="default" action="<?= $controller->url_for('persons/save_filter_preset') ?>" method="post">
    <section>
        <label>
            <?= dgettext('luna', 'Name der Suchvorlage') ?>
            <input type="text" name="name" size="75" maxlength="255" required>
        </label>
    </section>
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
