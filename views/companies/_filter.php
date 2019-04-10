<form action="" method="post">
    <header>
        <h1><?= dgettext('luna', 'Daten filtern') ?></h1>
    </header>
    <?php if (count($presets) > 0) : ?>
        <section>
            <label>
                <?= dgettext('luna', 'Eine vorhandene Suchvorlage laden') ?>
                <br>
                <select name="preset" id="luna-filter-presets"
                        data-update-url="<?= $controller->url_for('search/load_preset') ?>"
                        data-filter-type="companies">
                    <option value="">-- <?= dgettext('luna', 'bitte auswählen') ?> --</option>
                    <?php foreach ($presets as $name => $filterdata) : ?>
                        <option value="<?= htmlReady($name) ?>"><?= htmlReady($name) ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </section>
        <?= dgettext('luna', 'oder') ?>
    <?php endif ?>
    <section id="luna-data-filters">
        <div id="luna-textfilter">
            <input type="text" size="25" name="searchtext" value="<?= htmlReady($textfilter) ?>" data-type="companies"
                   placeholder="<?= dgettext('luna', 'Suchbegriff eingeben') ?>">
        </div>
        <?= Studip\LinkButton::create(dgettext('luna', 'Filter hinzufügen'),
            $controller->url_for('filters/get_filternames', 'companies'), ['id' => 'luna-add-filter']) ?>
        <div id="luna-newfilter" class="hidden-js"
             data-filternames-url="<?= $controller->url_for('filters/get_filternames', 'companies') ?>"
             data-filterdata-url="<?= $controller->url_for('filters/get_filterdata', 'companies') ?>"
             data-pleasechoose="<?= dgettext('luna', 'bitte auswählen') ?>">
            <span id="luna-newfilter-name"></span>
            <span id="luna-newfilter-config"></span>
            <?= Studip\Button::create(_('Übernehmen'), 'apply', ['class' => 'hidden-js']) ?>
        </div>
        <div id="luna-applied-filters"<?= count($filters['filters']) == 0 ? ' class="hidden-js"' : '' ?>
             data-filter-count="<?= count($filters['filters']) ?>">
            <?php if (count($filters['filters']) > 0) : $i = 0; ?>
                <?php foreach ($filters['filters'] as $filter) : ?>
                    <?= $this->render_partial('filters/_singlefilter',
                        ['allfilters' => $allfilters, 'filter' => $filter, 'i' => $i]) ?>
                <?php $i++; endforeach ?>
            <?php endif ?>
            <?= $this->render_partial('filters/_disjunction',
                ['count' => count($filters['filters']), 'disjunction' => $filters['disjunction']]) ?>
        </div>
    </section>
    <?php if (count($filters['filters']) > 0) : ?>
        <section id="luna-save-filters">
            <?= Studip\LinkButton::create(dgettext('luna', 'Suchfilter speichern'),
                $controller->url_for('search/filter_preset/companies'), ['data-dialog' => 'size=auto']) ?>
        </section>
    <?php endif ?>
</form>
