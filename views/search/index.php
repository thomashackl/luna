<h1>
    <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Suchvorlagen') ?>
</h1>
<?php foreach ($presets as $type => $data) : ?>
    <?php if (count($data['presets']) > 0) : ?>
        <table class="default">
            <caption>
                <?= sprintf(dgettext('luna', 'Suchvorlagen für %s'), htmlReady($data['name'])) ?>
            </caption>
            <colgroup>
                <col>
                <col>
                <col>
                <col width="25">
            </colgroup>
            <thead>
            <tr>
                <th><?= dgettext('luna', 'Name') ?></th>
                <th><?= dgettext('luna', 'Filter') ?></th>
                <th><?= dgettext('luna', 'Verknüpfung') ?></th>
                <th><?= dgettext('luna', 'Aktionen') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['presets'] as $name => $filters) : ?>
                <tr>
                    <td><?= htmlReady($name) ?></td>
                    <td>
                        <ul>
                            <?php foreach ($filters['filters'] as $filter) : ?>
                                <li>
                                    <?= htmlReady($data['allfilters'][$filter['column']]['name']) ?>
                                    <?= htmlReady($filter['compare']) ?>
                                    <?= htmlReady($data['allfilters'][$filter['column']]['class']::getDisplayValue(
                                            trim($filter['value']), $data['allfilters'][$filter['column']]['dbvalues'],
                                            $data['allfilters'][$filter['column']]['is_id'])) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </td>
                    <td>
                        <?php if ($filters['disjunction'] == 1) : ?>
                            <?= dgettext('luna', 'Nur eines der Filterfelder muss erfüllt sein') ?>
                        <?php else : ?>
                            <?= dgettext('luna', 'Alle Filterfelder müssen erfüllt sein') ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <a href="<?= $controller->url_for('search/delete_preset', $type, urlencode($name)) ?>" data-confirm="<?=
                                dgettext('luna', 'Wollen Sie die Suchvorlage wirklich löschen?')?>">
                            <?= Icon::create('trash', 'clickable')->asImg() ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>
            <?= sprintf(dgettext('luna', 'Es wurden keine Suchvorlagen für %s gefunden.'), $data['name']) ?>
        </p>
    <?php endif ?>
<?php endforeach ?>
