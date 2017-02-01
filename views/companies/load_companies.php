<?php if ($companies) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Unternehmen') ?>
            <br>
            <span class="luna-smaller-text">
                <?= sprintf(
                    dngettext('luna', '(%u Eintrag gefunden)', '(%u Eintr�ge gefunden)', $companycount),
                    $companycount) ?>
            </span>
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
            <col>
            <col width="90">
        </colgroup>
        <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Adresse') ?></th>
                <th><?= dgettext('luna', 'Ansprechpartner') ?></th>
                <th><?= dgettext('luna', 'E-Mail') ?></th>
                <th><?= dgettext('luna', 'Telefon') ?></th>
                <th><?= dgettext('luna', 'Schlagworte') ?></th>
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
                    <td>
                        <?php if ($c->contact) : ?>
                            <?= htmlReady($c->contact->getFullname('full')) ?>
                        <?php endif ?>
                    </td>
                    <td><?= htmlReady($c->email) ?></td>
                    <td><?= htmlReady($c->phone) ?></td>
                    <td>
                        <?php if (count($c->tags) > 0) : ?>
                            <?php foreach ($c->tags as $tag) : ?>
                                <?= htmlReady($tag->name) ?>
                                <br>
                            <?php endforeach ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (count($c->members) > 0) : ?>
                            <a href="<?= $controller->url_for('companies/members', $c->id) ?>" data-dialog="size=auto"
                                    title="<?= dgettext('luna', 'Mitglieder anzeigen') ?>">
                                <?= Icon::create('community', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('message/write/company', $c->id) ?>"
                                    title="<?= dgettext('luna', 'Nachricht schreiben') ?>">
                                <?= Icon::create('mail', 'clickable')->asImg() ?>
                            </a>
                        <?php endif ?>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('companies/edit', $c->id) ?>" data-dialog="size=auto"
                                    title="<?= dgettext('luna', 'Daten anzeigen/bearbeiten') ?>">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('companies/delete', $c->id) ?>" data-confirm="<?=
                                    dgettext('luna', 'Wollen Sie das Unternehmen wirklich l�schen?')?>"
                                    title="<?= dgettext('luna', 'L�schen') ?>">
                                <?= Icon::create('trash', 'clickable')->asImg() ?>
                            </a>
                        <?php else : ?>
                            <a href="<?= $controller->url_for('companies/info', $c->id) ?>" data-dialog
                                    title="<?= dgettext('luna', 'Daten anzeigen') ?>">
                                <?= Icon::create('info', 'clickable')->asImg() ?>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
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
                    <?= dgettext('luna', 'Eintr�ge pro Seite') ?>
                </td>
            </tr>
        </tfoot>
    </table>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Unternehmen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Unternehmen gefunden.') ?>
    </p>
<?php endif ?>
