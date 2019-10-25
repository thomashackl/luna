<li id="luna-contact-person-<?= $person->person_id ?>">
    <div class="col-2">
        <span id="<?= $person->person_id ?>-name">
            <?= htmlReady($person->user->getFullname()) ?>
        </span>
        <input type="hidden" name="contact_persons[<?= $person->person_id ?>][person_id]"
               value="<?= $person->person_id ?>">
    </div>
    <div class="col-3">
        <input type="text" id="<?= $person->person_id ?>-function"
               name="contact_persons[<?= $person->person_id ?>][function]"
               value="<?= $person->function ?>" maxlength="255"
               placeholder="<?= dgettext('luna', 'Funktion') ?>"
               aria-label="<?= dgettext('luna', 'Funktion') ?>">
    </div>
    <div class="col-1">
        <?php if ($person) : ?>
            <input type="hidden" name="contact_persons[<?= $person->person_id ?>][id]"
                   value="<?= $person->id ?>">
        <?php endif ?>
        <a href="" onclick="return STUDIP.Luna.removeContactPerson('<?= $person->person_id ?>')">
            <?= Icon::create('trash')->asImg(20) ?></a>
    </div>
</li>
