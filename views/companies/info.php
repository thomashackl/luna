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
        <?php if ($company->sector) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Branche') ?>
                </td>
                <td>
                    <?= htmlReady($company->sector) ?>
                </td>
            </tr>
        <?php endif ?>
        <tr>
            <td class="luna-label">
                <?= dgettext('luna', 'Adresse') ?>
            </td>
            <td>
                <?= nl2br(htmlReady($company->address)) ?>
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
        <?php if (count($company->skills) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Kompetenzen') ?>
                </td>
                <td>
                    <?php foreach ($company->skills as $skills) : ?>
                        <div><?= htmlReady($skill->name) ?></div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($company->tags) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Schlagworte') ?>
                </td>
                <td>
                    <?php foreach ($company->tags as $tag) : ?>
                        <div><?= htmlReady($tag->name) ?></div>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (count($company->last_contacts) > 0) : ?>
            <tr>
                <td class="luna-label">
                    <?= dgettext('luna', 'Letzte Kontakte') ?>
                </td>
                <td>
                    <?php foreach ($company->last_contacts as $contact) : ?>
                        <?php $folder = LunaFolder::findTopFolder($contact->id)?>
                        <?php if($folder != NULL) : ?>
                            <div class="hgroup">
                                <div>
                                    <h4><?= date('d.m.Y', $contact->date) ?></h4>
                                    <h6><?= htmlReady($contact->user->getFullname()) ?>:
                                        <?= htmlReady(User::find($contact->contact)->getFullname()) ?></h6>
                                </div>
                                <div><?= htmlReady($contact->notes) ?></div>
                                <?php if (count($folder->getFiles()) > 0) : ?>
                                    <section id="luna-last-contact-doc-list">
                                        <ul id="luna-last_contact_docs">
                                            <?php foreach ($folder->getFiles() as $d) : ?>
                                                <li>
                                                    <input type="hidden" name="last_contact_docs[]" value="<?= $d->id ?>">
                                                    <a href="<?= $d->getDownloadURL() ?>" target="_blank">
                                                        <?= FileManager::getIconForMimeType($d->file->mime_type) ?>
                                                        <?= htmlReady($d->name) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </section>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
