<?php if ($type == 'persons') : ?>
<?php foreach ($entries as $e) : ?>
BEGIN:VCARD
VERSION:3.0
KIND:individual
UID:urn:uuid:<?= md5($e->id) ?>-<?= strtolower($trans->transliterate($e->lastname)) ?>

N:<?= studip_utf8encode($e->lastname) ?>;<?= studip_utf8encode($e->firstname) ?>;;<?= studip_utf8encode($e->title_front) ?: '' ?>;<?= studip_utf8encode($e->title_rear) ?: '' ?>

FN:<?= studip_utf8encode($e->getFullname('full')) ?>

GENDER:<?= ($e->gender == 1 ? 'M' : ($e->gender == 2 ? 'F' : 'U' )) ?>

<?php if (count($e->companies) > 0) : ?>
<?php foreach ($e->companies as $c) : ?>
ORG:<?= studip_utf8encode($c->name) ?>

<?php endforeach ?>
<?php endif ?>
<?php if ($e->status) : ?>
TITLE:<?= studip_utf8encode($e->status) ?>

<?php endif ?>
<?php if (count($e->phonenumbers) > 0) : ?>
<?php foreach ($e->phonenumbers as $p) : ?>
TEL;TYPE=<?= ($p->type == 'office' ? 'WORK' :
    ($p->type == 'private' ? 'HOME' :
        ($p->type == 'mobile' ? 'CELL' : ''))) ?><?= $p->default ? ',PREF' : '' ?>,VOICE:<?= $p->number ?>

<?php endforeach ?>
<?php endif ?>
<?php if ($e->fax) : ?>
TEL;TYPE=FAX:<?= studip_utf8encode($e->fax) ?>

<?php endif ?>
ADR:;;<?= studip_utf8encode(preg_replace('/[\r\n]+/', "\r\n", $e->address)) ?>;<?= studip_utf8encode($e->city) ?>;;<?= studip_utf8encode($e->zip) ?>;<?= studip_utf8encode($e->country) ?>

LABEL;PREF:<?= implode("\r\n", [studip_utf8encode(preg_replace('/[\r\n]+/', "\r\n", $e->address)), studip_utf8encode($e->zip) . ' ' . studip_utf8encode($e->city), studip_utf8encode($e->country)]) ?>
<?php if (count($e->emails) > 0) : ?>
<?php foreach ($e->emails as $m) : ?>
EMAIL;TYPE=<?= ($m->type == 'office' ? 'WORK' :
    ($m->type == 'private' ? 'HOME' : '')) ?><?= $m->default ? ',PREF' : '' ?>,INTERNET:<?= studip_utf8encode($m->email) ?>

<?php endforeach ?>
<?php if ($e->homepage) : ?>
URL:<?= studip_utf8encode($e->homepage) ?>

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

N:<?= studip_utf8encode($e->name) ?>

FN:<?= studip_utf8encode($e->name) ?>

<?php if ($e->phone) : ?>
TEL;TYPE=WORK,PREF,VOICE:<?= $e->phone ?>

<?php endif ?>
<?php if ($e->fax) : ?>
TEL;TYPE=FAX:<?= studip_utf8encode($e->fax) ?>

<?php endif ?>
ADR:;;<?= studip_utf8encode(preg_replace('/[\r\n]+/', "\r\n", $e->address)) ?>;<?= studip_utf8encode($e->city) ?>;;<?= studip_utf8encode($e->zip) ?>;<?= studip_utf8encode($e->country) ?>

LABEL;PREF:<?= implode("\r\n", [studip_utf8encode(preg_replace('/[\r\n]+/', "\r\n", $e->address)), studip_utf8encode($e->zip) . ' ' . studip_utf8encode($e->city), studip_utf8encode($e->country)]) ?>

<?php if ($e->email) : ?>
EMAIL;TYPE=WORK,PREF,INTERNET:<?= studip_utf8encode($e->email) ?>

<?php endif ?>
<?php if ($e->homepage) : ?>
URL:<?= studip_utf8encode($e->homepage) ?>

<?php endif ?>
<?php if ($e->contact) : ?>
RELATION;TYPE=contact:urn:uuid:<?= md5($e->contact->id) ?>-<?= strtolower($trans->transliterate($e->contact->lastname)) ?>

<?php endif ?>
REV:<?= date('Y-m-d') ?>T<?= date('H:I:s') ?>Z
END:VCARD
<?php endforeach ?>
<?php endif;
