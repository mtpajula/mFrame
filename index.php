<?php

include_once 'm/mSkeleton.php';
include_once 'm/mWidget.php';
include_once 'm/mMenu.php';
include_once 'm/mForm.php';
include_once 'm/mDatabase.php';
include_once 'm/mDataManager.php';
include_once 'm/mUserManager.php';

$main = new mSkeleton('Mikon sivu');
$menu = new mMenu();

//~ $main->addCSS('index1.css');

$main->setHeaderElement('oma.css');

#$main->setDefaultTheme();
$main->setBootstrapTheme();

//~ $info = clone $main;
//~ $menu->add('home',$main);
//~ $menu->add('info',$info);

$w1 = new mWidget('Kirjautuminen');
$w1->showTitle();
$w1->setContent('Näkyy kaikilla sivuilla');

$db = new mDatabase('127.0.0.1', 'root', 'MysliPa55u', 'mframe2');
$users = new mUserManager($db);

$w1->add($users->generateLoginForm());
$main->add($w1);

$menu->addSkeleton($main);

$w2 = new mWidget('widget2');
$w2->showTitle();
$w2->setContent('sisältö 2');

$w3 = new mWidget('widget3');
$w3->showTitle();
$w3->setContent('sisältö 3');
$w3->setContent('sisältö 3 2');

$w4 = new mWidget('widget4', 'custom.php', 'moi');

$w3->add($w4);

$w6 = new mWidget('widget6');
$w6->showTitle();
$w3->add($w6);

$w7 = new mWidget('formtest');
$w7->showTitle();
$form = new mForm('post','testform');
$form->errorMessage = ' - Vaaditaan';
$form->addInput('text','Anna teksti','teksti');
$form->addInput('email','Anna sposti','sposti');
$form->addInput('password','Anna passu','passu');
$textarea = $form->addInput('textarea','Anna paljon tekstiä','area');
$textarea->sanitizeAs = 'full';

$radio = $form->addInput('radio','valitse tästä, oletko','radio');
$radio->addOption('Lapsi','lapsi');
$radio->addOption('Aikuinen','aikuinen');

$checkbox = $form->addInput('checkbox','valitse tästä, oletko','checkbox',false);
$checkbox->addOption('Koodari','c1');
$checkbox->addOption('Leipuri','c2');

$select = $form->addInput('select','Entäs tää','select');
$select->addOption('tylsää','s1');
$select->addOption('Hauskaa','s2');


$form->addDatabase($db);

$w7->add($form->generate());

$dManager = new mDataManager($form);
#$dManager->setCustomWidget('formdata.php', 'Formin datalista');

$menu->add('Koti',$w2);
$menu->add('Info',$w3);
$menu->add('Form',$w7);
$menu->add('FormData',$dManager->generate(true, true));
$menu->add('Rekisteröidy',$users->generateRegisterForm());
$menu->add('Hallitse',$users->generateUserAdmin());

$w5 = new mWidget('Menu');
$w5->showTitle();
$w5->add($menu->generate());

$menu->skeleton->add($w5);

$menu->manageShow();

?>
