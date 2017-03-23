<?php if (count($tag->users) > 0 ) : ?>
    <table class="default">
        <caption>
            <?= dgettext('luna', 'Zugeordnete Personen') ?>
        </caption>
        <colgroup>
            <col width="15">
            <col>
        </colgroup>
        <thead>
            <tr>
                <th></th>
                <th><?= _('Name') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($tag->users as $u) : ?>
            <tr>
                <td><?= $i ?>.</td>
                <td>
                    <a href="<?= $controller->url_for($hasWriteAccess ? 'persons/edit' : 'persons/info', $u->id) ?>">
                        <?= htmlReady($u->getFullname('full_rev')) ?>
                    </a>
                </td>
            </tr>
            <?php $i++; endforeach ?>
        </tbody>
    </table>
<?php endif ?>
<?php if (count($tag->companies) > 0 ) : ?>
    <table class="default">
        <caption>
            <?= dgettext('luna', 'Zugeordnete Unternehmen') ?>
        </caption>
        <colgroup>
            <col width="15">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th></th>
            <th><?= _('Name') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($tag->companies as $c) : ?>
            <tr>
                <td><?= $i ?>.</td>
                <td>
                    <a href="<?= $controller->url_for($hasWriteAccess ? 'companies/edit' : 'companies/info', $c->id) ?>">
                        <?= htmlReady($c->name) ?>
                    </a>
                </td>
            </tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
<?php endif ?>
