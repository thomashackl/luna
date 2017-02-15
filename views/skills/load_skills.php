<?php if (count($client->skills) > 0) : ?>
    <form action="<?= $controller->url_for('skills/bulkdelete') ?>" method="post" data-dialog="size=auto">
        <table class="default">
            <caption>
                <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Kompetenzen') ?>
                <br>
                <span class="luna-smaller-text">
                    <?= sprintf(
                        dngettext('luna', '(%u Eintrag gefunden)', '(%u Einträge gefunden)', count($client->skills)),
                        count($client->skills)) ?>
                </span>
                <?php if ($hasWriteAccess) : ?>
                    <span class="actions">
                        <a href="<?= $controller->url_for('skills/edit') ?>" data-dialog="size=auto">
                            <?= Icon::create('roles+add', 'clickable')->asImg() ?>
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
                        <input aria-label="<?= sprintf(_('Alle Kompetenzen auswählen')) ?>"
                               type="checkbox" name="all" value="1"
                               data-proxyfor=":checkbox[name='skills[]']">
                    </th>
                    <th colspan="2"><?= dgettext('luna', 'Name') ?></th>
                    <th><?= dgettext('luna', 'Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $s) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="skills[]" value="<?= $s->id ?>">
                        </td>
                        <td colspan="2"><?= htmlReady($s->name) ?></td>
                        <td>
                            <?= Icon::create('community', 'clickable',
                                ['title' => sprintf(
                                        dngettext('luna', '%u zugeordnete Person', '%u zugeordnete Personen',
                                            count($s->users)), count($s->users))])->asImg() ?>
                            <?php if ($hasWriteAccess) : ?>
                                <a href="<?= $controller->url_for('skills/edit', $s->id) ?>" data-dialog="size=auto"
                                        title="<?= dgettext('luna', 'Daten anzeigen/bearbeiten') ?>">
                                    <?= Icon::create('edit', 'clickable')->asImg() ?>
                                </a>
                                <a href="<?= $controller->url_for('skills/delete', $s->id) ?>" data-confirm="<?=
                                        dgettext('luna', 'Wollen Sie die Kompetenz wirklich löschen?')?>"
                                        title="<?= dgettext('luna', 'Löschen') ?>">
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
                                'Wenn Sie keinen Eintrag auswählen, wird die Aktion auf alle gefundenen Kompetenzen angewendet.') ?>
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
                                <a href="" onclick="return STUDIP.Luna.loadSkills(<?= $i-1 ?>)">
                                    <?= $i ?>
                                </a>
                            </div>
                            <?php if ($i < $pagecount) : ?>
                                |
                            <?php endif ?>
                        <?php endfor ?>
                    </td>
                    <td colspan="2" class="luna-entries-per-page" data-type="skills"
                            data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>">
                        <select name="entries-per-page"
                                data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>"
                                onchange="STUDIP.Luna.setEntriesPerPage('skills', this)">
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
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Kompetenzen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Kompetenzen gefunden.') ?>
    </p>
<?php endif ?>
