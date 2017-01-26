<table class="default">
    <caption>
        <?= sprintf(dgettext('luna', 'Daten des Unternehmens %s'), $company->name) ?>
    </caption>
    <colgroup>
        <col width="25%">
        <col width="75%">
    </colgroup>
    <tbody>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Name') ?>
            </td>
            <td>
                <?= htmlReady($company->name) ?>
            </td>
        </tr>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Adresse') ?>
            </td>
            <td>
                <?= htmlReady($company->street) ?>
                <br>
                <?= htmlReady($company->zip) ?> <?= htmlReady($company->city) ?>
                <br>
                <?= htmlReady($company->country) ?: dgettext('luna', 'Deutschland') ?>
            </td>
        </tr>
        <?php if ($company->email) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'E-Mail') ?>
                </td>
                <td>
                    <?= htmlReady($company->email) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($company->phone) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Telefon') ?>
                </td>
                <td>
                    <?= htmlReady($company->phone) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($company->fax) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Fax') ?>
                </td>
                <td>
                    <?= htmlReady($company->fax) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($company->homepage) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Homepage') ?>
                </td>
                <td>
                    <?= htmlReady($company->homepage) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($company->contact_person) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Ansprechpartner') ?>
                </td>
                <td>
                    <a href="<?= $controller->url_for('persons/info', $company->contact_person) ?>" data-dialog="size=auto">
                        <?= htmlReady(LunaUser::find($company->contact_person)->getFullname('full')) ?>
                    </a>
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
