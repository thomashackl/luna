<form action="" method="post">
    <header>
        <h1><?= dgettext('luna', 'Daten filtern') ?></h1>
    </header>
    <?php if (count($presets) > 0) : ?>
        <section>
            <label>
                <?= dgettext('luna', 'Eine vorhandene Suchvorlage laden') ?>
                <br>
                <select name="preset" id="luna-companyfilter-preset"
                        data-update-url="<?= $controller->url_for('filters/load_preset', 'companies') ?>">
                    <option value="">-- <?= dgettext('luna', 'bitte ausw�hlen') ?> --</option>
                    <?php foreach ($presets as $name => $filterdata) : ?>
                        <option value="<?= htmlReady($name) ?>"><?= htmlReady($name) ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </section>
        <?= dgettext('luna', 'oder') ?>
    <?php endif ?>
    <section id="luna-data-filters">
        <?= Studip\LinkButton::create(dgettext('luna', 'Filter hinzuf�gen'),
            $controller->url_for('filters/get_filternames', 'companies'), array('id' => 'luna-add-filter')) ?>
        <div id="luna-newfilter" class="hidden-js"
             data-filternames-url="<?= $controller->url_for('filters/get_filternames', 'companies') ?>"
             data-filterdata-url="<?= $controller->url_for('filters/get_filterdata', 'companies') ?>"
             data-pleasechoose="<?= dgettext('luna', 'bitte ausw�hlen') ?>">
            <span id="luna-newfilter-name"></span>
            <span id="luna-newfilter-config"></span>
            <?= Studip\Button::create(_('�bernehmen'), 'apply', array('class' => 'hidden-js')) ?>
        </div>
        <div id="luna-applied-filters"<?= count($filters) == 0 ? ' class="hidden-js"' : '' ?> data-filter-count="<?= count($filters) ?>">
            <?php if (count($filters) > 0) : $i = 0; ?>
                <?php foreach ($filters as $filter) : ?>
                    <?= $this->render_partial('companies/_singlefilter',
                        array('allfilters' => $allfilters, 'filter' => $filter, 'i' => $i)) ?>
                <?php $i++; endforeach ?>
            <?php endif ?>
        </div>
    </section>
    <?php if (count($filters) > 0) : ?>
        <section id="luna-save-filters">
            <?= Studip\LinkButton::create(dgettext('luna', 'Suchfilter speichern'),
                $controller->url_for('search/filter_preset'), array('data-dialog' => 'size=auto')) ?>
        </section>
    <?php endif ?>
</form>