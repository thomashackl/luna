<form class="default" action="<?= $controller->url_for('clients/save_permissions', $client->id) ?>" method="post">
    <header>
        <h1>
            <?= sprintf(dgettext('luna', 'Berechtigungen für Mandant "%s" bearbeiten'), $client->name) ?>
        </h1>
    </header>
    <section>
        <label>
            <?= dgettext('luna', 'Berechtigte Person hinzufügen') ?>
            <?= $search ?>
        </label>
        <ul id="luna-beneficiaries" data-levels='<?= studip_json_encode($levels) ?>'>
            <?php foreach ($client->beneficiaries as $b) : ?>
                <li class="<?= $b->user_id ?>">
                    <?= htmlReady($b->user->getFullname()) ?>
                    <select name="users[<?= $b->user_id ?>]">
                        <?php foreach ($levels as $level) : ?>
                            <option value="<?= $level['value'] ?>"<?= $b->status == $level['value'] ? ' selected' : '' ?>>
                                <?= htmlReady($level['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <a href="<?= $controller->url_for('clients/delete', $c->id) ?>"
                       onclick="return STUDIP.Luna.removePerson(this)">
                        <?= Icon::create('trash', 'clickable')->asImg() ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
    <footer data-dialog-button>
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Speichern'), 'store') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('clients')) ?>
    </footer>
</form>
