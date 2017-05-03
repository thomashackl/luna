<span class="luna-filter">
    <?= htmlReady($allfilters[$filter['column']]['name']) ?>
    <?= htmlReady($filter['compare']) ?>
    <?= htmlReady($allfilters[$filter['column']]['values'] ?
        $allfilters[$filter['column']]['values'][$allfilters[$filter['column']]['class']::getDisplayValue(trim($filter['value']),
            $allfilters[$filter['column']]['dbvalues'], $allfilters[$filter['column']]['is_id'])] :
        $allfilters[$filter['column']]['class']::getDisplayValue(trim($filter['value']),
            $allfilters[$filter['column']]['dbvalues'], $allfilters[$filter['column']]['is_id'])) ?>
    <input type="hidden" name="filters[<?= $i ?>][column]" value="<?= htmlReady($filter['column']) ?>">
    <input type="hidden" name="filters[<?= $i ?>][compare]" value="<?= htmlReady($filter['compare']) ?>">
    <input type="hidden" name="filters[<?= $i ?>][value]" value="<?= htmlReady($filter['value']) ?>">
    <?= Icon::create('decline', 'clickable', array('class' => 'luna-remove-filter', 'onclick' => 'STUDIP.Luna.removeFilter(this)')) ?>
</span>
