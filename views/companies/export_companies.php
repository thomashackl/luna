<form class="default" action="<?= $controller->url_for('companies/export_companies') ?>" method="post">
    <section>
        <label>
            <?= dgettext('luna', 'Dateiname') ?>
            <input type="text" name="filename" size="75" maxlength="255" value="luna-<?= date('Y-m-d-H-i') ?>">
        </label>
    </section>
    <footer data-dialog-button>
        <?php if ($flash['bulkcompanies']) : ?>
            <?php foreach ($flash['bulkcompanies'] as $c) : ?>
                <input type="hidden" name="companies[]" value="<?= htmlReady($c) ?>">
            <?php endforeach ?>
        <?php endif ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Exportieren'), 'do_export') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('companies')) ?>
    </footer>
</form>
