<form action="" method="post">
    <header>
        <h1><?= dgettext('luna', 'Daten filtern') ?></h1>
    </header>
    <section>
        <label>
            <?= dgettext('luna', 'Wer hat die Aktion ausgeführt?') ?>
            <select name="filter[user_id]">
                <option value="">-- <?= dgettext('luna', 'alle') ?> --</option>
                <?php foreach ($users as $user) : ?>
                    <option value="<?= $user->id ?>"><?= $user->getFullname('full_rev') ?></option>
                <?php endforeach ?>
            </select>
        </label>
    </section>
    <section>
        <label>
            <?= dgettext('luna', 'Art der Aktion:') ?>
            <select name="filter[action]">
                <option value="">-- <?= dgettext('luna', 'alle') ?> --</option>
                <option value="CREATE"><?= dgettext('luna', 'Anlegen') ?></option>
                <option value="UPDATE"><?= dgettext('luna', 'Daten ändern') ?></option>
                <option value="DELETE"><?= dgettext('luna', 'Löschen') ?></option>
                <option value="MAIL"><?= dgettext('luna', 'E-Mail schreiben') ?></option>
            </select>
        </label>
    </section>
    <section>
        <label>
            <?= dgettext('luna', 'Worauf wurde die Aktion angewendet?') ?>
            <select name="filter[action_type]">
                <option value="">-- <?= dgettext('luna', 'alle') ?> --</option>
                <option value="user"><?= dgettext('luna', 'Person(en)') ?></option>
                <option value="company"><?= dgettext('luna', 'Unternehmen') ?></option>
                <option value="client"><?= dgettext('luna', 'Mandant') ?></option>
            </select>
        </label>
    </section>
    <section class="hidden-js">
        <label>
            <?= dgettext('luna', 'Konkreter Eintrag:') ?>
            <select name="filter[target]">
                <option value="">-- <?= dgettext('luna', 'alle') ?> --</option>
            </select>
        </label>
    </section>
    <footer>
        <?= Studip\Button::createAccept(dgettext('luna', 'Anwenden'), 'set_filter') ?>
    </footer>
</form>
