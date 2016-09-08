<?php if ($clients) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Mandanten') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('clients/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('category+add', 'clickable')->asImg() ?>
                </a>
            </>
        </caption>
        <colgroup>
            <col>
            <col width="100">
        </colgroup>
        <thead>
        <tr>
            <th><?= dgettext('luna', 'Name') ?></th>
            <th><?= dgettext('luna', 'Aktionen') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $c) : ?>
            <tr>
                <td><?= htmlReady($c->name) ?></td>
                <td>
                    <?php if ($current_client->client_id != $c->id) : ?>
                        <a href="<?= $controller->url_for('clients/select', $c->id) ?>">
                            <?= Icon::create('accept', 'clickable')->asImg() ?>
                        </a>
                    <?php endif ?>
                    <a href="<?= $controller->url_for('clients/permissions', $c->id) ?>" data-dialog="size=auto">
                        <?= Icon::create('community', 'clickable')->asImg() ?>
                    </a>
                    <a href="<?= $controller->url_for('clients/edit', $c->id) ?>" data-dialog="size=auto">
                        <?= Icon::create('edit', 'clickable')->asImg() ?>
                    </a>
                    <a href="<?= $controller->url_for('clients/delete', $c->id) ?>" data-confirm="<?=
                        dgettext('luna', 'Wollen Sie den Mandanten wirklich l�schen? Damit '.
                            'werden alle Personen, Firmen und Kompetenzen dieses Mandanten '.
                            'ebenfalls gel�scht!')?>">
                        <?= Icon::create('trash', 'clickable')->asImg() ?>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Mandanten') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Mandanten gefunden.') ?>
    </p>
<?php endif ?>
