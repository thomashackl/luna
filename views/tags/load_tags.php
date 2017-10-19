<?php if (count($client->tags) > 0) : ?>
    <form action="<?= $controller->url_for('tags/bulkdelete') ?>" method="post" data-dialog="size=auto">
        <table class="default">
            <caption>
                <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagwörter') ?>
                <br>
                <span class="luna-smaller-text">
                    <?= sprintf(
                        dngettext('luna', '(%u Eintrag gefunden)', '(%u Einträge gefunden)', count($client->tags)),
                        count($client->tags)) ?>
                </span>
                <?php if ($hasWriteAccess) : ?>
                    <span class="actions">
                        <a href="<?= $controller->url_for('tags/edit') ?>" data-dialog="size=auto">
                            <?= Icon::create('tag+add', 'clickable')->asImg() ?>
                        </a>
                    </span>
                <?php endif ?>
            </caption>
            <colgroup>
                <col width="5">
                <col>
                <col>
                <col width="90">
            </colgroup>
            <thead>
            <tr>
                <th>
                    <input aria-label="<?= sprintf(_('Alle Schlagwörter auswählen')) ?>"
                           type="checkbox" name="all" value="1"
                           data-proxyfor=":checkbox[name='tags[]']">
                </th>
                <th colspan="2"><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tags as $t) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="tags[]" value="<?= $t->id ?>">
                    </td>
                    <td colspan="2"><?= htmlReady($t->name) ?></td>
                    <td class="actions">
                        <?php $actionMenu = ActionMenu::get() ?>
                        <?php if (count($t->users) > 0 || count($t->companies) > 0) : ?>
                            <?php $actionMenu->addLink(
                                $controller->url_for('tags/assigned_to', $t->id),
                                dgettext('luna', 'Zugeordnete Personen/Unternehmen anzeigen'),
                                Icon::create('community', 'clickable',['title' =>
                                    sprintf(
                                        dngettext('luna', '%u zugeordnete Person', '%u zugeordnete Personen',
                                            count($t->users)), count($t->users)) . '/' .
                                    sprintf(
                                        dngettext('luna', '%u zugeordnete Unternehmen', '%u zugeordnete Unternehmen',
                                            count($t->companies)), count($t->companies))]),
                                ['data-dialog' => '']
                            ) ?>
                        <?php endif ?>
                        <?php if ($hasWriteAccess) : ?>
                            <?php $actionMenu->addLink(
                                $controller->url_for('tags/edit', $t->id),
                                dgettext('luna', 'Daten anzeigen/bearbeiten'),
                                Icon::create('edit', 'clickable',['title' =>
                                    dgettext('luna', 'Daten anzeigen/bearbeiten')]),
                                ['data-dialog' => 'size=auto']
                            ) ?>
                            <?php $actionMenu->addLink(
                                $controller->url_for('tags/delete', $t->id),
                                dgettext('luna', 'Löschen'),
                                Icon::create('trash', 'clickable',['title' =>
                                    dgettext('luna', 'Löschen')]),
                                ['data-confirm' => dgettext('luna', 'Wollen Sie das Schlagwort wirklich löschen?')]
                            ) ?>
                        <?php endif ?>
                        <?= $actionMenu->render() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <i>
                        <?= dgettext('luna',
                            'Wenn Sie keinen Eintrag auswählen, wird die Aktion auf alle gefundenen Schlagwörter angewendet.') ?>
                    </i>
                </td>
                <td>
                    <?= Studip\Button::create(dgettext('luna', 'Löschen'), 'do-action') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?= dgettext('luna', 'Seite ') ?>
                    <?php for ($i = 1 ; $i <= $pagecount ; $i++) : ?>
                        <div class="luna-pagination<?= $i == $activepage ? ' active' : ''?>">
                            <a href="" onclick="return STUDIP.Luna.loadTags(<?= $i-1 ?>)">
                                <?= $i ?>
                            </a>
                        </div>
                        <?php if ($i < $pagecount) : ?>
                            |
                        <?php endif ?>
                    <?php endfor ?>
                </td>
                <td colspan="2" class="luna-entries-per-page" data-type="tags"
                    data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>">
                    <select name="entries-per-page"
                            data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>"
                            onchange="STUDIP.Luna.setEntriesPerPage('tags', this)">
                        <option value="25"<?= $entries_per_page == 25 ? ' selected' : ''?>>25</option>
                        <option value="50"<?= $entries_per_page == 50 ? ' selected' : ''?>>50</option>
                        <option value="100"<?= $entries_per_page == 100 ? ' selected' : ''?>>100</option>
                        <option value="250"<?= $entries_per_page == 250 ? ' selected' : ''?>>250</option>
                    </select>
                    <?= dgettext('luna', 'Einträge pro Seite') ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagwörter') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Schlagwörter gefunden.') ?>
    </p>
<?php endif ?>
