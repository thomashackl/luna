<?php if ($companies) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Firmen') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('companies/edit') ?>" data-dialog="size=auto">
                    <?= Icon::create('vcard+add', 'clickable')->asImg() ?>
                </a>
            </span>
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
        <?php if ($pagecount > 1) : ?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?= dgettext('luna', 'Seite ') ?>
                        <?php for ($i = 1 ; $i <= $pagecount ; $i++) : ?>
                            <div class="luna-pagination<?= $i == $activepage ? ' active' : ''?>">
                                <a href="" onclick="return STUDIP.Luna.loadCompanies(<?= $i-1 ?>)">
                                    <?= $i ?>
                                </a>
                            </div>
                            <?php if ($i < $pagecount) : ?>
                                |
                            <?php endif ?>
                        <?php endfor ?>
                    </td>
                    <td colspan="2" class="luna-entries-per-page" data-type="companies"
                            data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>">
                        <select name="entries-per-page"
                                data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>"
                                onchange="STUDIP.Luna.setEntriesPerPage('companies', this)">
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
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Firmen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Firmen gefunden.') ?>
    </p>
<?php endif ?>
