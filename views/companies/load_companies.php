<?php if ($companycount > 0) : ?>
    <form action="<?= $controller->url_for('companies/bulk') ?>" method="post" data-dialog="size=auto">
        <table class="default">
            <caption>
                <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Unternehmen') ?>
                <br>
                <span class="luna-smaller-text">
                    <?= sprintf(
                        dngettext('luna', '(%u Eintrag gefunden)', '(%u Einträge gefunden)', $companycount),
                        $companycount) ?>
                </span>
                <?php if ($hasWriteAccess) : ?>
                    <span class="actions">
                        <a href="<?= $controller->url_for('companies/edit') ?>" data-dialog="size=auto">
                            <?= Icon::create('vcard+add', 'clickable')->asImg() ?>
                        </a>
                    </span>
                <?php endif ?>
            </caption>
            <colgroup>
                <col width="5">
                <col>
                <col>
                <col width="150">
                <col width="250">
                <col width="150">
                <col>
                <col>
                <col width="90">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <input aria-label="<?= sprintf(_('Alle Unternehmen auswählen')) ?>"
                               type="checkbox" name="all" value="1"
                               data-proxyfor=":checkbox[name='companies[]']">
                    </th>
                    <th><?= dgettext('luna', 'Name') ?></th>
                    <th><?= dgettext('luna', 'Adresse') ?></th>
                    <th><?= dgettext('luna', 'Ansprechpartner') ?></th>
                    <th><?= dgettext('luna', 'E-Mail') ?></th>
                    <th><?= dgettext('luna', 'Telefon') ?></th>
                    <th><?= dgettext('luna', 'Branche') ?></th>
                    <th><?= dgettext('luna', 'Schlagworte') ?></th>
                    <th><?= dgettext('luna', 'Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $c) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="companies[]" value="<?= $c->id ?>">
                        </td>
                        <td>
                            <a href="<?= $controller->url_for(($hasWriteAccess ? 'companies/edit' : 'companies/info'),
                                    $c->id) ?>">
                                <?= htmlReady($c->name) ?>
                            </a>
                        </td>
                        <td>
                            <?= nl2br(htmlReady($c->address)) ?>
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
                        <td><?= htmlReady($c->sector) ?></td>
                        <td>
                            <?php if (count($c->tags) > 0) : ?>
                                <?php foreach ($c->tags as $tag) : ?>
                                    <?= htmlReady($tag->name) ?>
                                    <br>
                                <?php endforeach ?>
                            <?php endif ?>
                        </td>
                        <td>
                            <?php $actionMenu = ActionMenu::get() ?>
                            <?php if (count($c->members) > 0) : ?>
                                <?php $actionMenu->addLink(
                                    $controller->url_for('companies/members', $c->id),
                                    dgettext('luna', 'Mitglieder anzeigen'),
                                    Icon::create('community', 'clickable',['title' =>
                                        dgettext('luna', 'Mitglieder anzeigen')]),
                                    ['data-dialog' => 'size=auto']
                                ) ?>
                                <?php $actionMenu->addLink(
                                    $controller->url_for('message/write/company', $c->id),
                                    dgettext('luna', 'Nachricht schreiben'),
                                    Icon::create('mail', 'clickable',['title' =>
                                        dgettext('luna', 'Nachricht schreiben')])
                                ) ?>
                            <?php endif ?>
                            <?php $actionMenu->addLink(
                                $controller->url_for('export/vcard', 'companies', $c->id),
                                dgettext('luna', 'Visitenkarte exportieren'),
                                Icon::create('vcard', 'clickable', ['title' =>
                                    dgettext('luna', 'Visitenkarte exportieren')])
                            ) ?>
                            <?php if ($hasWriteAccess) : ?>
                                <?php $actionMenu->addLink(
                                    $controller->url_for('companies/edit', $c->id),
                                    dgettext('luna', 'Daten anzeigen/bearbeiten'),
                                    Icon::create('edit', 'clickable', ['title' =>
                                        dgettext('luna', 'Daten anzeigen/bearbeiten')])
                                ) ?>
                                <?php $actionMenu->addLink(
                                    $controller->url_for('companies/delete', $c->id),
                                    dgettext('luna', 'Löschen'),
                                    Icon::create('trash', 'clickable', ['title' => dgettext('luna', 'Löschen')]),
                                    ['data-confirm' => dgettext('luna', 'Wollen Sie das Unternehmen wirklich löschen?')]
                                ) ?>
                            <?php else : ?>
                                <?php $actionMenu->addLink(
                                    $controller->url_for('companies/info', $c->id),
                                    dgettext('luna', 'Daten anzeigen'),
                                    Icon::create('info', 'clickable', ['title' => dgettext('luna', 'Daten anzeigen')]),
                                    ['data-dialog' => '']
                                ) ?>
                            <?php endif ?>
                            <?= $actionMenu->render() ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="8">
                    <label>
                        <?= dgettext('luna', 'Aktion für ausgewählte Unternehmen') ?>
                        <select name="bulkaction">
                            <?php if (count($c->members) > 0 || $c->contact_person) : ?>
                                <option value="message">
                                    <?= dgettext('luna', 'Nachricht schreiben') ?>
                                </option>
                            <?php endif ?>
                            <option value="export">
                                <?= dgettext('luna', 'Excel-Export') ?>
                            </option>
                            <option value="vcard">
		                        <?= dgettext('luna', 'Visitenkarten exportieren') ?>
                            </option>
                        </select>
                    </label>
                    <br>
                    <i>
                        <?= dgettext('luna',
                            'Wenn Sie niemanden auswählen, wird die Aktion auf alle gefundenen Unternehmen angewendet.') ?>
                    </i>
                </td>
                <td>
                    <?= Studip\Button::createAccept(dgettext('luna', 'Ausführen'), 'do-action') ?>
                </td>
            </tr>
                <tr>
                    <td colspan="7">
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
        </table>
    </form>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Unternehmen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Unternehmen gefunden.') ?>
    </p>
<?php endif ?>
