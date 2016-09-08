<?php if ($persons) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Personen') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('persons/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('person+add', 'clickable')->asImg() ?>
                </a>
            </>
        </caption>
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="25">
        </colgroup>
        <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Adresse') ?></th>
                <th><?= dgettext('luna', 'Firma') ?></th>
                <th><?= dgettext('luna', 'Kompetenzen') ?></th>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($persons as $p) : ?>
                <tr>
                    <td><?= htmlReady($p->getFullname()) ?></td>
                    <td>
                        <?= htmlReady($p->street) ?>
                        <br>
                        <?= htmlReady($p->zip) ?> <?= htmlReady($p->city) ?>
                    </td>
                    <td>
                        <?php if ($p->companies) : ?>
                            <?php foreach ($p->companies as $c) : ?>
                                <div><?= htmlReady($c->name) ?></div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (count($p->skills) > 0) : ?>
                            <ul>
                                <?php foreach ($p->skills as $skill) : ?>
                                    <li>
                                        <?= htmlReady($skill->name) ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('persons/edit', $p->id) ?>" data-dialog="size=auto">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('persons/delete', $p->id) ?>" data-confirm="<?=
                                    dgettext('luna', 'Wollen Sie die Person wirklich löschen?')?>">
                                <?= Icon::create('trash', 'clickable')->asImg() ?>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else : ?>
    <h1>
        <?= dgettext('luna', 'Lehrbeauftragten- und Adressverwaltung') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Personen gefunden.') ?>
    </p>
<?php endif ?>
