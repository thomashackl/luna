<form action="" method="post">
    <head>
        <h1><?= dgettext('luna', 'Daten filtern') ?></h1>
    </head>
    <section id="luna-data-filters">
        <?= Studip\LinkButton::create(dgettext('luna', 'Filter hinzufügen'),
            $controller->url_for('persons/get_filternames'), array('id' => 'luna-add-filter')) ?>
        <div id="luna-newfilter" class="hidden-js"
                data-filternames-url="<?= $controller->url_for('persons/get_filternames') ?>"
                data-filterdata-url="<?= $controller->url_for('persons/get_filterdata') ?>">
            <span id="luna-newfilter-name"></span>
            <span id="luna-newfilter-config"></span>
            <?= Studip\Button::create(_('Übernehmen'), 'apply', array('class' => 'hidden-js')) ?>
        </div>
        <div id="luna-applied-filters"<?= count($filters) == 0 ? ' class="hidden-js"' : '' ?> data-filter-count="<?= count($filters) ?>">
            <?php if (count($filters) > 0) : $i = 0; ?>
                <?php foreach ($filters as $filter) : ?>
                    <span class="luna-filter">
                        <?= htmlReady($allfilters[$filter['column']]['name']) ?>
                        <?= htmlReady($filter['compare']) ?>
                        <?= htmlReady($filter['value']) ?>
                        <input type="hidden" name="filters[<?= $i ?>][column]" value="<?= htmlReady($filter['column']) ?>">
                        <input type="hidden" name="filters[<?= $i ?>][compare]" value="<?= htmlReady($filter['compare']) ?>">
                        <input type="hidden" name="filters[<?= $i ?>][value]" value="<?= htmlReady($filter['value']) ?>">
                        <?= Icon::create('decline', 'clickable', array('class' => 'luna-remove-filter')) ?>
                    </span>
                <?php $i++; endforeach ?>
            <?php endif ?>
        </div>
    </section>
</form>
