<?php if ($persons || $filter) : ?>
    <?= $this->render_partial('persons/_filter') ?>
<?php endif ?>
<div id="luna-data" data-type="persons" data-update-url="<?= $controller->url_for('persons/load_persons') ?>"
     data-error-message="<?= dgettext('luna', 'Es ist ein Fehler aufgetreten.') ?>"></div>
