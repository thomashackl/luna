<?php if ($type == 'persons') : ?>
<?php foreach ($entries as $e) : ?>
BEGIN:VCARD
VERSION:3.0
KIND:individual
UID:urn:uuid:<?= md5($e->id) ?>-<?= strtolower($trans->transliterate($e->lastname)) ?>

N:<?= $e->lastname ?>;<?= $e->firstname ?>;;<?= $e->title_front ?: '' ?>;<?= $e->title_rear ?: '' ?>

FN:<?= $e->getFullname('full') ?>

GENDER:<?= ($e->gender == 1 ? 'M' : ($e->gender == 2 ? 'F' : 'U' )) ?>

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
TEL;TYPE=<?= ($p->type == 'office' ? 'WORK' :
    ($p->type == 'private' ? 'HOME' :
        ($p->type == 'mobile' ? 'CELL' : ''))) ?><?= $p->default ? ',PREF' : '' ?>,VOICE:<?= $p->number ?>

<?php endforeach ?>
<?php endif ?>
<?php if ($e->fax) : ?>
TEL;TYPE=FAX:<?= $e->fax ?>

<?php endif ?>
ADR:;;<?= preg_replace('/[\r\n]+/', "\r\n", $e->address) ?>;<?= $e->city ?>;;<?= $e->zip ?>;<?= $e->country ?>

LABEL;PREF:<?= implode("\r\n", [preg_replace('/[\r\n]+/', "\r\n", $e->address), $e->zip . ' ' . $e->city, $e->country]) ?>
<?php if (count($e->emails) > 0) : ?>
<?php foreach ($e->emails as $m) : ?>
EMAIL;TYPE=<?= ($m->type == 'office' ? 'WORK' :
    ($m->type == 'private' ? 'HOME' : '')) ?><?= $m->default ? ',PREF' : '' ?>,INTERNET:<?= $m->email ?>

<?php endforeach ?>
<?php if ($e->homepage) : ?>
URL:<?= $e->homepage ?>

<?php endif ?>
<?php endif ?>
REV:<?= date('Y-m-d') ?>T<?= date('H:I:s') ?>Z
END:VCARD
<?php endforeach ?>
<?php elseif ($type == 'companies') : ?>
<?php foreach ($entries as $e) : ?>
BEGIN:VCARD
VERSION:3.0
KIND:org
UID:urn:uuid:<?= md5($e->id) ?>-<?= strtolower($trans->transliterate(str_replace(' ', '-', $e->name))) ?>

N:<?= $e->name ?>

FN:<?= $e->name ?>

<?php if ($e->phone) : ?>
TEL;TYPE=WORK,PREF,VOICE:<?= $e->phone ?>

<?php endif ?>
<?php if ($e->fax) : ?>
TEL;TYPE=FAX:<?= $e->fax ?>

<?php endif ?>
ADR:;;<?= preg_replace('/[\r\n]+/', "\r\n", $e->address) ?>;<?= $e->city ?>;;<?= $e->zip ?>;<?= $e->country ?>

LABEL;PREF:<?= implode("\r\n", [preg_replace('/[\r\n]+/', "\r\n", $e->address), $e->zip . ' ' . $e->city, $e->country]) ?>

<?php if ($e->email) : ?>
EMAIL;TYPE=WORK,PREF,INTERNET:<?= $e->email ?>

<?php endif ?>
<?php if ($e->homepage) : ?>
URL:<?= $e->homepage ?>

<?php endif ?>
<?php if ($e->contact_persons != null && count($e->contact_persons) > 0) : ?>
<?php foreach ($e->contact_persons as $one) : ?>
RELATED;TYPE=contact:urn:uuid:<?= md5($one->person_id) ?>-<?= strtolower($trans->transliterate($one->user->lastname)) ?>
<?php endforeach ?>
<?php endif ?>

REV:<?= date('Y-m-d') ?>T<?= date('H:I:s') ?>Z
END:VCARD
<?php endforeach ?>
<?php endif;
