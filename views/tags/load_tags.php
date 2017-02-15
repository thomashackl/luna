<?php if (count($client->tags) > 0) : ?>
    <form action="<?= $controller->url_for('tags/bulkdelete') ?>" method="post" data-dialog="size=auto">
        <table class="default">
            <caption>
                <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagw�rter') ?>
                <br>
                <span class="luna-smaller-text">
                    <?= sprintf(
                        dngettext('luna', '(%u Eintrag gefunden)', '(%u Eintr�ge gefunden)', count($client->tags)),
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
                    <input aria-label="<?= sprintf(_('Alle Schlagw�rter ausw�hlen')) ?>"
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
                    <td>
                        <?= Icon::create('community', 'clickable',
                            ['title' => sprintf(
                                dngettext('luna', '%u zugeordnete Person', '%u zugeordnete Personen',
                                    count($t->users)), count($t->users)) . '/' .
                                sprintf(
                                    dngettext('luna', '%u zugeordnete Unternehmen', '%u zugeordnete Unternehmen',
                                        count($t->companies)), count($t->companies))
                            ])->asImg() ?>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('tags/edit', $t->id) ?>" data-dialog="size=auto"
                               title="<?= dgettext('luna', 'Daten anzeigen/bearbeiten') ?>">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('tags/delete', $t->id) ?>" data-confirm="<?=
                            dgettext('luna', 'Wollen Sie das Schlagwort wirklich l�schen?')?>"
                               title="<?= dgettext('luna', 'L�schen') ?>">
                                <?= Icon::create('trash', 'clickable')->asImg() ?>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <i>
                        <?= dgettext('luna',
                            'Wenn Sie keinen Eintrag ausw�hlen, wird die Aktion auf alle gefundenen Schlagw�rter angewendet.') ?>
                    </i>
                </td>
                <td>
                    <?= Studip\Button::create(dgettext('luna', 'L�schen'), 'do-action') ?>
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
                    <?= dgettext('luna', 'Eintr�ge pro Seite') ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Schlagw�rter') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Schlagw�rter gefunden.') ?>
    </p>
<?php endif ?>
