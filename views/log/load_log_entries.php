<?php if ($log) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Aktionsprotokoll') ?>
            <br>
            <span class="luna-smaller-text">
                <?= sprintf(
                    dngettext('luna', '(%u Eintrag gefunden)', '(%u Einträge gefunden)', $logcount),
                    $logcount) ?>
            </span>
        </caption>
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
        <tr>
            <th><?= dgettext('luna', 'Datum') ?></th>
            <th><?= dgettext('luna', 'Ausgeführt von') ?></th>
            <th><?= dgettext('luna', 'Aktion') ?></th>
            <th><?= dgettext('luna', 'Betroffener Eintrag') ?></th>
            <th><?= dgettext('luna', 'Weitere Informationen') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($log as $l) : ?>
            <tr>
                <td>
                    <?= date('d.m.Y H:i:s', $l->mkdate) ?>
                </td>
                <td>
                    <?php if ($l->user) : ?>
                        <?= htmlReady($l->user->getFullname('full')) ?> (<?= htmlReady($l->user->username) ?>)
                    <?php else : ?>
                        <?= dgettext('luna', 'unbekannt') ?>
                    <?php endif ?>
                </td>
                <td>
                    <?php
                        if ($l->action == 'create') {
                            switch($l->affected_type) {
                                case 'user':
                                    $text = dgettext('luna', 'Person wurde angelegt.');
                                    break;
                                case 'email':
                                    $text = dgettext('luna', 'E-Mailadresse wurde hinzugefügt.');
                                    break;
                                case 'phone':
                                    $text = dgettext('luna', 'Telefonnummer wurde hinzugefügt.');
                                    break;
                                case 'company':
                                    $text = dgettext('luna', 'Unternehmen wurde angelegt.');
                                    break;
                                case 'client':
                                    $text = dgettext('luna', 'Mandant wurde angelegt.');
                                    break;
                            }
                        } else if ($l->action == 'update') {
                            switch($l->affected_type) {
                                case 'user':
                                    $text = dgettext('luna', 'Personendaten wurden verändert.');
                                    break;
                                case 'email':
                                    $text = dgettext('luna', 'E-Mailadresse wurde verändert.');
                                    break;
                                case 'phone':
                                    $text = dgettext('luna', 'Telefonnummer wurde verändert.');
                                    break;
                                case 'company':
                                    $text = dgettext('luna', 'Unternehmensdaten wurden verändert.');
                                    break;
                                case 'client':
                                    $text = dgettext('luna', 'Mandantendaten wurden verändert.');
                                    break;
                            }
                        } else if ($l->action == 'delete') {
                            switch($l->affected_type) {
                                case 'user':
                                    $text = dgettext('luna', 'Person wurde gelöscht.');
                                    break;
                                case 'email':
                                    $text = dgettext('luna', 'E-Mailadresse wurde entfernt.');
                                    break;
                                case 'phone':
                                    $text = dgettext('luna', 'Telefonnummer wurde entfernt.');
                                    break;
                                case 'company':
                                    $text = dgettext('luna', 'Unternehmen wurde gelöscht.');
                                    break;
                                case 'client':
                                    $text = dgettext('luna', 'Mandant wurde gelöscht.');
                                    break;
                            }
                        } else if ($l->action == 'mail') {
                            switch($l->affected_type) {
                                case 'user':
                                    $text = dgettext('luna', 'Serienmail an %u Personen wurde verschickt.');
                                    break;
                                case 'company':
                                    $text = dgettext('luna', 'Serienmail an ein Unternehmen wurde verschickt.');
                                    break;
                            }
                        }
                    ?>
                    <?= $text ?>
                </td>
                <td>
                    <?php
                        switch ($l->affected_type) {
                            case 'user':
                                $entries = LunaUser::findMany($l->affected->getArrayCopy());
                                $link = 'persons/edit';
                                $name = 'getFullname';
                                $param = 'full';
                                break;
                            case 'email':
                            case 'phone':
                                $entries = LunaUser::find(reset($l->affected->getArrayCopy()));
                                $link = 'persons/edit';
                                $name = 'getFullname';
                                $param = 'full';
                                break;
                            case 'company':
                                $entries = LunaCompany::find(reset($l->affected->getArrayCopy()));
                                $link = 'companies/edit';
                                $name = 'name';
                                break;
                            case 'client':
                                $entries = LunaClient::find(reset($l->affected->getArrayCopy()));
                                $link = 'clients/edit';
                                $name = 'name';
                                break;
                        }
                    ?>
                    <?php if (is_array($entries) && !empty($entries)) : ?>
                        <?php foreach ($entries as $entry) : ?>
                            <a href="<?= $controller->url_for($link, $entry->id) ?>" data-dialog="size=auto">
                                <?php if (method_exists($entry, $name)) : ?>
                                    <?= htmlReady($entry->$name($param)) ?>
                                <?php else : ?>
                                    <?= htmlReady($entry->$name) ?>
                                <?php endif ?>
                            </a>
                            <br>
                        <?php endforeach ?>
                    <?php elseif ($entries) : ?>
                        <a href="<?= $controller->url_for($link, $entries->id) ?>">
                            <?php if (method_exists($entries, $name)) : ?>
                                <?= htmlReady($entries->$name($param)) ?>
                            <?php else : ?>
                                <?= htmlReady($entries->$name) ?>
                            <?php endif ?>
                        </a>
                    <?php else : ?>
                        <?= htmlReady($l->info) ?>
                    <?php endif ?>
                </td>
                <td>
                    <?= htmlReady($l->info) ?>
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
                            <a href="" onclick="return STUDIP.Luna.loadLogEntries(<?= $i-1 ?>)">
                                <?= $i ?>
                            </a>
                        </div>
                        <?php if ($i < $pagecount) : ?>
                            |
                        <?php endif ?>
                    <?php endfor ?>
                </td>
                <td colspan="1" class="luna-entries-per-page" data-type="log"
                    data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>">
                    <select name="entries-per-page"
                            data-set-url="<?= $controller->url_for('filters/set_entries_per_page') ?>"
                            onchange="STUDIP.Luna.setEntriesPerPage('log', this)">
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
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Aktionsprotokoll') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Einträge gefunden.') ?>
    </p>
<?php endif ?>
