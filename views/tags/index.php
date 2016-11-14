<?php if ($tags) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagw�rter') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('tags/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('tag+add', 'clickable')->asImg() ?>
                </a>
            </span>
        </caption>
        <colgroup>
            <col>
            <col width="25">
        </colgroup>
        <thead>
        <tr>
            <th><?= dgettext('luna', 'Schlagwort') ?></th>
            <th><?= dgettext('luna', 'Aktionen') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tags as $t) : ?>
            <tr>
                <td><?= htmlReady($t->name) ?></td>
                <td>
                    <?php if ($hasWriteAccess) : ?>
                        <a href="<?= $controller->url_for('tags/edit', $t->id) ?>" data-dialog="size=auto">
                            <?= Icon::create('edit', 'clickable')->asImg() ?>
                        </a>
                        <a href="<?= $controller->url_for('tags/delete', $t->id) ?>" data-confirm="<?=
                        dgettext('luna', 'Wollen Sie das Schlagwort wirklich l�schen?')?>">
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
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagw�rter') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Schlagw�rter gefunden.') ?>
    </p>
<?php endif ?>
