<?php if ($clients) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Mandanten') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('clients/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('category+add', 'clickable')->asImg() ?>
                </a>
            </span>
        </caption>
        <colgroup>
            <col>
            <col>
            <col width="100">
        </colgroup>
        <thead>
        <tr>
            <th><?= dgettext('luna', 'Name') ?></th>
            <th><?= dgettext('luna', 'Absender für E-Mails') ?></th>
            <th><?= dgettext('luna', 'Aktionen') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $c) : ?>
            <tr>
                <td><?= htmlReady($c->name) ?></td>
                <td><?= htmlReady($c->sender_address) ?></td>
                <td>
                    <?php $actionMenu = ActionMenu::get() ?>
                    <?php if ($currentClient->client_id != $c->id) : ?>
                        <?php $actionMenu->addLink(
                            $controller->url_for('clients/select', $c->id),
                            dgettext('luna', 'Mandant auswählen'),
                            Icon::create('accept', 'clickable',['title' =>
                                dgettext('luna', 'Mandant auswählen')])
                        ) ?>
                    <?php endif ?>
                    <?php if ($isRoot || ($c->beneficiaries &&
                            $c->beneficiaries->findOneBy('user_id', $GLOBALS['user']->id)->status == 'admin')) : ?>
                        <?php $actionMenu->addLink(
                            $controller->url_for('clients/permissions', $c->id),
                            dgettext('luna', 'Berechtigungen bearbeiten'),
                            Icon::create('community', 'clickable',['title' =>
                                dgettext('luna', 'Berechtigungen bearbeiten')]),
                            ['data-dialog' => 'size=auto']
                        ) ?>
                    <?php endif ?>
                    <?php if ($isRoot) : ?>
                        <?php $actionMenu->addLink(
                            $controller->url_for('clients/edit', $c->id),
                            dgettext('luna', 'Daten anzeigen/bearbeiten'),
                            Icon::create('edit', 'clickable',['title' =>
                                dgettext('luna', 'Daten anzeigen/bearbeiten')]),
                            ['data-dialog' => 'size=auto']
                        ) ?>
                        <?php $actionMenu->addLink(
                            $controller->url_for('clients/delete', $c->id),
                            dgettext('luna', 'Löschen'),
                            Icon::create('trash', 'clickable',['title' =>
                                dgettext('luna', 'Löschen')]),
                            ['data-confirm' =>  dgettext('luna', 'Wollen Sie den Mandanten wirklich löschen? Damit '.
                                'werden alle Personen, Unternehmen, Kompetenzen und Schlagwörter dieses Mandanten '.
                                'ebenfalls gelöscht!')]
                        ) ?>
                    <?php endif ?>
                    <?= $actionMenu->render() ?>
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
