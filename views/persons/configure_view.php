<form class="default" action="<?= $controller->url_for('persons/configure_view') ?>" method="post">
    <header>
        <h1>
            <?= dgettext('luna', 'Welche Spalten sollen angezeigt werden (der Name erscheint immer)?') ?>
        </h1>
    </header>
    <section id="luna-export-datafields">
        <?php foreach ($fields as $name => $field) : ?>
            <label>
                <input type="checkbox" name="fields[]" value="<?= htmlReady($name) ?>"<?= in_array($name, $selected) ? ' checked' : ''?>>
                <?= htmlReady($field['name']) ?>
            </label>
        <?php endforeach ?>
    </section>
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
