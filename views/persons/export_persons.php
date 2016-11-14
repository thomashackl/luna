<form class="default" action="<?= $controller->url_for('persons/export_persons') ?>" method="post">
    <section>
        <?php foreach ($fields as $name => $field) : ?>
            <label>
                <input type="checkbox" name="fields[]" value="<?= htmlReady($name) ?>">
                <?= htmlReady($field['name']) ?>
            </label>
        <?php endforeach ?>
    </section>
    <section>
        <label>
            <?= dgettext('luna', 'Dateiname') ?>
            <input type="text" name="filename" size="75" maxlength="255" value="luna-<?= date('Y-m-d-H-i') ?>">
        </label>
    </section>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(dgettext('luna', 'Exportieren'), 'do_export') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
