<?php if ($type == 'persons') : ?>
<?php foreach ($entries as $e) : ?>
BEGIN:VCARD
VERSION:3.0
N:<?= $e->lastname ?>;<?= $e->firstname ?>;;<?= $e->title_front ?: '' ?>;<?= $e->title_rear ?: '' ?>

FN:<?= $e->getFullname('full')?>

<?php if (count($e->companies) > 0) : ?>
<?php foreach ($e->companies as $c) : ?>
ORG:<?= $c->name ?>
<?php endforeach ?>
<?php endif ?>
<?php if ($e->status) : ?>
TITLE:<?= $e->status ?>
<?php endif ?>
<?php if (count($e->phonenumbers) > 0) : ?>
<?php foreach ($e->phonenumbers as $p) : ?>
TEL;TYPE=<?= $p->type == 'office' ? 'WORK' :
    $p->type == 'private' ? 'HOME' :
    $p->type == 'mobile' ? 'CELL' : '' ?><?= $p->default ? ',PREF' : '' ?>,VOICE:<?= $p->number ?>

<?php endforeach ?>
<?php endif ?>
ADR:;;<?= $e->address ?>;<?= $e->city ?>;;<?= $e->zip ?>;<?= $e->country ?>

LABEL;PREF:<?= implode('\n', [$e->address, $e->zip . ' ' . $e->city, $e->country]) ?>
<?php if (count($e->emails) > 0) : ?>
<?php foreach ($e->emails as $e) : ?>
EMAIL;TYPE=<?= $e->type == 'office' ? 'WORK' :
    $e->type == 'private' ? 'HOME' :
    $e->type == 'mobile' ? 'CELL' : '' ?><?= $e->default ? ',PREF' : '' ?>,INTERNET:<?= $e->email ?>

<?php endforeach ?>
<?php endif ?>
REV:<?= date('Y-m-d') ?>T<?= date('H:I:s') ?>Z
END:VCARD
<?php endforeach ?>
<?php endif ?>
