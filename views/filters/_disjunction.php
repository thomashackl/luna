<?php if ($count > 1) : ?>
    <span>
        <label>
            <input type="checkbox" name="disjunction" value="1" onclick="STUDIP.Luna.loadPersons()"<?= $disjunction ? ' checked' : '' ?>>
            <?= dgettext('luna', 'Nur eines der Filterfelder muss erfüllt sein') ?>
        </label>
    </span>
<?php endif;