<?php if ($log || $filter) : ?>
    <?= $this->render_partial('log/_filter') ?>
<?php endif ?>
<div id="luna-data" data-type="log" data-update-url="<?= $controller->url_for('log/load_log_entries') ?>"></div>
