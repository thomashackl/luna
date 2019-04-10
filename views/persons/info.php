<table class="default">
    <caption>
        <?= sprintf(dgettext('luna', 'Daten von %s'), $person->getFullname('full')) ?>
    </caption>
    <colgroup>
        <col width="25%">
        <col width="75%">
    </colgroup>
    <tbody>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Status') ?>
            </td>
            <td>
                <?= htmlReady($person->status) ?>
            </td>
        </tr>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Name') ?>
            </td>
            <td>
                <?= htmlReady($person->getFullname('full')) ?>
            </td>
        </tr>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Geschlecht') ?>
            </td>
            <td>
                <?= $person->gender == 1 ?
                    dgettext('luna', 'männlich') :
                    ($person->gender == 2 ?
                        dgettext('luna', 'weiblich') :
                        dgettext('luna', 'unbekannt')) ?>
            </td>
        </tr>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Adresse') ?>
            </td>
            <td>
                <?= nl2br(htmlReady($person->address)) ?>
                <br>
                <?= htmlReady($person->zip) ?> <?= htmlReady($person->city) ?>
                <br>
                <?= htmlReady($person->country) ?: dgettext('luna', 'Deutschland') ?>
            </td>
        </tr>
        <?php if (count($person->emails) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'E-Mailadresse(n)') ?>
                </td>
                <td>
                    <?php foreach ($person->emails as $m) : ?>
                        <div<?= $m->default ? ' class="luna-defaultentry"' : '' ?>>
                            <?= $m->type == 'private' ?
                                dgettext('luna', 'Privat') :
                                ($m->type == 'office' ?
                                    dgettext('luna', 'Geschäftlich') :
                                    dgettext('luna', 'Sonstige')) ?>
                            <?= htmlReady($m->email) ?>
                        </div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($person->phonenumbers) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Telefonnummer(n)') ?>
                </td>
                <td>
                    <?php foreach ($person->phonenumbers as $p) : ?>
                        <div<?= $p->default ? ' class="luna-defaultentry"' : '' ?>>
                            <?= $p->type == 'private' ?
                                dgettext('luna', 'Privat') :
                                ($p->type == 'office' ?
                                    dgettext('luna', 'Geschäftlich') :
                                    ($p->type == 'mobile' ?
                                        dgettext('luna', 'Mobil') :
                                        dgettext('luna', 'Sonstige'))) ?>
                            <?= htmlReady($p->number) ?>
                        </div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($person->fax) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Fax') ?>
                </td>
                <td>
                    <?= htmlReady($person->fax) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($person->homepage) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Homepage') ?>
                </td>
                <td>
                    <?= htmlReady($person->homepage) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($person->companies) > 0) :  ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Unternehmen') ?>
                </td>
                <td>
                    <?= htmlReady($person->companies->first()->name) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($person->skills) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Kompetenzen') ?>
                </td>
                <td>
                    <?php foreach ($person->skills as $skill) : ?>
                        <div><?= htmlReady($skill->name) ?></div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($person->tags) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Schlagworte') ?>
                </td>
                <td>
                    <?php foreach ($person->tags as $tag) : ?>
                        <div><?= htmlReady($tag->name) ?></div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($person->notes) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Notizen') ?>
                </td>
                <td>
                    <?=formatReady($person->notes) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($documents) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Dateien') ?>
                </td>
                <td>
                    <?php foreach ($documents as $d) : ?>
                        <li>
                            <input type="hidden" name="userdocs[]" value="<?= $d->id ?>">
                            <a href="<?= $d->getDownloadURL() ?>" target="_blank">
                                <?= FileManager::getIconForMimeType($d->file->mime_type) ?>
                                <?= htmlReady($d->name) ?>
                            </a>
                            <a href="<?= $controller->url_for('persons/delete_doc', $person->id, $d->id) ?>">
                                <?= Icon::create('trash', 'clickable', ['class' => 'text-bottom']) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
