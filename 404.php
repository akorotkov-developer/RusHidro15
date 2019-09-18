<?php

//функции кеширования
require "inc/var.php";
require_once "inc/libs/caching.php";
require_once "inc/libs/dtimer.php";

$dtimer = new dtimer();
$config['cache'] = 0;
require_once "includes.php";

session_start();
$sql = new Sql();
$sql->connect();

$control = new Control(false);
$control->template = 'error';
$control->Init();
$control->Make();

$sql->close();

if (in_array($_SERVER['REMOTE_ADDR'], $config['admin_IP'])) {
    $dtimer->tick("Done.");
    echo $dtimer->show();
}