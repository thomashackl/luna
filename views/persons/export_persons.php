<form class="default" action="<?= $controller->url_for('persons/export_persons') ?>" method="post">
    <section class="contentbox">
        <article class="luna-export-datafields">
            <header>
                <h1>
                    <a href="<?= ContentBoxHelper::href('luna-export-datafields', array('contentbox_type' => 'news')) ?>">
                        <?= dgettext('luna', 'Zu exportierende Datenfelder') ?>
                    </a>
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
        </article>
    </section>
    <section>
        <label>
            <?= dgettext('luna', 'Dateiname') ?>
            <input type="text" name="filename" size="75" maxlength="255" value="luna-<?= date('Y-m-d-H-i') ?>">
        </label>
    </section>
    <footer data-dialog-button>
        <?php if ($flash['bulkusers']) : ?>
            <?php foreach ($flash['bulkusers'] as $u) : ?>
                <input type="hidden" name="users[]" value="<?= htmlReady($u) ?>">
            <?php endforeach ?>
        <?php endif ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Exportieren'), 'do_export') ?>
        <?= Studip\Button::create(dgettext('luna', 'Als Standard speichern'), 'default') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
