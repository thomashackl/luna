<?php if ($companies) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Firmen') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('companies/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('vcard+add', 'clickable')->asImg() ?>
                </a>
            </>
        </caption>
        <colgroup>
            <col>
            <col>
            <col width="150">
            <col width="250">
            <col width="150">
            <col width="25">
        </colgroup>
        <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Adresse') ?></th>
                <th><?= dgettext('luna', 'Ansprechpartner') ?></th>
                <th><?= dgettext('luna', 'E-Mail') ?></th>
                <th><?= dgettext('luna', 'Telefon') ?></th>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $c) : ?>
                <tr>
                    <td><?= htmlReady($c->name) ?></td>
                    <td>
                        <?= htmlReady($c->street) ?>
                        <br>
                        <?= htmlReady($c->zip) ?> <?= htmlReady($c->city) ?>
                    </td>
                    <td><?= htmlReady($c->contact_person) ?></td>
                    <td><?= htmlReady($c->email) ?></td>
                    <td><?= htmlReady($c->phone) ?></td>
                    <td>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('companies/edit', $c->id) ?>" data-dialog="size=auto">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('companies/delete', $c->id) ?>" data-confirm="<?=
                                    dgettext('luna', 'Wollen Sie die Firma wirklich löschen?')?>">
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
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Firmen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Firmen gefunden.') ?>
    </p>
<?php endif ?>
