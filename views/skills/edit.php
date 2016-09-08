<form class="default" action="<?= $controller->url_for('skills/save', $skill->id ?: null) ?>" method="post" data-dialog="reload-on-close">
    <header>
        <h1>
            <?= $skill->isNew() ?
                dgettext('luna', 'Neue Kompetenz anlegen') :
                dgettext('luna', 'Kompetenzdaten bearbeiten') ?>
        </h1>
    </header>
    <section>
        <label>
            <span class="required">
                <?= dgettext('luna', 'Name') ?>
            </span>
            <input type="text" name="name" size="75" maxlength="255"
                   value="<?= htmlReady($skill->name) ?>" required>
        </label>
    </section>
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
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('skills')) ?>
    </footer>
</form>
