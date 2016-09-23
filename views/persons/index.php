<?php if ($persons || $filter) : ?>
    <?= $this->render_partial('persons/_filter') ?>
<?php endif ?>
<div id="luna-data" data-update-url="<?= $controller->url_for('persons/load_data') ?>"></div>
