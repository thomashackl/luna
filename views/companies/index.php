<?php if ($companies || $filter) : ?>
    <?= $this->render_partial('companies/_filter') ?>
<?php endif ?>
<div id="luna-data" data-type="companies" data-update-url="<?= $controller->url_for('companies/load_companies') ?>"></div>
