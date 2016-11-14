<?php if ($skills) : ?>
    <table class="default">
        <caption>
            <?= dgettext('luna', 'Lehrbeauftragten- und Adressverwaltung - Kompetenzen') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('skills/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('roles+add', 'clickable')->asImg() ?>
                </a>
            </span>
        </caption>
        <colgroup>
            <col>
            <col width="25">
        </colgroup>
        <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skills as $s) : ?>
                <tr>
                    <td><?= htmlReady($s->name) ?></td>
                    <td>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('skills/edit', $s->id) ?>" data-dialog="size=auto">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('skills/delete', $s->id) ?>" data-confirm="<?=
                                    dgettext('luna', 'Wollen Sie die Kompetenz wirklich löschen?')?>">
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
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Kompetenzen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Kompetenzen gefunden.') ?>
    </p>
<?php endif ?>
