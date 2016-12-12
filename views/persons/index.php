<?php if ($persons || $filter) : ?>
    <?= $this->render_partial('persons/_filter') ?>
<?php endif ?>
<form action="<?= $controller->url_for('persons/bulk') ?>" method="post" data-dialog>
    <div id="luna-data" data-type="persons" data-update-url="<?= $controller->url_for('persons/load_persons') ?>"></div>
</form>
