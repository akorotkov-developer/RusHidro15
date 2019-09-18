<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";


global $config;
global $sql;
$sql = new Sql();
$sql->connect();

kick_unauth();

//сброс кеша
clear_cache(0);


if ($_POST) {

    //robots.txt
    $res = file_put_contents($config['DOCUMENT_ROOT'] . 'robots.txt', stripslashes($_POST['DATA']['robots']));


    $stub_tpl = @file_get_contents($config['DOCUMENT_ROOT'] . 'templates/stub.html');
    if (strlen($stub_tpl) == 0) {
        $stub_tpl =
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="Content-type" content="text/html; charset=Windows-1251">
<title></title>

<link href="/styles/base.css" rel="stylesheet" type="text/css">

</head>

<body>
	
    <table style="width: 100%; height: 100%">
    	<tr>
        	<td style="vertical-align: middle">
            	<div style="width: 980px; margin: 0 auto; text-align: center; position: relative">
                	<img src="/img/logo.png" alt="" style="position: absolutel; top: 20px; left: 20px" />
				<!--#control::content#-->
                </div>
            </td>
        </tr>
    </table>

</body>
</html>';
    }

    if (intval($_POST['DATA']['siteblock'])) //если сайт заблокирован
    {
        $stub = str_replace('<!--#control::content#-->', stripslashes($_POST['data0']), $stub_tpl);
        file_put_contents($config['DOCUMENT_ROOT'] . 'siteblock.html', $stub);
        file_put_contents($config['DOCUMENT_ROOT'] . 'admin/templates/settings/siteblock_stub.txt', stripslashes($_POST['data0']));
    } else {
        @unlink($config['DOCUMENT_ROOT'] . 'siteblock.html');
        file_put_contents($config['DOCUMENT_ROOT'] . 'admin/templates/settings/siteblock_stub.txt', stripslashes($_POST['data0']));
    }


    if (isset($_FILES['txtfile'])) {
        $ext = strtolower(pathinfo($_FILES['txtfile']['name'], PATHINFO_EXTENSION));
        if ($ext == 'txt') {
            move_uploaded_file($_FILES['txtfile']['tmp_name'], $config['DOCUMENT_ROOT'] . '/' . $_FILES['txtfile']['name']);
        }

    }

    if (isset($_FILES['favicon'])) {
        move_uploaded_file($_FILES['favicon']['tmp_name'], $config['DOCUMENT_ROOT'] . '/favicon.ico');
    }


    header('Location: /admin/settings.php');
    die();
}


include "templates/includes/new_top.php";

$page = new stdClass();
$page->title = "Настройки сайта";

$page->DATA['robots'] = @file_get_contents($config['DOCUMENT_ROOT'] . 'robots.txt');
$page->DATA['siteblock_stub'] = @file_get_contents($config['DOCUMENT_ROOT'] . 'admin/templates/settings/siteblock_stub.txt');
if (is_file($config['DOCUMENT_ROOT'] . 'siteblock.html'))
    $page->DATA['siteblock'] = 1;
else
    $page->DATA['siteblock'] = 0;


$_GTC = clone($page);
include('templates/settings/settings.php');


?>

<?php
include "templates/includes/new_bottom.php";
?>
