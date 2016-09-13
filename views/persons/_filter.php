<form action="" method="post">
    <head>
        <h1><?= dgettext('luna', 'Daten filtern') ?></h1>
    </head>
    <section>
        <?= Studip\LinkButton::create(dgettext('luna', 'Filter hinzufügen'),
            $controller->url_for('persons/get_filternames'), array('id' => 'luna-add-filter')) ?>
    </section>
    <section id="luna-data-filters">
        <div id="luna-newfilter"
                data-filternames-url="<?= $controller->url_for('persons/get_filternames') ?>"
                data-filterdata-url="<?= $controller->url_for('persons/get_filterdata') ?>">
            <span id="luna-newfilter-name"></span>
            <span id="luna-newfilter-config"></span>
        </div>
    </section>
</form>
