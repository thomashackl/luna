<?php if ($persons) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Personen') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('persons/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('person+add', 'clickable')->asImg() ?>
                </a>
                <a href="<?= $controller->url_for('persons/configure_view') ?>" data-dialog="size=auto">
                    <?= Icon::create('checkbox-checked', 'clickable',
                        array('title' => dgettext('luna', 'Welche Spalten sollen angezeigt werden?')))->asImg() ?>
                </a>
            </span>
        </caption>
        <colgroup>
            <col>
            <?php foreach ($columns as $c) : ?>
                <col>
            <?php endforeach ?>
            <col width="25">
        </colgroup>
        <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <?php foreach ($columns as $c) : ?>
                    <th><?= $c != 'address' ? htmlReady($allfilters[$c]['name']) : dgettext('luna', 'Adresse') ?></th>
                <?php endforeach ?>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($persons as $p) : ?>
            <tr>
                <td><?= htmlReady($p->getFullname()) ?></td>
                <?php foreach ($columns as $c) : ?>
                    <td>
                        <?php if (!in_array($c, array('companies', 'skills', 'address', 'emails', 'phonenumbers'))) : ?>
                            <?= htmlReady($p->$c) ?>
                        <?php elseif ($c == 'address') : ?>
                            <?= htmlReady($p->street) ?>
                            <br>
                            <?= htmlReady($p->zip) ?> <?= htmlReady($p->city) ?>
                        <?php else : ?>
                            <?php foreach ($p->$c as $e) : ?>
                                <div><?= htmlReady($e->name) ?></div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
                <td>
                    <?php if ($hasWriteAccess) : ?>
                        <a href="<?= $controller->url_for('persons/edit', $p->id) ?>" data-dialog>
                            <?= Icon::create('info', 'clickable')->asImg() ?>
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
        <?php if ($pagecount > 1) : ?>
            <tfoot>
                <tr>
                    <td colspan="<?= count($columns) ?>">
                        <?= dgettext('luna', 'Seite ') ?>
                        <?php for ($i = 1 ; $i <= $pagecount ; $i++) : ?>
                            <div class="luna-pagination<?= $i == $activepage ? ' active' : ''?>">
                                <a href="" onclick="return STUDIP.Luna.loadPersons(<?= $i-1 ?>)">
                                    <?= $i ?>
                                </a>
                            </div>
                            <?php if ($i < $pagecount) : ?>
                                |
                            <?php endif ?>
                        <?php endfor ?>
                    </td>
                    <td colspan="2" class="luna-entries-per-page">
                        <select name="entries-per-page"
                                data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>"
                                onchange="STUDIP.Luna.setEntriesPerPage('persons', this)">
                            <option value="25"<?= $entries_per_page == 25 ? ' selected' : ''?>>25</option>
                            <option value="50"<?= $entries_per_page == 50 ? ' selected' : ''?>>50</option>
                            <option value="100"<?= $entries_per_page == 100 ? ' selected' : ''?>>100</option>
                            <option value="250"<?= $entries_per_page == 250 ? ' selected' : ''?>>250</option>
                        </select>
                        <?= dgettext('luna', 'Einträge pro Seite') ?>
                    </td>
                </tr>
            </tfoot>
        <?php endif ?>
    </table>
<?php else : ?>
    <h1>
        <?= dgettext('luna', 'Lehrbeauftragten- und Adressverwaltung') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Personen gefunden.') ?>
    </p>
<?php endif ?>
