<?php if ($presets) : ?>
    <table class="default">
        <caption>
            <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Suchvorlagen') ?>
        </caption>
        <colgroup>
            <col>
            <col>
            <col width="25">
        </colgroup>
        <thead>
        <tr>
            <th><?= dgettext('luna', 'Name') ?></th>
            <th><?= dgettext('luna', 'Filter') ?></th>
            <th><?= dgettext('luna', 'Aktionen') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($presets as $name => $filters) : ?>
            <tr>
                <td><?= htmlReady($name) ?></td>
                <td>
                    <ul>
                        <?php foreach ($filters as $filter) : ?>
                            <li>
                                <?= htmlReady($allfilters[$filter['column']]['name']) ?>
                                <?= htmlReady($filter['compare']) ?>
                                <?= htmlReady(
                                    $allfilters[$filter['column']]['class']::getDisplayValue($filter['value'],
                                        $allfilters[$filter['column']]['dbvalues'])) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </td>
                <td>
                    <a href="<?= $controller->url_for('search/delete', $name) ?>" data-confirm="<?=
                            dgettext('luna', 'Wollen Sie die Suchvorlage wirklich löschen?')?>">
                        <?= Icon::create('trash', 'clickable')->asImg() ?>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php else : ?>
    <h1>
        <?= $plugin->getLongDisplayName() . ' - ' . dgettext('luna', 'Suchvorlagen') ?>
    </h1>
    <p>
        <?= dgettext('luna', 'Es wurden keine Suchvorlagen gefunden.') ?>
    </p>
<?php endif ?>
