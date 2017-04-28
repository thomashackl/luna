<?php if ($personcount > 0) : ?>
    <form action="<?= $controller->url_for('persons/bulk') ?>" method="post" data-dialog="size=auto">
        <table class="default">
            <caption>
                <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Personen') ?>
                <br>
                <span class="luna-smaller-text">
                    <?= sprintf(
                        dngettext('luna', '(%u Eintrag gefunden)', '(%u Einträge gefunden)', $personcount),
                        $personcount) ?>
                </span>
                <span class="actions">
                    <?php if ($hasWriteAccess) : ?>
                        <a href="<?= $controller->url_for('persons/edit') ?>" data-dialog="size=auto">
                            <?= Icon::create('person+add', 'clickable')->asImg() ?>
                        </a>
                    <?php endif ?>
                    <a href="<?= $controller->url_for('persons/configure_view') ?>" data-dialog="size=auto">
                        <?= Icon::create('checkbox-checked', 'clickable',
                            array('title' => dgettext('luna', 'Welche Spalten sollen angezeigt werden?')))->asImg() ?>
                    </a>
                </span>
            </caption>
            <colgroup>
                <col width="5">
                <col>
                <?php foreach ($columns as $c) : ?>
                    <col>
                <?php endforeach ?>
                <col width="25">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <input aria-label="<?= sprintf(_('Alle Personen auswählen')) ?>"
                               type="checkbox" name="all" value="1"
                               data-proxyfor=":checkbox[name='persons[]']">
                    </th>
                    <th><?= dgettext('luna', 'Name') ?></th>
                    <?php foreach ($columns as $c) : ?>
                        <th>
                            <?= $c != 'address' ? htmlReady($allfilters[$c]['name']) : dgettext('luna', 'Adresse') ?>
                        </th>
                    <?php endforeach ?>
                    <th><?= dgettext('luna', 'Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($persons as $p) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="persons[]" value="<?= $p->id ?>">
                    </td>
                    <td><?= htmlReady($p->getFullname()) ?></td>
                    <?php foreach ($columns as $c) : ?>
                        <td>
                            <?php if (!in_array($c, array('companies', 'skills', 'address', 'emails', 'phonenumbers'))) : ?>
                                <?= htmlReady($p->$c) ?>
                            <?php elseif ($c == 'address') : ?>
                                <?= nl2br(htmlReady($p->address)) ?>
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
                        <a href="<?= $controller->url_for('message/write/user', $p->id) ?>">
                            <?= Icon::create('mail', 'clickable')->asImg() ?>
                        </a>
                        <?php if ($hasWriteAccess) : ?>
                            <a href="<?= $controller->url_for('persons/edit', $p->id) ?>">
                                <?= Icon::create('edit', 'clickable')->asImg() ?>
                            </a>
                            <a href="<?= $controller->url_for('persons/delete', $p->id) ?>" data-confirm="<?=
                                    dgettext('luna', 'Wollen Sie die Person wirklich löschen?')?>">
                                <?= Icon::create('trash', 'clickable')->asImg() ?>
                            </a>
                        <?php else : ?>
                            <a href="<?= $controller->url_for('persons/info', $p->id) ?>" data-dialog>
                                <?= Icon::create('info', 'clickable')->asImg() ?>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?= count($columns) + 2 ?>">
                        <label>
                            <?= dgettext('luna', 'Aktion für ausgewählte Personen') ?>
                            <select name="bulkaction">
                                <option value="message">
                                    <?= dgettext('luna', 'Nachricht schreiben') ?>
                                </option>
                                <option value="csv">
                                    <?= dgettext('luna', 'Excel-Export') ?>
                                </option>
                                <option value="serialmail">
                                    <?= dgettext('luna', 'Export für Word-Serienbrief') ?>
                                </option>
                                <option value="vcard">
                                    <?= dgettext('luna', 'Kontakte exportieren') ?>
                                </option>
                            </select>
                        </label>
                        <br>
                        <i>
                            <?= dgettext('luna',
                            'Wenn Sie niemanden auswählen, wird die Aktion auf alle gefundenen Personen angewendet.') ?>
                        </i>
                    </td>
                    <td>
                        <?= Studip\Button::createAccept(dgettext('luna', 'Ausführen'), 'do-action') ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="<?= count($columns) + 1 ?>">
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
        </table>
    </form>
<?php else : ?>
    <h1>
        <?= dgettext('luna', 'Lehrbeauftragten- und Adressverwaltung') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Personen gefunden.') ?>
    </p>
<?php endif ?>
