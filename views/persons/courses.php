<h1>
    <?= $plugin->getLongDisplayName() . ' - ' .
    sprintf(dgettext('luna', 'Veranstaltungen von %s'), $user->getFullname('full')) ?>
</h1>
<?php if (count($courses) > 0) : ?>
    <?php foreach ($courses as $semester => $semcourses) : ?>
        <table class="default">
            <caption>
                <?= htmlReady($semester) ?>
            </caption>
            <colgroup>
                <col width="25%">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th><?= dgettext('luna', 'Nummer') ?></th>
                    <th><?= dgettext('luna', 'Name') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($semcourses as $c) : ?>
                    <tr>
                        <td><?= htmlReady($c->veranstaltungsnummer) ?></td>
                        <td>
                            <a href="<?= URLHelper::getURL('dispatch.php/course/details/', array('cid' => $c->id)) ?>">
                                <?= htmlReady($c->name) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endforeach ?>
<?php else : ?>
    <p>
        <?= dgettext('luna', 'Es wurden keine Veranstaltungen gefunden.') ?>
    </p>
<?php endif ?>
