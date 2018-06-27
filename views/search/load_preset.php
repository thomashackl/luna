<?php if (count($filters) > 0) : $i = 0; ?>
    <?php foreach ($filters as $filter) : ?>
        <?= $this->render_partial('filters/_singlefilter',
            array('allfilters' => $allfilters, 'filter' => $filter, 'i' => $i)) ?>
    <?php $i++; endforeach ?>
<?php endif ?>
