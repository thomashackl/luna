<form class="default" action="<?= $controller->url_for('message/send') ?>" method="post" enctype="multipart/form-data">
    <section>
        <label>
            <span class="required">
                <?= dgettext('luna', 'Absender') ?>
            </span>
            <input type="email" name="sender" value="<?= htmlReady($client->sender_address) ?>">
        </label>
        <label>
            <input type="checkbox" name="sendercopy" value="1" checked>
            <?= dgettext('luna', 'Kopie der Nachricht an diese Adresse senden') ?>
        </label>
    </section>
    <section>
        <span class="required">
            <?= dgettext('luna', 'Empfänger') ?>
        </span>
        <?php if (count($users) > 10) : ?>
            <br>
            <a id="luna-toggle-recipients"<?= count($users) <= 10 ? ' class="hidden-js"' : '' ?> href="">
                <span id="luna-hide-recipients">
                    <?= Icon::create('arr_1right', 'clickable') ?>
                </span>
                <span id="luna-show-recipients" style="display:none">
                    <?= Icon::create('arr_1down', 'clickable') ?>
                </span>
                <?= sprintf(dgettext('luna', '%u Empfänger anzeigen/ausblenden'), count($users)) ?>
            </a>
        <?php endif ?>
        <div id="luna-recipients"<?= count($users) > 10 ? ' class="hidden-js"' : '' ?>>
            <?php foreach ($users as $u) : ?>
                <span class="email">
                    <?= htmlReady($u->getDefaultEmail()) ?>
                    <input type="hidden" name="recipients[]" value="<?= $u->id ?>">
                </span>
            <?php endforeach ?>
        </div>
        <div>
            <label>
                <?= dgettext('luna', 'Weitere Empfänger in CC, durch Komma getrennt') ?>
                <br>
                <i>
                    <?= dgettext('luna', 'Bitte beachten Sie: Serienmails werden an diese Empfänger ohne Textersetzungen verschickt!') ?>
                </i>
                <textarea name="cc" cols="75" rows="2"></textarea>
            </label>
        </div>
    </section>
    <section>
        <label>
            <span class="required">
                <?= dgettext('luna', 'Betreff') ?>
            </span>
            <input type="text" name="subject" required placeholder="<?=
                dgettext('luna', 'Geben Sie hier den Betreff Ihrer E-Mail ein.') ?>">
        </label>
    </section>
    <section id="luna-message">
        <label id="luna-markers">
            <?= dgettext('luna', 'Feld für Serienmail einfügen') ?>
            <select name="markers">
                <option value="" data-description="">-- <?= dgettext('luna', 'bitte auswählen') ?> --</option>
                <?php foreach ($markers as $marker) : ?>
                    <option value="{<?= $marker['marker'] ?>}" data-description="<?= htmlReady(nl2br($marker['description'])) ?>"><?= htmlReady($marker['name']) ?></option>
                <?php endforeach ?>
            </select>
            <?= Studip\LinkButton::createAccept(_('Einsetzen'), '', array('id' => 'luna-add-marker', 'class' => 'hidden-js')) ?>
            <div id="luna-marker-description"></div>
        </label>
        <label>
            <span class="required">
                <?= dgettext('luna', 'Nachricht') ?>
            </span>
            <textarea name="message" cols="75" rows="20" class="add_toolbar" required placeholder="<?=
                dgettext('luna', 'Geben Sie hier den Inhalt Ihrer E-Mail ein.') ?>"></textarea>
        </label>
    </section>
    <section>
        <label class="luna-cursor-pointer">
            <?= dgettext('luna', 'Dateianhänge') ?>
            <br>
            <input type="file" name="docs[]" multiple>
            <?= Icon::create('upload', 'clickable', array('title' => _('Datei(en) hochladen'), 'class' => 'text-bottom')) ?>
            <?= _('Datei(en) hochladen') ?>
        </label>
        <ul id="luna-newdocs"></ul>
        <br><br>
    </section>
    <footer data-dialog-button>
        <?php if ($type && $id) : ?>
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="hidden" name="target_id" value="<?= $id ?>">
        <?php endif ?>
        <?= Studip\Button::createAccept(dgettext('luna', 'Absenden'), 'send') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('persons')) ?>
    </footer>
</form>
