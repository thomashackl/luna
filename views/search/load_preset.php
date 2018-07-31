<?php if (count($filters) > 0) : $i = 0; ?>
    <?php foreach ($filters['filters'] as $filter) : ?>
        <?= $this->render_partial('filters/_singlefilter',
            array('allfilters' => $allfilters, 'filter' => $filter, 'i' => $i)) ?>
    <?php $i++; endforeach ?>
    <?= $this->render_partial('filters/_disjunction',
        ['count' => count($filters['filters']), 'disjunction' => $filters['disjunction']]) ?>
<?php endif ?>
