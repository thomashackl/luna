<?php if (is_array($value)) : ?>
    <?php foreach ($value as $k => $v) : ?>
        <?= $this->render_partial('persons/_flashvalue', ['prefix' => $prefix . '[' . $key . ']', 'key' => $k, 'value' => $v]) ?>
    <?php endforeach ?>
<?php else : ?>
    <input type="hidden" name="<?= $prefix ?>[<?= $key ?>]" value="<?= $value ?>">
<?php endif;
