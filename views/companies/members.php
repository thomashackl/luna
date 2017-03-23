<table class="default">
    <caption>
        <?= sprintf(dgettext('luna', 'Mitglieder des Unternehmens %s'), $company->name) ?>
    </caption>
    <colgroup>
        <col width="15">
        <col>
        <?php if ($hasWriteAccess) : ?>
            <col width="20">
        <?php endif ?>
    </colgroup>
    <tbody>
        <?php $i = 1; foreach ($company->members as $member) : ?>
            <tr>
                <td>
                    <?= $i ?>.
                </td>
                <td>
                    <a href="<?= $controller->url_for('persons/edit', $member->id) ?>" data-dialog="size=auto">
                        <?= htmlReady($member->getFullname('full')) ?>
                    </a>
                </td>
                <?php if ($hasWriteAccess) : ?>
                    <td data-remove-url="<?= $controller->url_for('companies/delete_member') ?>">
                        <a href="<?= $controller->url_for('companies/delete_member', $company->id, $member->id) ?>"
                                title="<?= dgettext('luna', 'Zuordnung löschen') ?>"
                                class="luna-member-remove"
                                data-company="<?= $company->id ?>" data-member="<?= $member->id ?>">
                            <?= Icon::create('trash', 'clickable')->asImg() ?>
                        </a>
                    </td>
                <?php endif ?>
            </tr>
        <?php $i++; endforeach ?>
    </tbody>
</table>
